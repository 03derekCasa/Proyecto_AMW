<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();

        $participant = $this->users
            ->where('id', '!=', $currentUser->id)
            ->first();

        $currentUserInConversation = $this->users
            ->where('id', $currentUser->id)
            ->first();

        $lastReadAt = $currentUserInConversation?->pivot?->last_read_at;

        $unreadQuery = $this->messages()
            ->where('sender_id', '!=', $currentUser->id);

        if ($lastReadAt) {
            $unreadQuery->where('created_at', '>', $lastReadAt);
        }

        return [
            'id' => $this->id,

            'participant' => $participant ? [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'avatar' => $participant->avatar ?? null,
                'profile' => [
                    'artistic_name' => $participant->profile?->artistic_name,
                    'specialty' => $participant->profile?->specialty,
                    'profile_image_url' => $participant->profile?->profile_image_url,
                ],
            ] : null,

            'users' => $this->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar ?? null,
                    'profile' => [
                        'artistic_name' => $user->profile?->artistic_name,
                        'specialty' => $user->profile?->specialty,
                        'profile_image_url' => $user->profile?->profile_image_url,
                    ],
                ];
            }),

            'last_message' => $this->whenLoaded('lastMessage', function () {
                if (!$this->lastMessage) {
                    return null;
                }

                return [
                    'id' => $this->lastMessage->id,
                    'body' => $this->lastMessage->body,
                    'image_url' => $this->lastMessage->image_url,
                    'sender_id' => $this->lastMessage->sender_id,
                    'created_at' => $this->lastMessage->created_at?->toDateTimeString(),
                ];
            }),

            'unread_count' => $unreadQuery->count(),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
