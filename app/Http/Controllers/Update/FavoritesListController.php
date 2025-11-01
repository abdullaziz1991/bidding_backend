<?php

namespace App\Http\Controllers\Update;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class FavoritesListController extends Controller
{
    
public function updateFavoritesList(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required|integer|exists:users,id',
        'FavoritesList' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'Status' => 'Missing or invalid parameters',
            'errors' => $validator->errors(),
        ], 400);
    }

    $id = $request->input('id');
    $favoritesList = $request->input('FavoritesList');
    $user = User::where('id', $id)->first();

    if (!$user) {
        return response()->json([
            'Status' => 'User not found'
        ], 404);
    }

    $user->favoritesList = $favoritesList;

    if ($user->isDirty('favoritesList')) {
        $user->save();
        return response()->json(['Status' => 'The operation succeeded']);
    } else {
        return response()->json(['Status' => 'No changes were made']);
    }
}
}
