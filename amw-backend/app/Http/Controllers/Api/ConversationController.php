<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $conversations = $request->user()
            ->conversations()
            ->with(['users.profile', 'lastMessage'])
            ->latest('conversations.updated_at')
            ->get();

        return response()->json([
            'message' => 'Conversaciones obtenidas correctamente',
            'data' => ConversationResource::collection($conversations),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $currentUser = $request->user();
        $participantId = (int) $validated['participant_id'];

        if ($currentUser->id === $participantId) {
            return response()->json([
                'message' => 'No puedes crear una conversación contigo mismo',
            ], 422);
        }

        $participant = User::findOrFail($participantId);

        $conversation = Conversation::whereHas('users', function ($query) use ($currentUser) {
            $query->where('users.id', $currentUser->id);
        })
            ->whereHas('users', function ($query) use ($participantId) {
                $query->where('users.id', $participantId);
            })
            ->first();

        if (!$conversation) {
            $conversation = DB::transaction(function () use ($currentUser, $participant) {
                $conversation = Conversation::create();

                $conversation->users()->attach([
                    $currentUser->id => [
                        'last_read_at' => now(),
                    ],
                    $participant->id => [
                        'last_read_at' => null,
                    ],
                ]);

                return $conversation;
            });
        }

        $conversation->load(['users.profile', 'lastMessage']);

        return response()->json([
            'message' => 'Conversación preparada correctamente',
            'data' => new ConversationResource($conversation),
        ], 201);
    }

    public function markAsRead(Request $request, Conversation $conversation)
    {
        $this->authorizeConversationAccess($request, $conversation);

        $conversation->users()->updateExistingPivot($request->user()->id, [
            'last_read_at' => now(),
        ]);

        return response()->json([
            'message' => 'Conversación marcada como leída',
        ]);
    }

    private function authorizeConversationAccess(Request $request, Conversation $conversation): void
    {
        $belongsToConversation = $conversation->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        abort_unless($belongsToConversation, 403, 'No tienes acceso a esta conversación');
    }
}
