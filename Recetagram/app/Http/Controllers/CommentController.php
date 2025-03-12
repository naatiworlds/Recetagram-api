<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        try {
            $comments = $post->comments()->with('user')->latest()->get();
            return ResponseHelper::success($comments, 'Comentarios recuperados exitosamente');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al recuperar los comentarios', 500);
        }
    }

    public function show(Post $post, Comment $comment)
    {
        try {
            return ResponseHelper::success($comment, 'Comentario recuperado exitosamente');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al recuperar el comentario', 500);
        }
    }

    public function store(Request $request, Post $post)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string'
            ]);

            $comment = $post->comments()->create([
                'content' => $validated['content'],
                'user_id' => auth()->id()
            ]);

            return ResponseHelper::success($comment, 'Comentario creado exitosamente', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al crear el comentario', 500);
        }
    }

    public function update(Request $request, Post $post, Comment $comment)
    {
        try {
            if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return ResponseHelper::error('No autorizado', 403);
            }

            $validated = $request->validate([
                'content' => 'required|string'
            ]);

            $comment->update($validated);
            return ResponseHelper::success($comment, 'Comentario actualizado exitosamente');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al actualizar el comentario', 500);
        }
    }

    public function destroy(Post $post, Comment $comment)
    {
        try {
            if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return ResponseHelper::error('No autorizado', 403);
            }

            $comment->delete();
            return ResponseHelper::success(null, 'Comentario eliminado exitosamente');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al eliminar el comentario', 500);
        }
    }
} 