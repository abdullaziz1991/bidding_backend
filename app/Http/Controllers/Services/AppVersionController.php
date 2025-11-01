<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class AppVersionController extends Controller
{
    public function getVersion(): JsonResponse
    {
        $path = 'json/AppVersion.json';

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'Version file not found'], 404);
        }
        $content = Storage::disk('public')->get($path);
        $json = json_decode($content, true);
        return response()->json($json);
    }
}
