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
        
        // Debug log para ver los datos
        Log::info('Datos actuales del post:', [
            'post_actual' => $post->toArray()
        ]);
        Log::info('Datos recibidos para actualizar:', [
            'validated' => $validated
        ]);

        if ($post->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            throw new \Exception('No tienes permiso para actualizar este post');
        }

        // Preparar los datos a actualizar
        $dataToUpdate = [];
        
        if (isset($validated['title'])) {
            $dataToUpdate['title'] = $validated['title'];
            Log::info('Comparación de títulos:', [
                'titulo_actual' => $post->title,
                'titulo_nuevo' => $validated['title'],
                'son_diferentes' => $post->title !== $validated['title']
            ]);
        }
        
        if (isset($validated['description'])) {
            $dataToUpdate['description'] = $validated['description'];
        }
        
        if (isset($validated['ingredients'])) {
            $dataToUpdate['ingredients'] = $validated['ingredients'];
        }
        
        // Agregar el manejo de la imagen
        if (isset($validated['imagen'])) {
            try {
                // Si existe una imagen anterior, eliminarla de Cloudinary
                if ($post->imagen) {
                    $oldPublicId = $this->getPublicIdFromUrl($post->imagen);
                    if ($oldPublicId) {
                        Cloudinary::destroy($oldPublicId);
                    }
                }
                
                // Subir la nueva imagen a Cloudinary
                $uploadResult = Cloudinary::upload($validated['imagen']->getRealPath(), [
                    'folder' => 'posts'
                ]);
                
                // Obtener la URL segura directamente del resultado
                $secureUrl = $uploadResult['secure_url'];
                if (!$secureUrl) {
                    throw new \Exception('Error al obtener la URL de Cloudinary');
                }
                
                $dataToUpdate['imagen'] = $secureUrl;
            } catch (\Exception $e) {
                Log::error('Error uploading to Cloudinary: ' . $e->getMessage());
                throw new \Exception('Error al subir la imagen: ' . $e->getMessage());
            }
        }

        // Debug log para ver qué datos se van a actualizar
        Log::info('Datos que se van a actualizar:', [
            'dataToUpdate' => $dataToUpdate
        ]);

        // Si no hay cambios, devolver error
        if (empty($dataToUpdate)) {
            Log::info('No hay datos para actualizar');
            throw new \Exception('No se proporcionaron datos para actualizar');
        }

        // Intentar actualizar
        $updated = $post->update($dataToUpdate);
        
        if (!$updated) {
            throw new \Exception('No se pudo actualizar el post');
        }

        Log::info('Post actualizado correctamente');

        return Post::with([
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
