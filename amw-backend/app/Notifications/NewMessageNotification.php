<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $actor,
        private readonly Conversation $conversation,
        private readonly Message $message
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_message',
            'text' => 'Tienes un nuevo mensaje de ' . $this->actorName() . '.',
            'actor' => $this->actorData(),
            'conversation_id' => $this->conversation->id,
            'message_id' => $this->message->id,
            'message_preview' => mb_strimwidth((string) $this->message->body, 0, 80, '...'),
            'url' => '/messages?conversation=' . $this->conversation->id,
        ];
    }

    private function actorName(): string
    {
        return $this->actor->profile?->artistic_name
            ?? $this->actor->username
            ?? $this->actor->name;
    }

    private function actorData(): array
    {
        return [
            'id' => $this->actor->id,
            'name' => $this->actorName(),
            'username' => $this->actor->username,
            'profile_image_url' => $this->actor->profile?->profile_image_url,
        ];
    }
}
