<?php
namespace App\Http\Controllers;

use DB;
use Storage;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('details')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'detail.*' => 'required|string',
                'image_path.*' => 'image|mimes:jpg,png,jpeg,gif|max:2048',
            ]);

            $product = new Product();
            $product->name = $request->name;
            $product->save();

            foreach ($request->detail as $index => $data) {
                $detailData = [
                    'product_id' => $product->id,
                    'detail' => $data,
                ];

                if (isset($request->image_path[$index]) && $request->image_path[$index]->isValid()) {
                    $image = $request->image_path[$index];
                    $imagePath = $image->store('images', 'public');
                    $detailData['image_path'] = $imagePath;
                }

                ProductDetail::create($detailData);
            }

            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'detail.*' => 'required|string',
            'image_path.*' => 'image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        $product->name = $request->name;
        $product->save();

        // Retrieve existing details
        $existingDetails = $product->details()->get();

        foreach ($request->detail as $index => $detail) {
            $detailData = [
                'product_id' => $product->id,
                'detail' => $detail,
            ];

            // Update existing detail or create a new one
            $existingDetail = $existingDetails[$index] ?? new ProductDetail();

            // Check if a new image is uploaded using hasFile()
            if ($request->hasFile("image_path.$index") && $request->file("image_path.$index")->isValid()) {
                // Delete old image if it exists
                if ($existingDetail->image_path) {
                    Storage::disk('public')->delete($existingDetail->image_path);
                }

                // Upload new image
                $image = $request->file("image_path.$index");
                $imagePath = $image->store('images', 'public');
                $detailData['image_path'] = $imagePath;
            } else {
                // Retain existing image if no new one is uploaded
                $detailData['image_path'] = $existingDetail->image_path;
            }

            // Save or update the detail
            $existingDetail->fill($detailData)->save();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->details()->each(function ($detail) {
            if ($detail->image_path) {
                Storage::disk('public')->delete($detail->image_path);
            }
            $detail->delete();
        });

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}