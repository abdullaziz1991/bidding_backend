<?php

namespace App\Http\Controllers\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Carbon\Carbon;

class FetchProductsController extends Controller
{
    public function fetch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string',
                'productCategory' => 'nullable|integer',
                'offset' => 'required|integer',
                'id' => 'required|integer',
                'sortBy' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'Status' => 'Missing or invalid parameters'
                ], 400);
            }
            $search = $request->input('search', '');
            $productCategory = $request->input('productCategory', 0);
            $offset = $request->input('offset');
            $id = $request->input('id');
            $sortBy = $request->input('sortBy', 'recently added');
            $query = Product::select(
    
              'products.productId',
        'products.sellerId',
        'products.productCategory',
        'products.productTitle',
        'products.productDetails',
        'products.productImage',
        'products.productPrice',
        'products.productAdvertisement',
        'products.productCountry',
        'products.productCity',
        'products.productLikes',
        'products.productStatus',
        'products.productStatusRatio',
        'products.productAmount',
        'products.productCurrency',
        'products.isApproved',
        'biddings.biddingEndDate',
        'biddings.bidding_details',
           
            'biddings.biddingEndDate', 'biddings.bidding_details')
                ->join('biddings', 'products.productId', '=', 'biddings.product_id')
                ->with(['seller:id,email_verified_at,userName,userGender,userImage,userRating,userRatingList'])
                ->where('isApproved', 1)
                ->where('sellerId', '!=', $id);

            if ($productCategory > 0) {
                $query->where('productCategory', $productCategory);
            }

            if (!empty($search)) {
                $query->where('productTitle', 'like', '%' . $search . '%');
            }
            switch ($sortBy) {
                case 'recently added':
                    $query->orderBy('productId', 'desc');
                    break;
                case 'time remaining':
                    $query->orderBy('productDateTime', 'asc');
                    break;
                case 'importance: high to low':
                    $query->orderBy('productLikes', 'desc');
                    break;
                case 'importance: low to high':
                    $query->orderBy('productLikes', 'asc');
                    break;
                default:
                    $query->orderBy('productId', 'desc');
            }
            $products = $query->offset($offset)->limit(10)->get();
            $now = Carbon::now('Asia/Damascus')->toIso8601String();
            $mergedProducts = $products->map(function ($product) use ($now) {
                $productArray = $product->toArray();

                if (isset($productArray['seller'])) {
                    $sellerData = $productArray['seller'];
                    unset($productArray['seller']); // إزالة الكائن الأصلي
                    $productArray = array_merge($productArray, $sellerData); // دمج بيانات البائع
                }
                $productArray['serverDateTime'] = $now;
                return $productArray;
            });

            return response()->json([
                'Status' => 'The operation succeeded',
                'Result' => $mergedProducts,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'Status' => 'Error',
                'Message' => $e->getMessage(),
            ], 500);
        }
    }
}