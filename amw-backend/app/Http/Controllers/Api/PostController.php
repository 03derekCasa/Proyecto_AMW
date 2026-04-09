<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Listar todos los posts públicos
    public function index()
    {
        $posts = Post::with(['user.profile', 'category'])
            ->where('is_published', true)
            ->latest()
            ->get();

        return response()->json($posts);
    }

    // Crear nuevo post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:255',
            'type' => 'required|in:obra,evento,producto',
            'is_published' => 'nullable|boolean',
        ]);

        $post = Post::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'type' => $validated['type'],
            'is_published' => $validated['is_published'] ?? true,
        ]);

        return response()->json([
            'message' => 'Publicación creada correctamente',
            'post' => $post->load(['user.profile', 'category']),
        ], 201);
    }

    // Ver una publicación concreta
    public function show($id)
    {
        $post = Post::with(['user.profile', 'category'])->findOrFail($id);

        return response()->json($post);
    }

    // Ver publicaciones del usuario autenticado
    public function myPosts()
    {
        $posts = auth()->user()
            ->posts()
            ->with(['category'])
            ->latest()
            ->get();

        return response()->json($posts);
    }

    // Actualizar publicación
    public function update(Request $request, $id)
    {
        $post = Post::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:255',
            'type' => 'sometimes|required|in:obra,evento,producto',
            'is_published' => 'nullable|boolean',
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Publicación actualizada correctamente',
            'post' => $post->load(['user.profile', 'category']),
        ]);
    }

    // Eliminar publicación
    public function destroy($id)
    {
        $post = Post::where('user_id', auth()->id())->findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Publicación eliminada correctamente'
        ]);
    }
}
