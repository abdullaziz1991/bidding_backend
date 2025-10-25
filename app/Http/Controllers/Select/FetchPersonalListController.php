<?php

namespace App\Http\Controllers\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class FetchPersonalListController extends Controller
{
    public function fetchPersonalList(Request $request)
    {
        // التحقق من المعطيات
        $validator = Validator::make($request->all(), [
            'personalList' => 'required',
            'userId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Missing or invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        $personalListRaw = $request->input('personalList');
        $userId = $request->input('userId');
        $productIds = [];

        // فك الترميز إذا كانت String (JSON)
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

        // وقت السيرفر موحد
        $now = Carbon::now('Asia/Damascus')->toIso8601String();

        // جلب المنتجات مع join على جدول biddings
        $products = Product::select('products.*', 'biddings.biddingEndDate', 'biddings.bidding_details')
            ->leftJoin('biddings', 'products.productId', '=', 'biddings.product_id')
            ->with(['seller:id,userName,userGender,userImage,userRating,userRatingList'])
            ->whereIn('products.productId', $productIds)
            ->get()
            ->map(function ($product) use ($now) {
                $productArray = $product->toArray();

                // دمج بيانات البائع
                if (isset($productArray['seller'])) {
                    $sellerData = $productArray['seller'];
                    unset($productArray['seller']);
                    $productArray = array_merge($productArray, $sellerData);
                }

                // إضافة الوقت الحالي
                $productArray['serverDateTime'] = $now;

                return $productArray;
            });

        // ids اللي فعليًا موجودة بالـ DB
        $validIds = $products->pluck('productId')->toArray();

        // تحديث عمود personalList في users
        User::where('id', $userId)->update([
            'personalList' => json_encode($validIds) // نخزنها كـ JSON
        ]);

        return response()->json([
            'Status' => 'The operation succeeded',
            'Result' => $products,
            'personalList' => $validIds
        ]);
    }
}


