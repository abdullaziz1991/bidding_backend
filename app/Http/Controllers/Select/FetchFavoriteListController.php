<?php


namespace App\Http\Controllers\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class FetchFavoriteListController extends Controller
{
    
    public function fetchFavoriteList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'favoritesList' => 'required',
            'userId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Missing or invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        $personalListRaw = $request->input('favoritesList');
        $userId = $request->input('userId');
        $productIds = [];

        if (is_string($personalListRaw)) {
            $decoded = json_decode($personalListRaw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $productIds = array_filter($decoded, 'is_numeric');
            }
        } elseif (is_array($personalListRaw)) {
            $productIds = array_filter($personalListRaw, 'is_numeric');
        }

        if (empty($productIds)) {
            return response()->json([
                'Status' => 'Invalid or empty product list'
            ], 400);
        }
        $now = Carbon::now('Asia/Damascus')->toIso8601String();
        $products = Product::select('products.*', 'biddings.biddingEndDate', 'biddings.bidding_details')
            ->leftJoin('biddings', 'products.productId', '=', 'biddings.product_id')
            ->with(['seller:id,userName,userGender,userImage,userRating,userRatingList'])
            ->whereIn('products.productId', $productIds)
            ->get()
            ->map(function ($product) use ($now) {
                $productArray = $product->toArray();
                if (isset($productArray['seller'])) {
                    $sellerData = $productArray['seller'];
                    unset($productArray['seller']);
                    $productArray = array_merge($productArray, $sellerData);
                }
                $productArray['serverDateTime'] = $now;

                return $productArray;
            });
        $validIds = $products->pluck('productId')->toArray();
        User::where('id', $userId)->update([
            'favoritesList' => json_encode($validIds) // نخزنها كـ JSON
        ]);

        return response()->json([
            'Status' => 'The operation succeeded',
            'Result' => $products,
            'favoritesList' => $validIds
        ]);
    }
}
