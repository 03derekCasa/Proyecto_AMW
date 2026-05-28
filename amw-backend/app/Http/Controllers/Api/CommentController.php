<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Notifications\PostCommentedNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request, $postId)
    {
        $post = Post::where('is_published', true)->findOrFail($postId);
        $currentUserId = auth('sanctum')->user()?->id;

        $comments = $post->comments()
            ->with(['user.profile'])
            ->withCount('likes')
            ->when($currentUserId, function ($query) use ($currentUserId) {
                $query->withExists([
                    'likes as liked_by_me' => function ($likeQuery) use ($currentUserId) {
                        $likeQuery->where('user_id', $currentUserId);
                    },
                ]);
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Comentarios obtenidos correctamente',
            'data' => CommentResource::collection($comments),
        ]);
    }

    public function store(Request $request, $postId)
    {
        $post = Post::with('user')->where('is_published', true)->findOrFail($postId);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'content' => $validated['content'],
        ]);

        if ($post->user_id !== $request->user()->id) {
            $actor = $request->user()->loadMissing('profile');
            $post->user->notify(new PostCommentedNotification($actor, $post, $comment));
        }

        $comment->load(['user.profile']);
        $comment->loadCount('likes');
        $comment->setAttribute('liked_by_me', false);

        return response()->json([
            'message' => 'Comentario creado correctamente',
            'data' => new CommentResource($comment),
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este comentario',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comentario eliminado correctamente',
        ]);
    }
}
