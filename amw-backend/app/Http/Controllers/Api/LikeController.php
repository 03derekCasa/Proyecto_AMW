<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $like = Like::firstOrCreate([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        $post->load(['user.profile', 'category']);
        $post->loadCount('likes');

        return response()->json([
            'message' => $like->wasRecentlyCreated
                ? 'Like añadido correctamente'
                : 'El usuario ya había dado like a esta publicación',
            'data' => new PostResource($post),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        Like::where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->delete();

        $post->load(['user.profile', 'category']);
        $post->loadCount('likes');

        return response()->json([
            'message' => 'Like eliminado correctamente',
            'data' => new PostResource($post),
        ]);
    }

    public function myLikes(Request $request)
    {
        $posts = $request->user()
            ->likedPosts()
            ->with(['user.profile', 'category'])
            ->withCount('likes')
            ->latest('likes.created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Publicaciones favoritas obtenidas correctamente',
            'data' => PostResource::collection($posts),
        ]);
    }
}
