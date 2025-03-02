<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function getAllPosts()
    {
        return Post::with('user')->latest()->get();
    }

    public function createPost(array $validated)
    {
        try {
            if (isset($validated['imagen'])) {
                // Guardar la imagen en storage/app/public/posts
                $imagePath = $validated['imagen']->store('posts', 'public');
                $validated['imagen'] = $imagePath;
            }

            Log::info('Image saved successfully:', ['path' => $validated['imagen']]);

            return Post::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'imagen' => $validated['imagen'],
                'ingredients' => $validated['ingredients'],
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PostService::createPost: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getPostById($id)
    {
        return Post::with('user')->findOrFail($id);
    }

    public function updatePost($id, array $validated)
    {
        $post = Post::findOrFail($id);

        if (isset($validated['imagen'])) {
            // Eliminar la imagen anterior si existe
            if ($post->imagen) {
                Storage::disk('public')->delete($post->imagen);
            }
            // Guardar la nueva imagen
            $imagePath = $validated['imagen']->store('posts', 'public');
            $validated['imagen'] = $imagePath;
        }

        $post->update($validated);
        return $post;
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        
        // Eliminar la imagen si existe
        if ($post->imagen) {
            Storage::disk('public')->delete($post->imagen);
        }
        
        $post->delete();
    }
}
