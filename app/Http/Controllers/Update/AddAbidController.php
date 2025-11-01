<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Bidding;
use Carbon\Carbon;

class AddAbidController extends Controller
{
    public function updateAddAbid(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'bidding_details' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['Status' => 'Invalid input', 'errors' => $validator->errors()], 400);
        }

        $product_id = $request->input('product_id');
        $bidding_details = json_decode($request->input('bidding_details'), true);

        if (!is_array($bidding_details) || empty($bidding_details)) {
            return response()->json(['Status' => 'Invalid JSON format'], 400);
        }
        $bidding_details[0]['bid_time'] = Carbon::now('Asia/Damascus')->format('Y-m-d\TH:i:s.u');
        $bidding = Bidding::where('product_id', $product_id)->first();
        if (!$bidding) {
            return response()->json(['Status' => 'Product not found'], 404);
        }

        $existing_bids = json_decode($bidding->bidding_details, true);

        if (!is_array($existing_bids)) {
            $existing_bids = [];
        }
        if ((double)$bidding_details[0]['bid_amount'] > (int)($existing_bids[0]['bid_amount'] ?? 0)) {
            // دمج المزايدات الجديدة مع القديمة
            $merged_bids = array_merge($bidding_details, $existing_bids);
            $bidding->bidding_details = json_encode($merged_bids, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $bidding->save();
            $product = Product::find($product_id);
            if ($product) {
                $product->productPrice = (int)$bidding_details[0]['bid_amount'];
                $product->save();
            }
            return response()->json(['Status' => 'The operation succeeded'], 200);
        } else {
            return response()->json(['Status' => 'New bid must be higher than the current highest bid'], 200);
        }
    }
}
