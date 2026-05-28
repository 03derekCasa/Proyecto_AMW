<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Sube la imagen asociada a una publicación.
     */
    public function image(Request $request, CloudinaryService $cloudinaryService): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $upload = $cloudinaryService->uploadImage(
            $validated['image'],
            'posts'
        );

        return response()->json([
            'message' => 'Imagen subida correctamente',
            'data' => [
                /*
                 * Se conserva "path" porque tu frontend ya recibe este dato.
                 * Ahora contiene el identificador público de Cloudinary.
                 */
                'path' => $upload['public_id'],
                'url' => $upload['url'],
            ],
        ], 201);
    }
}
