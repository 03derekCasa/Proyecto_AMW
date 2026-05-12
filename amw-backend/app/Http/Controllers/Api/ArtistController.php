<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\User;

class ArtistController extends Controller
{
    public function index()
    {
        $artists = User::with('profile')
            ->withCount('posts')
            ->orderBy('name')
            ->paginate(12);

        return response()->json([
            'message' => 'Artistas obtenidos correctamente',
            'data' => $artists,
        ]);
    }

    public function show($id)
    {
        $artist = User::with('profile')
            ->withCount('posts')
            ->findOrFail($id);

        $posts = $artist->posts()
            ->with(['user.profile', 'category'])
            ->withCount(['likes', 'comments'])
            ->where('is_published', true)
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Perfil público del artista obtenido correctamente',
            'data' => [
                'artist' => [
                    'id' => $artist->id,
                    'name' => $artist->name,
                    'posts_count' => $artist->posts_count,
                    'profile' => [
                        'artistic_name' => $artist->profile?->artistic_name,
                        'specialty' => $artist->profile?->specialty,
                        'biography' => $artist->profile?->biography,
                        'profile_image_url' => $artist->profile?->profile_image_url,
                        'social_links' => $artist->profile?->social_links,
                    ],
                ],
                'posts' => PostResource::collection($posts),
            ],
        ]);
    }
}
