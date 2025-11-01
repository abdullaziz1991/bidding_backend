<?php

namespace App\Http\Controllers\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeleteProductController extends Controller
{
       public function deleteProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ProductId' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Validation Failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $productId = $request->input('ProductId');

        $product = DB::table('products')->where('productId', $productId)->first();

        if (!$product) {
            return response()->json(['Status' => 'Product not found'], 404);
        }
        if ($product->productImage) {
            $images = json_decode($product->productImage, true);

            if (is_array($images)) {
                foreach ($images as $imageName) {
                    $fullPath = public_path('storage/productImages/' . basename($imageName));

                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
        }
        DB::beginTransaction();

        try {
            DB::table('biddings')->where('product_id', $productId)->delete();
            DB::table('products')->where('productId', $productId)->delete();

            DB::commit();

            return response()->json(['Status' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['Status' => 'Error', 'Message' => $e->getMessage()], 500);
        }
    }
}
