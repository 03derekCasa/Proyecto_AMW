<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, Conversation $conversation)
    {
        $this->authorizeConversationAccess($request, $conversation);

        $conversation->users()->updateExistingPivot($request->user()->id, [
            'last_read_at' => now(),
        ]);

        $messages = $conversation->messages()
            ->with(['sender.profile'])
            ->oldest()
            ->get();

        return response()->json([
            'message' => 'Mensajes obtenidos correctamente',
            'data' => MessageResource::collection($messages),
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorizeConversationAccess($request, $conversation);

        $validated = $request->validate([
            'body' => ['required_without:image_url', 'nullable', 'string', 'max:5000'],
            'image_url' => ['nullable', 'string', 'max:255'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        $conversation->touch();

        $message->load(['sender.profile']);

        return response()->json([
            'message' => 'Mensaje enviado correctamente',
            'data' => new MessageResource($message),
        ], 201);
    }

    private function authorizeConversationAccess(Request $request, Conversation $conversation): void
    {
        $belongsToConversation = $conversation->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        abort_unless($belongsToConversation, 403, 'No tienes acceso a esta conversación');
    }
}
