<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function updateProfile(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'images' => 'nullable|array',
                'images.*' => 'image|max:2048',
            ]);
            $jsonData = json_decode($request->input('data'), true);
            $validator = Validator::make($jsonData, [
                'id' => 'required|integer',
                'userName' => 'required|string',
                'userNumber' => 'required|string',
                'userGender' => 'required|string',
                'userEmail' => 'required|string',
                'oldImage' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'Status' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }
            $data = $validator->validated();
            $uploadedImage = null;
            if ($request->hasFile('images')) {
                $image = $request->file('images')[0]; // أول صورة فقط
                $path = $image->store('personalImages', 'public');
                $uploadedImage = basename($path);
            }
            $updateFields = [
                'userName' => $data['userName'],
                'userNumber' => $data['userNumber'],
                'userGender' => $data['userGender'],
                'userEmail' => $data['userEmail'],
            ];
            if ($uploadedImage) {
                $updateFields['userImage'] = $uploadedImage;
            }

            $affected = DB::table('users')
                ->where('id', $data['id'])
                ->update($updateFields);
            if ($uploadedImage && !in_array($data['oldImage'], ['Male_Image.jpg', 'Female_Image.jpg'])) {
                $oldPath = public_path('storage/personalImages/' . basename($data['oldImage']));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            DB::commit();

            if ($affected > 0) {
                return response()->json([
                    'Status' => 'The operation succeeded',
                    'Result' => true,
                    'newImage' => $uploadedImage
                ]);
            } else {
                return response()->json(['Status' => 'No changes made or user not found']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'Status' => 'Error',
                'Message' => $e->getMessage()
            ], 500);
        }
    }
}
