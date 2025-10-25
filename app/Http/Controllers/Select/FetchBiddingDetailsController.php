<?php

namespace App\Http\Controllers\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FetchBiddingDetailsController extends Controller
{
    public function fetchBiddingDetails(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Invalid product ID',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $productId = $request->input('product_id');

            // تنفيذ الاستعلام
            $bidding = DB::table('biddings')
                ->select('bidding_details', 'biddingDays', 'biddingEndDate', 'comments')
                ->where('product_id', $productId)
                ->limit(1)
                ->first();

            if (!$bidding) {
                return response()->json([
                    'Status' => 'No bidding data found'
                ], 404);
            }

            // إضافة الوقت الحالي
            // $bidding->serverDateTime = now()->format('Y-m-d\TH:i:s.u');
        //   $bidding->serverDateTime = Carbon::now('Asia/Damascus')->format('Y-m-d\TH:i:s.u');
        
         $bidding->serverDateTime = Carbon::now('Asia/Damascus')->toIso8601String();
        //   $bidding->serverDateTime = Carbon::now('Asia/Damascus')->format('Y-m-d\TH:i:s.uP');




            return response()->json([
                'Result' => $bidding
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Status' => 'Error',
                'Message' => $e->getMessage()
            ], 500);
        }
    }
}
