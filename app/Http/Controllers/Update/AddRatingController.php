<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddRatingController extends Controller
{

    public function updateRating(Request $request)
    {
        try {
            // ✅ التحقق من صحة البيانات المدخلة
            $validator = Validator::make($request->all(), [
                'evaluatedId' => 'required|integer',
                'evaluatesId' => 'required|integer',
                'evaluationValue' => 'required|numeric|between:1,5'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'Status' => 'Missing or invalid parameters',
                    'errors' => $validator->errors()
                ], 422); // Unprocessable Entity
            }

            $evaluatedId = $request->input('evaluatedId');
            $evaluatesId = $request->input('evaluatesId');
            $evaluationValue = $request->input('evaluationValue');

            // ✅ جلب المستخدم
            $user = DB::table('users')->where('id', $evaluatedId)->first();

            if (!$user) {
                return response()->json(['Status' => "User not found"], 404);
            }

            $userRatingList = json_decode($user->userRatingList, true);
            if (!is_array($userRatingList)) {
                $userRatingList = [];
            }

            // ✅ التحقق من وجود تقييم سابق
            $found = false;
            foreach ($userRatingList as &$rating) {
                if ($rating['evaluatesId'] === $evaluatesId) {
                    if ($rating['evaluationValue'] == $evaluationValue) {
                        return response()->json([
                            'Status' => "No changes made",
                            'CurrentRating' => $evaluationValue
                        ], 200);
                    }
                    $rating['evaluationValue'] = $evaluationValue;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $userRatingList[] = [
                    'evaluatesId' => $evaluatesId,
                    'evaluationValue' => $evaluationValue
                ];
            }

            // ✅ حساب المتوسط الجديد
            $totalRating = array_sum(array_column($userRatingList, 'evaluationValue'));
            $ratingCount = count($userRatingList);
            $newAverageRating = $ratingCount > 0 ? round($totalRating / $ratingCount, 2) : 0;

            $updatedRatingList = json_encode($userRatingList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            DB::table('users')
                ->where('id', $evaluatedId)
                ->update([
                    'userRating' => $newAverageRating,
                    'userRatingList' => $updatedRatingList
                ]);

            return response()->json([
                'Status' => "The operation succeeded",
                'NewAverageRating' => $newAverageRating,
                'UpdatedRatingList' => $userRatingList
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Status' => "Error",
                'Message' => $e->getMessage()
            ], 500);
        }
    }
}
