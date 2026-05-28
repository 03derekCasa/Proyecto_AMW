<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Listar o buscar usuarios para iniciar conversaciones.
     *
     * Solo devuelve datos públicos:
     * - nombre artístico
     * - @username
     * - especialidad
     * - foto de perfil
     */
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $cleanSearch = ltrim(strtolower($search), '@');

        $users = User::query()
            ->with('profile')
            ->where('id', '!=', $request->user()->id)
            ->when($search !== '', function ($query) use ($search, $cleanSearch) {
                $query->where(function ($subQuery) use ($search, $cleanSearch) {
                    $subQuery
                        ->where('username', 'ILIKE', "%{$cleanSearch}%")
                        ->orWhereHas('profile', function ($profileQuery) use ($search) {
                            $profileQuery
                                ->where('artistic_name', 'ILIKE', "%{$search}%")
                                ->orWhere('specialty', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('username')
            ->limit(20)
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'profile' => [
                        'id' => $user->profile?->id,
                        'artistic_name' => $user->profile?->artistic_name,
                        'specialty' => $user->profile?->specialty,
                        'biography' => $user->profile?->biography,
                        'profile_image_url' => $user->profile?->profile_image_url,
                        'cover_image_url' => $user->profile?->cover_image_url,
                        'social_links' => $user->profile?->social_links,
                    ],
                ];
            });

        return response()->json([
            'message' => 'Usuarios obtenidos correctamente',
            'data' => $users,
        ]);
    }

    /**
     * Perfil público de otro artista.
     */
    public function publicProfile(Request $request, int $id): JsonResponse
    {
        $user = User::query()
            ->with('profile')
            ->withCount(['followers', 'following'])
            ->findOrFail($id);

        $posts = Post::query()
            ->with(['category', 'user.profile'])
            ->withCount(['likes', 'comments'])
            ->where('user_id', $user->id)
            ->where('is_published', true)
            ->latest()
            ->get();

        $isFollowing = false;

        if ($request->user()->id !== $user->id) {
            $isFollowing = $request->user()
                ->following()
                ->where('users.id', $user->id)
                ->exists();
        }

        return response()->json([
            'message' => 'Perfil público obtenido correctamente',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],

                'profile' => [
                    'id' => $user->profile?->id,
                    'user_id' => $user->id,
                    'artistic_name' => $user->profile?->artistic_name,
                    'specialty' => $user->profile?->specialty,
                    'biography' => $user->profile?->biography,
                    'profile_image_url' => $user->profile?->profile_image_url,
                    'cover_image_url' => $user->profile?->cover_image_url,
                    'social_links' => $user->profile?->social_links,
                ],

                'posts' => PostResource::collection($posts),

                'stats' => [
                    'works_count' => $posts->count(),
                    'followers' => $user->followers_count,
                    'following' => $user->following_count,
                ],

                /*
                 * Este valor permitirá que Vue muestre:
                 * Seguir / Siguiendo.
                 */
                'is_following' => $isFollowing,

                /*
                 * Nos permite ocultar el botón si accidentalmente
                 * se abre el perfil público propio.
                 */
                'is_own_profile' => $request->user()->id === $user->id,
            ],
        ]);
    }
}
