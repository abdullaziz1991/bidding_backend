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
            // التحقق من صحة البيانات
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

            // استقبال البيانات مع القيم الافتراضية
            $search = $request->input('search', '');
            $productCategory = $request->input('productCategory', 0);
            $offset = $request->input('offset');
            $id = $request->input('id');
            $sortBy = $request->input('sortBy', 'recently added');

            // بناء الاستعلام مع join على biddings
            $query = Product::select(
                
                // 'products.*',
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

            // فرز النتائج
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

            // جلب المنتجات
            $products = $query->offset($offset)->limit(10)->get();

            // وقت السيرفر الموحد
            $now = Carbon::now('Asia/Damascus')->toIso8601String();

            // دمج بيانات seller + إضافة الوقت
            $mergedProducts = $products->map(function ($product) use ($now) {
                $productArray = $product->toArray();

                if (isset($productArray['seller'])) {
                    $sellerData = $productArray['seller'];
                    unset($productArray['seller']); // إزالة الكائن الأصلي
                    $productArray = array_merge($productArray, $sellerData); // دمج بيانات البائع
                }

                // إضافة الوقت الحالي بتوقيت دمشق
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


// namespace App\Http\Controllers\Select;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
// use App\Models\Product;
// use Carbon\Carbon;

// class FetchProductsController extends Controller
// {
//     /**
//  * @OA\Get(
//  *     path="/api/fetch-Products",
//  *     summary="Fetch products with filters and pagination",
//  *     tags={"Products"},
//  *     @OA\Parameter(
//  *         name="search",
//  *         in="query",
//  *         required=false,
//  *         description="Search term for product title",
//  *         @OA\Schema(type="string")
//  *     ),
//  *     @OA\Parameter(
//  *         name="productCategory",
//  *         in="query",
//  *         required=false,
//  *         description="Category ID",
//  *         @OA\Schema(type="integer")
//  *     ),
//  *     @OA\Parameter(
//  *         name="offset",
//  *         in="query",
//  *         required=true,
//  *         description="Pagination offset",
//  *         @OA\Schema(type="integer")
//  *     ),
//  *     @OA\Parameter(
//  *         name="id",
//  *         in="query",
//  *         required=true,
//  *         description="User ID making the request",
//  *         @OA\Schema(type="integer")
//  *     ),
//  *     @OA\Parameter(
//  *         name="sortBy",
//  *         in="query",
//  *         required=false,
//  *         description="Sort option: recently added, time remaining, importance: high to low, importance: low to high",
//  *         @OA\Schema(type="string")
//  *     ),
//  *     @OA\Response(
//  *         response=200,
//  *         description="Successful operation",
//  *         @OA\JsonContent(
//  *             @OA\Property(property="Status", type="string"),
//  *             @OA\Property(property="Result", type="array", @OA\Items(type="object"))
//  *         )
//  *     ),
//  *     @OA\Response(
//  *         response=400,
//  *         description="Invalid parameters"
//  *     ),
//  *     @OA\Response(
//  *         response=500,
//  *         description="Server error"
//  *     )
//  * )
//  */

//     public function fetch(Request $request)
//     {


//         try {
//             // التحقق من صحة البيانات
//             $validator = Validator::make($request->all(), [
//                 'search' => 'nullable|string',
//                 'productCategory' => 'nullable|integer',
//                 'offset' => 'required|integer',
//                 'id' => 'required|integer',
//                 'sortBy' => 'nullable|string',
//             ]);

//             if ($validator->fails()) {
//                 return response()->json([
//                     'Status' => 'Missing or invalid parameters'
//                 ], 400);
//             }

//             // استقبال البيانات مع القيم الافتراضية
//             $search = $request->input('search', '');
//             $productCategory = $request->input('productCategory', 0);
//             $offset = $request->input('offset');
//             $id = $request->input('id');
//             $sortBy = $request->input('sortBy', 'recently added');

//             // بناء الاستعلام
//             $query = Product::select('products.*')
//                 ->with(['seller:id,email_verified_at,userName,userGender,userImage,userRating,userRatingList'])
//                 ->where('isApproved', 1)
//                 ->where('sellerId', '!=', $id);

//             if ($productCategory > 0) {
//                 $query->where('productCategory', $productCategory);
//             }

//             if (!empty($search)) {
//                 $query->where('productTitle', 'like', '%' . $search . '%');
//             }

//             // فرز النتائج
//             switch ($sortBy) {
//                 case 'recently added':
//                     $query->orderBy('productId', 'desc');
//                     break;
//                 case 'time remaining':
//                     $query->orderBy('productDateTime', 'asc');
//                     break;
//                 case 'importance: high to low':
//                     $query->orderBy('productLikes', 'desc');
//                     break;
//                 case 'importance: low to high':
//                     $query->orderBy('productLikes', 'asc');
//                     break;
//                 default:
//                     $query->orderBy('productId', 'desc');
//             }

//             // جلب المنتجات
//             $products = $query->offset($offset)->limit(10)->get();

//             // دمج بيانات seller داخل كل منتج
//             // $mergedProducts = $products->map(function ($product) {
//             //     $productArray = $product->toArray();
//             //     if (isset($productArray['seller'])) {
//             //         $sellerData = $productArray['seller'];
//             //         unset($productArray['seller']); // إزالة الكائن الأصلي
//             //         $productArray = array_merge($productArray, $sellerData); // دمج بيانات البائع
//             //     }
//             //     return $productArray;
//             // });
            
//           $now = Carbon::now('Asia/Damascus')->toIso8601String();
//           $mergedProducts = $products->map(function ($product) use ($now) {
//           $productArray = $product->toArray();

//              if (isset($productArray['seller'])) {
//                 $sellerData = $productArray['seller'];
//                 unset($productArray['seller']); // إزالة الكائن الأصلي
//                 $productArray = array_merge($productArray, $sellerData); // دمج بيانات البائع
//                   }
//                   // إضافة الوقت الحالي بتوقيت دمشق
//                   $productArray['serverDateTime'] = $now;
//                   return $productArray;
//               });
//             return response()->json([
//                 'Status' => 'The operation succeeded',
//                 'Result' => $mergedProducts,
//             ], 200);
            
//         } catch (\Exception $e) {
//             return response()->json([
//                 'Status' => 'Error',
//                 'Message' => $e->getMessage(),
//             ], 500);
//         }
//     }
// }
