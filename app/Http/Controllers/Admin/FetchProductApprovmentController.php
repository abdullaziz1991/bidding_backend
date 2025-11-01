<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FetchProductApprovmentController extends Controller
{
    public function fetchUnapprovedProducts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'adminEmail' => 'required|email',
                'search' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['Status' => 'Validation Error', 'errors' => $validator->errors()], 422);
            }

            $adminEmail = $request->input('adminEmail');
            $search = $request->input('search', '');
            $supervisorEmails = include base_path('services/AdminList.php');

            if (!in_array($adminEmail, $supervisorEmails)) {
                return response()->json(['Status' => 'Not authorized'], 403);
            }
            $now = Carbon::now('Asia/Damascus')->toIso8601String();

            $query = DB::table('products as p')
                ->join('users as u', 'u.id', '=', 'p.sellerId')
                ->leftJoin('biddings as b', 'p.productId', '=', 'b.product_id')
                ->select(
                    'p.*',
                    'u.userName',
                    'u.userImage',
                    'u.userRating',
                    'u.userRatingList',
                    'b.biddingEndDate',
                    'b.bidding_details'
                )
                ->where('p.isApproved', 0);

            if (!empty($search)) {
                $query->where('p.productTitle', 'like', "%$search%");
            }
            $products = $query->get();
            $mergedProducts = $products->map(function ($product) use ($now) {
                $productArray = (array)$product; // تحويل stdClass إلى مصفوفة
                $productArray['serverDateTime'] = $now; // إضافة وقت السيرفر
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
