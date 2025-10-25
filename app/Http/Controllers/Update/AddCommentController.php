<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class AddCommentController extends Controller
{
    public function updateAddComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'comments' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Status' => 'Missing or invalid parameters',
                'errors' => $validator->errors(),
            ], 400);
        }

        $product_id = $request->input('product_id');
        $commentsJson = $request->input('comments');

        $commentsDecoded = json_decode($commentsJson, true);
        if (!is_array($commentsDecoded)) {
            return response()->json([
                'Status' => 'Invalid JSON format',
            ], 400);
        }

        if (!empty($commentsDecoded)) {
            $commentsDecoded[0]['timestamp'] = now()->format('Y-m-d\TH:i:s.u');
        }

        $existing = DB::table('biddings')->where('product_id', $product_id)->first();

        if (!$existing) {
            return response()->json([
                'Status' => 'Product not found',
            ], 404);
        }

        $commentsFromServer = json_decode($existing->comments, true);
        if (!is_array($commentsFromServer)) {
            $commentsFromServer = [];
        }

        $newComments = array_merge($commentsDecoded, $commentsFromServer);
        $updatedCommentsJson = json_encode($newComments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $updated = DB::table('biddings')
            ->where('product_id', $product_id)
            ->update(['comments' => $updatedCommentsJson]);

        if ($updated > 0) {
            return response()->json([
                'Status' => 'The operation succeeded',
            ], 200);
        } else {
            return response()->json([
                'Status' => 'No changes were made',
            ], 200);
        }
    }
}
