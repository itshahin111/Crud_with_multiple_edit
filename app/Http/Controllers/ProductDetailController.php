<?php
// app/Http/Controllers/ProductDetailController.php
namespace App\Http\Controllers;

use App\Models\ProductDetail;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function destroy($id)
    {
        $detail = ProductDetail::findOrFail($id);

        // Delete the image if exists
        if ($detail->image_path) {
            \Storage::disk('public')->delete($detail->image_path);
        }

        // Delete the detail record
        $detail->delete();

        return response()->json(['success' => 'Detail deleted successfully.']);
    }
}