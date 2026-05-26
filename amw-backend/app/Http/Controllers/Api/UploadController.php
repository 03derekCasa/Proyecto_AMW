<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function image(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $path = $validated['image']->store('posts', 'public');

        return response()->json([
            'message' => 'Imagen subida correctamente',
            'data' => [
                'path' => $path,
                'url' => asset('storage/' . $path),
            ],
        ], 201);
    }
}
