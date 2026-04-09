<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(
            $request->user()->load('profile')
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'artistic_name' => ['nullable', 'string', 'max:255'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'profile_image_url' => ['nullable', 'string', 'max:255'],
            'social_links' => ['nullable', 'array'],
        ]);

        $profile = $request->user()->profile;

        $profile->update($validated);

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'profile' => $profile,
        ]);
    }
}
