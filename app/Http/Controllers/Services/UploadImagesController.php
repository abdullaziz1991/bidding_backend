<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadImagesController extends Controller
{
    public function uploadImages(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:2048',
            // |mimes:jpeg,png,jpg,gif,webp
        ]);

        $uploadedPaths = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('productImages', 'public');
            $uploadedPaths[] = asset('storage/' . str_replace('public/', '', $path));
        }

        return response()->json([
            'Status' => 'Success',
            'ImagePaths' => $uploadedPaths,
        ]);
    }
}
