<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\User; // ✅ أضفت هذا السطر

class IncreaseLikesCountController extends Controller
{
    public function updateIncreaseLikesCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId'      => 'required|integer|exists:products,productId',
            // 'productLikes'   => 'required|integer',
            'productLikes' => 'required|integer|min:0',
            'id'         => 'required|integer|exists:users,id',
            'FavoritesList'  => 'required|string', // لأنه جايك JSON نص
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Missing or invalid parameters',
                'errors' => $validator->errors(),
            ], 400);
        }

        $productId      = $request->input('productId');
        $productLikes   = $request->input('productLikes');
        $id         = $request->input('id');
        $favoritesList  = $request->input('FavoritesList'); // جاي JSON نص

        // نحول النص JSON إلى مصفوفة
        $favoritesArray = json_decode($favoritesList, true);

        if (!is_array($favoritesArray)) {
            return response()->json([
                'Status' => 'Invalid FavoritesList format',
            ], 400);
        }

        // نعيد ترميزها بشكل نظيف قبل التخزين
        $favoritesListJson = json_encode($favoritesArray);

        try {
            $product = Product::where('productId', $productId)->first();
            $user    = User::where('id', $id)->first();

            if (!$product) {
                return response()->json(['Status' => 'Product not found'], 404);
            }
            if (!$user) {
                return response()->json(['Status' => 'User not found'], 404);
            }

            $product->productLikes = $productLikes;
            $user->favoritesList   = $favoritesListJson;

            $productChanged   = $product->isDirty('productLikes');
            $favoritesChanged = $user->isDirty('favoritesList');

            if ($productChanged) {
                $product->save();
            }
            if ($favoritesChanged) {
                $user->save();
            }

            if ($productChanged || $favoritesChanged) {
                return response()->json(['Status' => 'The operation succeeded']);
            } else {
                return response()->json(['Status' => 'No changes were made']);
            }

        } catch (\Exception $e) {
            return response()->json([
                'Status' => 'Error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}



