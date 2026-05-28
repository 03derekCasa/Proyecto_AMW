<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Seguir a otro usuario.
     */
    public function store(Request $request, User $user): JsonResponse
    {
        $authenticatedUser = $request->user();

        if ($authenticatedUser->id === $user->id) {
            return response()->json([
                'message' => 'No puedes seguir tu propio perfil.',
            ], 422);
        }

        $alreadyFollowing = $authenticatedUser
            ->following()
            ->where('users.id', $user->id)
            ->exists();

        if (!$alreadyFollowing) {
            $authenticatedUser->following()->attach($user->id);
        }

        return response()->json([
            'message' => $alreadyFollowing
                ? 'Ya sigues a este usuario.'
                : 'Ahora sigues a este usuario.',
            'data' => $this->followData($authenticatedUser, $user),
        ]);
    }

    /**
     * Dejar de seguir a otro usuario.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $authenticatedUser = $request->user();

        if ($authenticatedUser->id === $user->id) {
            return response()->json([
                'message' => 'No puedes dejar de seguir tu propio perfil.',
            ], 422);
        }

        $authenticatedUser->following()->detach($user->id);

        return response()->json([
            'message' => 'Has dejado de seguir a este usuario.',
            'data' => $this->followData($authenticatedUser, $user),
        ]);
    }

    /**
     * Datos necesarios para actualizar inmediatamente
     * el botón y los contadores del perfil público.
     */
    private function followData(User $authenticatedUser, User $profileUser): array
    {
        return [
            'is_following' => $authenticatedUser
                ->following()
                ->where('users.id', $profileUser->id)
                ->exists(),

            'followers_count' => $profileUser->followers()->count(),
            'following_count' => $profileUser->following()->count(),
        ];
    }
}
