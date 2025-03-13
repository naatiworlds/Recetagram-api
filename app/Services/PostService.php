<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Follow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function getAllPosts()
    {
        return Post::with(['user', 'likedBy'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();
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
        return Post::with(['user', 'comments.user', 'likedBy'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($id);
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
    public function getPostsByUserId($userId)
    {
        return Post::where('user_id', $userId)->get();
    }

    public function getFollowingPosts($userId)
    {
        Log::info('Getting following posts for user: ' . $userId);

        $followingIds = Follow::where('follower_id', $userId)
            ->where('status', 'accepted')
            ->pluck('following_id')
            ->toArray();

        Log::info('Following IDs:', $followingIds);

        return Post::whereIn('user_id', $followingIds)
            ->with(['user', 'likedBy'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();
    }

    public function getPublicPosts()
    {
        Log::info('Getting public posts');

        return Post::whereHas('user', function ($query) {
            $query->where('is_public', true);
        })
            ->with(['user', 'likedBy'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();
    }

    public function getFilteredPosts($filters = [])
    {
        try {
            Log::info('Filtering posts with criteria:', $filters);

            $query = Post::query()
                ->with(['user', 'likedBy'])
                ->withCount(['likes', 'comments']);

            // Añadir try-catch específico para la consulta
            try {
                $results = $query->latest()->get();
                Log::info('Found ' . $results->count() . ' posts');
                return $results;
            } catch (\PDOException $e) {
                Log::error('Database error: ' . $e->getMessage());
                throw new \Exception('Error de base de datos al recuperar los posts');
            }
        } catch (\Exception $e) {
            Log::error('Error in getFilteredPosts: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
