<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLikedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $actor,
        private readonly Post $post
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'post_liked',
            'text' => $this->actorName() . ' le ha dado me gusta a tu obra.',
            'actor' => $this->actorData(),
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'url' => '/posts/' . $this->post->id,
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
