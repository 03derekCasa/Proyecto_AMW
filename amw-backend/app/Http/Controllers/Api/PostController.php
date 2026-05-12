<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user.profile', 'category'])
            ->withCount(['likes', 'comments'])
            ->where('is_published', true)
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'ILIKE', "%{$search}%")
                        ->orWhere('description', 'ILIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Publicaciones obtenidas correctamente',
            'data' => PostResource::collection($posts),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:obra,evento,producto'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'type' => $validated['type'],
            'is_published' => $validated['is_published'] ?? true,
        ]);

        $post->load(['user.profile', 'category']);
        $post->loadCount(['likes', 'comments']);

        return response()->json([
            'message' => 'Publicación creada correctamente',
            'data' => new PostResource($post->load),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $post = Post::with(['user.profile', 'category'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($id);

        if (!$post->is_published && (!$request->user() || $post->user_id !== $request->user()->id)) {
            return response()->json([
                'message' => 'No tienes permiso para ver esta publicación',
            ], 403);
        }

        return response()->json([
            'message' => 'Publicación obtenida correctamente',
            'data' => new PostResource($post),
        ]);
    }

    public function myPosts(Request $request)
    {
        $posts = $request->user()
            ->posts()
            ->with(['category'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Publicaciones del usuario obtenidas correctamente',
            'data' => PostResource::collection($posts),
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'in:obra,evento,producto'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $post->update($validated);
        $post->load(['user.profile', 'category']);
        $post->loadCount('likes');

        return response()->json([
            'message' => 'Publicación actualizada correctamente',
            'data' => new PostResource($post),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::where('user_id', $request->user()->id)->findOrFail($id);

        $post->delete();

        return response()->json([
            'message' => 'Publicación eliminada correctamente',
        ]);
    }
}
