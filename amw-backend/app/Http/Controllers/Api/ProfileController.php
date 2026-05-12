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
        $profile = $request->user()
            ->profile()
            ->firstOrCreate(
                ['user_id' => $request->user()->id],
                ['artistic_name' => $request->user()->name]
            );

        return response()->json([
            'message' => 'Perfil obtenido correctamente',
            'data' => [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'artistic_name' => $profile->artistic_name,
                'specialty' => $profile->specialty,
                'biography' => $profile->biography,
                'profile_image_url' => $profile->profile_image_url,
                'social_links' => $profile->social_links,
            ],
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

        $profile = $request->user()
            ->profile()
            ->firstOrCreate(
                ['user_id' => $request->user()->id],
                ['artistic_name' => $request->user()->name]
            );

        $profile->update([
            'artistic_name' => $validated['artistic_name'],
            'specialty' => $validated['specialty'] ?? null,
            'biography' => $validated['biography'] ?? null,
            'social_links' => $validated['social_links'] ?? null,
        ]);

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'data' => [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'artistic_name' => $profile->artistic_name,
                'specialty' => $profile->specialty,
                'biography' => $profile->biography,
                'profile_image_url' => $profile->profile_image_url,
                'social_links' => $profile->social_links,
            ],
        ]);
    }

    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = $request->user()
            ->profile()
            ->firstOrCreate(
                ['user_id' => $request->user()->id],
                ['artistic_name' => $request->user()->name]
            );

        if ($profile->profile_image_url && str_contains($profile->profile_image_url, '/storage/')) {
            $oldPath = explode('/storage/', $profile->profile_image_url)[1] ?? null;

            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
        }

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
}
