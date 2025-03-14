<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Follow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
                // Subir imagen a Cloudinary con manejo de errores
                try {
                    $uploadedFile = Cloudinary::upload($validated['imagen']->getRealPath(), [
                        'folder' => 'posts',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto',
                        ]
                    ]);
                    
                    if (!$uploadedFile || !$uploadedFile->getSecurePath()) {
                        throw new \Exception('Error al obtener la URL de Cloudinary');
                    }
                    
                    $validated['imagen'] = $uploadedFile->getSecurePath();
                } catch (\Exception $e) {
                    Log::error('Error uploading to Cloudinary: ' . $e->getMessage());
                    throw new \Exception('Error al subir la imagen: ' . $e->getMessage());
                }
            }

            $post = Post::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'imagen' => $validated['imagen'] ?? null,
                'ingredients' => $validated['ingredients'],
                'user_id' => auth()->id(),
            ]);

            return Post::with(['user', 'likedBy' => function($query) {
                    $query->select('users.id', 'users.name');
                }])
                ->withCount(['likes', 'comments'])
                ->findOrFail($post->id);

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
        try {
            $post = Post::findOrFail($id);
            
            // Preparar los datos a actualizar
            $dataToUpdate = [];
            
            if (isset($validated['title'])) {
                $dataToUpdate['title'] = $validated['title'];
            }
            
            if (isset($validated['description'])) {
                $dataToUpdate['description'] = $validated['description'];
            }
            
            if (isset($validated['ingredients'])) {
                $dataToUpdate['ingredients'] = $validated['ingredients'];
            }

            if (isset($validated['imagen'])) {
                try {
                    // Si hay una imagen anterior, eliminarla de Cloudinary
                    if ($post->imagen) {
                        $oldPublicId = $this->getPublicIdFromUrl($post->imagen);
                        if ($oldPublicId) {
                            Cloudinary::destroy($oldPublicId);
                        }
                    }
                    
                    $uploadResult = Cloudinary::upload($validated['imagen']->getRealPath(), [
                        'folder' => 'posts'
                    ]);
                    
                    if (!isset($uploadResult['secure_url'])) {
                        throw new \Exception('Error al obtener la URL de Cloudinary');
                    }
                    
                    $dataToUpdate['imagen'] = $uploadResult['secure_url'];
                } catch (\Exception $e) {
                    Log::error('Error uploading to Cloudinary: ' . $e->getMessage());
                    throw new \Exception('Error al subir la imagen: ' . $e->getMessage());
                }
            }

            // Realizar la actualización solo si hay datos para actualizar
            if (!empty($dataToUpdate)) {
                $post->update($dataToUpdate);
            }

            // Recargar el post con todas sus relaciones y conteos
            $updatedPost = Post::with([
                'user' => function($query) {
                    $query->select('id', 'name', 'email', 'role', 'created_at', 'updated_at', 'is_private', 'is_public');
                },
                'likedBy' => function($query) {
                    $query->select('users.id', 'users.name')
                        ->withPivot('post_id', 'user_id');
                }
            ])
            ->withCount(['likes', 'comments'])
            ->findOrFail($post->id);

            // Asegurarse de que el post está fresco con los últimos datos
            $updatedPost->refresh();

            return $updatedPost;

        } catch (\Exception $e) {
            Log::error('Error in updatePost: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    private function getPublicIdFromUrl($url)
    {
        if (preg_match('/\/v\d+\/([^\/]+)\/[^\/]+$/', $url, $matches)) {
            return 'posts/' . pathinfo($matches[1], PATHINFO_FILENAME);
        }
        return null;
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
