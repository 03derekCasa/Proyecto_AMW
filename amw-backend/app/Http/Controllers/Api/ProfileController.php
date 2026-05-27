<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $this->getOrCreateProfile($request);

        return response()->json([
            'message' => 'Perfil obtenido correctamente',
            'data' => $this->profileData($profile),
        ]);
    }

    public function update(Request $request)
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

    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = $this->getOrCreateProfile($request);

        $this->deleteStoredImage($profile->profile_image_url);

        $path = $validated['image']->store('profiles', 'public');

        $profile->update([
            'profile_image_url' => asset('storage/' . $path),
        ]);

        return response()->json([
            'message' => 'Imagen de perfil subida correctamente',
            'data' => [
                'profile_image_url' => $profile->profile_image_url,
            ],
        ], 201);
    }

    public function uploadCoverImage(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $profile = $this->getOrCreateProfile($request);

        $this->deleteStoredImage($profile->cover_image_url);

        $path = $validated['image']->store('profile-covers', 'public');

        $profile->update([
            'cover_image_url' => asset('storage/' . $path),
        ]);

        return response()->json([
            'message' => 'Imagen de cabecera subida correctamente',
            'data' => [
                'cover_image_url' => $profile->cover_image_url,
            ],
        ], 201);
    }

    private function getOrCreateProfile(Request $request): Profile
    {
        return $request->user()
            ->profile()
            ->firstOrCreate(
                ['user_id' => $request->user()->id],
                ['artistic_name' => $request->user()->name]
            );
    }

    private function deleteStoredImage(?string $imageUrl): void
    {
        if (!$imageUrl || !str_contains($imageUrl, '/storage/')) {
            return;
        }

        $oldPath = explode('/storage/', $imageUrl)[1] ?? null;

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
    }

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
