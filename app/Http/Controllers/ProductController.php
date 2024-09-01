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
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'detail.*' => 'required|string',
            'image_path.*' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
            'id.*' => 'nullable|integer' // Validate that each detail ID is an integer
        ]);

        // Update product name
        $product->name = $request->name;
        $product->save();

        // Handle existing and new details
        for ($i = 0; $i < count($request->detail); $i++) {
            // Check if the detail ID exists for updating
            $detailId = $request->id[$i] ?? null; // Get the ID if it exists

            // Check if existing detail needs to be updated
            if ($detailId) {
                $existingDetail = ProductDetail::where('id', $detailId)
                    ->where('product_id', $product->id)
                    ->first();

                if ($existingDetail) {
                    // Update existing detail
                    $existingDetail->detail = $request->detail[$i];

                    // Handle image update if a new image is uploaded
                    if ($request->hasFile("image_path.$i") && $request->file("image_path.$i")->isValid()) {
                        // Delete the old image if it exists
                        if ($existingDetail->image_path) {
                            Storage::disk('public')->delete($existingDetail->image_path);
                        }

                        // Store the new image
                        $image = $request->file("image_path.$i");
                        $existingDetail->image_path = $image->store('images', 'public');
                    }

                    $existingDetail->save();
                }
            } else {
                // Create new detail if no existing detail ID is provided
                $detailData = [
                    'product_id' => $product->id,
                    'detail' => $request->detail[$i],
                ];

                // Handle image upload for new detail
                if ($request->hasFile("image_path.$i") && $request->file("image_path.$i")->isValid()) {
                    $image = $request->file("image_path.$i");
                    $detailData['image_path'] = $image->store('images', 'public');
                }

                // Create new ProductDetail
                ProductDetail::create($detailData);
            }
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