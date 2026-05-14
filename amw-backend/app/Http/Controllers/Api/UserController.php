<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::query()
            ->with('profile')
            ->where('id', '!=', $request->user()->id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhereHas('profile', function ($profileQuery) use ($search) {
                            $profileQuery
                                ->where('artistic_name', 'ILIKE', "%{$search}%")
                                ->orWhere('specialty', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? null,
                    'profile' => [
                        'id' => $user->profile?->id,
                        'artistic_name' => $user->profile?->artistic_name,
                        'specialty' => $user->profile?->specialty,
                        'biography' => $user->profile?->biography,
                        'profile_image_url' => $user->profile?->profile_image_url,
                        'social_links' => $user->profile?->social_links,
                    ],
                ];
            });

        return response()->json([
            'message' => 'Usuarios obtenidos correctamente',
            'data' => $users,
        ]);
    }

    public function publicProfile(Request $request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

        $posts = Post::with(['category', 'user.profile'])
            ->where('user_id', $user->id)
            ->where('is_published', true)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Perfil público obtenido correctamente',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? null,
                ],
                'profile' => [
                    'id' => $user->profile?->id,
                    'user_id' => $user->id,
                    'artistic_name' => $user->profile?->artistic_name,
                    'specialty' => $user->profile?->specialty,
                    'biography' => $user->profile?->biography,
                    'profile_image_url' => $user->profile?->profile_image_url,
                    'social_links' => $user->profile?->social_links,
                ],
                'posts' => $posts,
                'stats' => [
                    'works_count' => $posts->count(),
                    'followers' => 0,
                    'following' => 0,
                ],
            ],
        ]);
    }
}
