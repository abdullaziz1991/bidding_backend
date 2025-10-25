<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
      public function updateComment(Request $request)
    {
        try {
            // استخراج البيانات من الطلب
            $productId = $request->input('product_id');
            $comments = $request->input('comments');

            if (!$productId || !$comments) {
                return response()->json(['Status' => 'Missing required parameters'], 400);
            }

            // التحقق من وجود المنتج
            $exists = DB::table('biddings')->where('product_id', $productId)->exists();

            if (!$exists) {
                return response()->json(['Status' => 'Product not found'], 404);
            }

            // تنفيذ التحديث باستخدام Query Builder
            $updated = DB::table('biddings')
                ->where('product_id', $productId)
                ->update(['comments' => $comments]);

            if ($updated) {
                return response()->json(['Status' => 'The operation succeeded']);
            } else {
                return response()->json(['Status' => 'No changes were made']);
            }

        } catch (\Exception $e) {
            return response()->json([
                'Status' => 'Error',
                'Message' => $e->getMessage()
            ], 500);
        }
    }
}
