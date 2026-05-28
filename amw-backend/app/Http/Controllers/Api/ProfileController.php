<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Devuelve el perfil del usuario autenticado.
     */
    public function show(Request $request): JsonResponse
    {
        $profile = $this->getOrCreateProfile($request);

        return response()->json([
            'message' => 'Perfil obtenido correctamente',
            'data' => $this->profileData($profile),
        ]);
    }

    /**
     * Actualiza los datos de texto del perfil.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'artistic_name' => ['required', 'string', 'max:255'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string', 'max:5000'],

            'social_links' => ['nullable', 'array'],
            'social_links.instagram' => ['nullable', 'string', 'max:255'],
            'social_links.behance' => ['nullable', 'string', 'max:255'],
            'social_links.website' => ['nullable', 'string', 'max:255'],
            'social_links.tiktok' => ['nullable', 'string', 'max:255'],
            'social_links.youtube' => ['nullable', 'string', 'max:255'],
        ]);

        $profile = $this->getOrCreateProfile($request);

        $profile->update([
            'artistic_name' => $validated['artistic_name'],
            'specialty' => $validated['specialty'] ?? null,
            'biography' => $validated['biography'] ?? null,
            'social_links' => $validated['social_links'] ?? null,
        ]);

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'data' => $this->profileData($profile),
        ]);
    }

    /**
     * Sube la imagen de perfil del usuario a Cloudinary.
     */
    public function uploadImage(
        Request $request,
        CloudinaryService $cloudinaryService
    ): JsonResponse {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = $this->getOrCreateProfile($request);

        /*
         * Elimina únicamente una imagen antigua guardada localmente.
         * Esto permite mantener compatibilidad con imágenes subidas antes
         * de integrar Cloudinary.
         */
        $this->deleteLocalStoredImage($profile->profile_image_url);

        $upload = $cloudinaryService->uploadImage(
            $validated['image'],
            'profiles'
        );

        $profile->update([
            'profile_image_url' => $upload['url'],
        ]);

        return response()->json([
            'message' => 'Imagen de perfil subida correctamente',
            'data' => [
                'profile_image_url' => $profile->profile_image_url,
            ],
        ], 201);
    }

    /**
     * Sube la imagen de portada del perfil a Cloudinary.
     */
    public function uploadCoverImage(
        Request $request,
        CloudinaryService $cloudinaryService
    ): JsonResponse {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $profile = $this->getOrCreateProfile($request);

        /*
         * Elimina únicamente portadas antiguas almacenadas todavía
         * en el disco local de Laravel.
         */
        $this->deleteLocalStoredImage($profile->cover_image_url);

        $upload = $cloudinaryService->uploadImage(
            $validated['image'],
            'profile-covers'
        );

        $profile->update([
            'cover_image_url' => $upload['url'],
        ]);

        return response()->json([
            'message' => 'Imagen de cabecera subida correctamente',
            'data' => [
                'cover_image_url' => $profile->cover_image_url,
            ],
        ], 201);
    }

    /**
     * Obtiene el perfil existente o crea uno básico para el usuario.
     */
    private function getOrCreateProfile(Request $request): Profile
    {
        return $request->user()
            ->profile()
            ->firstOrCreate(
                ['user_id' => $request->user()->id],
                ['artistic_name' => $request->user()->name]
            );
    }

    /**
     * Elimina imágenes antiguas almacenadas mediante /storage/.
     *
     * Las imágenes nuevas se guardan en Cloudinary. Por ahora no se borran
     * automáticamente imágenes antiguas de Cloudinary, porque tu base de
     * datos todavía no guarda su public_id.
     */
    private function deleteLocalStoredImage(?string $imageUrl): void
    {
        if (!$imageUrl || !str_contains($imageUrl, '/storage/')) {
            return;
        }

        $oldPath = explode('/storage/', $imageUrl)[1] ?? null;

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    /**
     * Estructura de datos enviada al frontend.
     */
    private function profileData(Profile $profile): array
    {
        return [
            'id' => $profile->id,
            'user_id' => $profile->user_id,
            'artistic_name' => $profile->artistic_name,
            'specialty' => $profile->specialty,
            'biography' => $profile->biography,
            'profile_image_url' => $profile->profile_image_url,
            'cover_image_url' => $profile->cover_image_url,
            'social_links' => $profile->social_links,
        ];
    }
}
