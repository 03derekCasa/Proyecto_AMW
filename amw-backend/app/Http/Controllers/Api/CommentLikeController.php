<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Notifications\CommentLikedNotification;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function store(Request $request, Comment $comment)
    {
        $like = CommentLike::firstOrCreate([
            'user_id' => $request->user()->id,
            'comment_id' => $comment->id,
        ]);

        if ($like->wasRecentlyCreated && $comment->user_id !== $request->user()->id) {
            $actor = $request->user()->loadMissing('profile');
            $owner = $comment->user;
            $owner->notify(new CommentLikedNotification($actor, $comment));
        }

        return response()->json([
            'message' => $like->wasRecentlyCreated
                ? 'Like añadido al comentario'
                : 'El usuario ya había dado like al comentario',
            'data' => $this->preparedComment($comment, $request->user()->id),
        ]);
    }

    public function destroy(Request $request, Comment $comment)
    {
        CommentLike::where('user_id', $request->user()->id)
            ->where('comment_id', $comment->id)
            ->delete();

        return response()->json([
            'message' => 'Like eliminado del comentario',
            'data' => $this->preparedComment($comment, $request->user()->id),
        ]);
    }

    private function preparedComment(Comment $comment, int $userId): CommentResource
    {
        $comment->load(['user.profile']);
        $comment->loadCount('likes');
        $comment->setAttribute(
            'liked_by_me',
            $comment->likes()->where('user_id', $userId)->exists()
        );

        return new CommentResource($comment);
    }
}
