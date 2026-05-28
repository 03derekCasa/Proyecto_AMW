<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentLikedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $actor,
        private readonly Comment $comment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment_liked',
            'text' => $this->actorName() . ' le ha dado me gusta a tu comentario.',
            'actor' => $this->actorData(),
            'post_id' => $this->comment->post_id,
            'comment_id' => $this->comment->id,
            'comment_preview' => mb_strimwidth($this->comment->content, 0, 80, '...'),
            'url' => '/posts/' . $this->comment->post_id,
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
