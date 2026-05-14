<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'hashtags' => $this->hashtags ?? [],
            'image_url' => $this->image_url,
            'type' => $this->type,
            'is_published' => $this->is_published,
            'likes_count' => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,

            'category' => $this->whenLoaded('category', function () {
                if (!$this->category) {
                    return null;
                }

                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),

            'author' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user?->id,
                    'name' => $this->user?->name,
                    'artistic_name' => $this->user?->profile?->artistic_name,
                    'profile_image_url' => $this->user?->profile?->profile_image_url,
                ];
            }),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
