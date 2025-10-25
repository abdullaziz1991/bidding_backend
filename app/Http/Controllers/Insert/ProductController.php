<?php

namespace App\Http\Controllers\Insert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Bidding;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Info(
 *     title="Bidding API",
 *     version="1.0",
 *     description="برنامج المزايدات"
 * )
 */

class ProductController extends Controller
{
        /**
     * @OA\Post(
     *     path="/api/product",
     *     summary="إنشاء منتج جديد مع مزايدة",
     *     description="إرسال بيانات المنتج والمزايدة وتخزينها",
     *     operationId="storeProduct",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary")
     *                 ),
     *                 @OA\Property(property="data", type="string", description="بيانات JSON مشفرة على شكل string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تمت العملية بنجاح"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من البيانات"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في السيرفر"
     *     )
     * )
     */

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();


            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|max:2048',
                // |mimes:jpeg,png,jpg,gif,webp
            ]);
            $jsonData = json_decode($request->input('data'), true);


            $validator = Validator::make($jsonData, [
                'sellerId' => 'required|integer',
                // 'sellerNumber' => 'required|string',
                'productCategory' => 'required|integer',
                'productTitle' => 'required|string',
                'productDetails' => 'required|string',
                // 'productImage' => 'required|string',
                // 'productImageNumber' => 'required|integer',
                'productPrice' => 'required|numeric',
                'productStatus' => 'required|string',
                'productStatusRatio' => 'required|numeric',
                'productAmount' => 'required|numeric',
                'productCurrency' => 'required|string',
                'productCountry' => 'required|string',
                'productCity' => 'required|string',
                'bidDetails' => 'required|string',
                'biddingDays' => 'required|integer',
                'personalList' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json(['Status' => 'Validation Error', 'errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            // 

            // foreach ($request->file('images') as $image) {
            //     // حفظ الصورة في مجلد "productImages" داخل القرص "public"
            //     $path = $image->store('productImages', 'public'); // الناتج: productImages/abc123.jpg
            //     // $uploadedPaths2[] = asset('storage/' . str_replace('public/', '', $path));
            //     // استخراج فقط اسم الملف مع المجلد
            //     $uploadedPaths[] = $path; // فقط productImages/abc123.jpg
            // }
            $uploadedPaths = [];

            foreach ($request->file('images') as $image) {
                // حفظ الصورة في مجلد "productImages" داخل القرص "public"
                $path = $image->store('productImages', 'public'); // مثل: productImages/abc123.jpg

                // استخراج فقط اسم الصورة (abc123.jpg) بدون المجلد
                $uploadedPaths[] = basename($path);
            }

            $productDateTime = Carbon::now('Asia/Damascus');
            //  $productDateTime =Carbon::now('Asia/Damascus')->addHours(3);
            $biddingEndDate = $productDateTime->copy()->addDays((int) $data['biddingDays']);

            $product = Product::create([
                'sellerId' => $data['sellerId'],
                // 'sellerNumber' => $data['sellerNumber'],
                'productCategory' => $data['productCategory'],
                'productTitle' => $data['productTitle'],
                'productDetails' => $data['productDetails'],
                // 'productImage' => $data['productImage'],
                'productImage' =>  json_encode($uploadedPaths),
                // 'productImageNumber' => $data['productImageNumber'],
                'productPrice' => $data['productPrice'],
                // 'productDateTime' => $productDateTime,
                'productStatus' => $data['productStatus'],
                'productStatusRatio' => $data['productStatusRatio'],
                'productAmount' => $data['productAmount'],
                'productCurrency' => $data['productCurrency'],
                'productCountry' => $data['productCountry'],
                'productCity' => $data['productCity'],
            ]);
            
            $decodedBids = json_decode($data['bidDetails'], true);
            $orderedBids = [];

            foreach ($decodedBids as $bid) {
             $orderedBids[] = [
                'bidder_id'     => $bid['bidder_id'] ?? '',
                'country_flag'  => $bid['country_flag'] ?? '',
               'bid_amount'    => $bid['bid_amount'] ?? 0,
                 'bid_time'      => $bid['bid_time'] ?? now()->toIso8601String(),
                   ];
                  }
            
            

            Bidding::create([
                'product_id' => $product->productId,
                'productOwnerId' => $data['sellerId'],
                // 'bidding_details' => $data['bidDetails'],
                'bidding_details' => json_encode($orderedBids, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                'biddingDays' => $data['biddingDays'],
                'biddingEndDate' => $biddingEndDate,
            ]);

            // $sellerId = $data['sellerId'];
            $personalList = $data['personalList'];
            // إضافة المنتج إلى بداية القائمة
            array_unshift($personalList,$product->productId);

            // تحديث المستخدم
            DB::table('users')
                ->where('id',  $data['sellerId'])
                ->update(['personalList' => json_encode($personalList)]);
            DB::commit();

            return response()->json([
                'Status' => 'The operation succeeded',
                'productId' =>  $product->productId,
                'uploadedImges' =>  $uploadedPaths,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Product store error: " . $e->getMessage());
            return response()->json([
                'Status' => 'Error',
                'Message' => $e->getMessage()
            ], 500);
        }
    }

}
