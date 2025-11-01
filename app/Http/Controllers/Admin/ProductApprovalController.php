<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProductApprovalController extends Controller
{
    public function approve(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'productId' => 'required|integer',
                'adminEmail' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'Status' => 'Missing or invalid parameters',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $productId = $request->input('productId');
            $adminEmail = $request->input('adminEmail');
            $supervisorEmails = include base_path('services/AdminList.php');

            if (!in_array($adminEmail, $supervisorEmails)) {
                return response()->json(['Status' => 'Not authorized'], 403);
            }
            $productExists = DB::table('products')->where('productId', $productId)->exists();

            if (!$productExists) {
                return response()->json(['Status' => 'Product not found'], 404);
            }
            $updated = DB::table('products')
                ->where('productId', $productId)
                ->update(['isApproved' => 1]);
            if ($updated > 0) {
                $bidding = DB::table('biddings')
                    ->where('product_id', $productId)
                    ->first();

                if ($bidding) {
                    $productDateTime = Carbon::now('Asia/Damascus');
                    $biddingEndDate = $productDateTime->copy()->addDays((int) $bidding->biddingDays);
                    DB::table('biddings')
                        ->where('product_id', $productId)
                        ->update(['biddingEndDate' => $biddingEndDate]);
                }

                return response()->json(['Status' => 'The operation succeeded']);
            } else {
                return response()->json(['Status' => 'No changes were made']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'Status' => 'Error',
                'Message' => $e->getMessage(),
            ], 500);
        }
    }
}