<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request)
    {
        try {
            $posts = Post::with(['user', 'likedBy'])
                ->withCount(['likes', 'comments'])
                ->latest()
                ->get();

            return ResponseHelper::success($posts, 'Posts recuperados exitosamente');
        } catch (\Exception $e) {
            log::error('Error en PostController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return ResponseHelper::error('Error al recuperar los posts: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'ingredients' => 'required|string' // Cambiado a string para recibir JSON
            ]);

            // Decodificar los ingredientes
            $validated['ingredients'] = json_decode($validated['ingredients'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid ingredients format');
            }

            Log::info('Validation passed. Request data:', $request->all());

            $post = $this->postService->createPost($validated);
            Log::info('Post created successfully:', ['post_id' => $post->id]);

            return ResponseHelper::success($post, 'Post created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return ResponseHelper::error($e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return ResponseHelper::error('Failed to create post: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $post = $this->postService->getPostById($id);
            return ResponseHelper::success($post, 'Post retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Post not found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Debug más detallado
            Log::info('Request completo:', [
                'all' => $request->all(),
                'has_file' => $request->hasFile('imagen'),
                'raw_content' => $request->getContent(),
                'files' => $request->allFiles(),
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);

            // Validar primero
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'imagen' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'ingredients' => 'sometimes|string'
            ]);

            // Si la validación pasa pero no hay datos, intentar obtener los datos del input directamente
            if (empty($validated)) {
                $data = [];
                
                // Obtener datos del input directamente
                foreach (['title', 'description', 'ingredients'] as $field) {
                    if ($request->input($field) !== null) {
                        $data[$field] = $request->input($field);
                    }
                }

                // Manejar la imagen separadamente
                if ($request->hasFile('imagen')) {
                    $data['imagen'] = $request->file('imagen');
                }

                // Si aún no hay datos, devolver error
                if (empty($data)) {
                    return ResponseHelper::error('No se proporcionaron datos válidos', 422, [
                        'code' => 'VALIDATION_ERROR',
                        'detail' => 'La solicitud no contiene campos válidos para actualizar',
                        'debug' => [
                            'request_data' => $request->all(),
                            'input_data' => $request->input(),
                            'files' => $request->allFiles()
                        ]
                    ]);
                }

                $validated = $data;
            }

            // Continuar con el proceso de actualización
            $post = $this->postService->updatePost($id, $validated);
            return ResponseHelper::success($post, 'Post actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error en update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ResponseHelper::error('Error al actualizar el post: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->postService->deletePost($id);
            return ResponseHelper::success(null, 'Post deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to delete post', 500);
        }
    }

    public function getUserPosts($userId)
    {
        try {
            $posts = $this->postService->getPostsByUserId($userId);
            return ResponseHelper::success($posts, 'Posts del usuario recuperados exitosamente');
        } catch (\Exception $e) {
            Log::error('Error getting user posts: ' . $e->getMessage());
            return ResponseHelper::error('Error al recuperar los posts del usuario', 500);
        }
    }

    public function getFollowingPosts()
    {
        Log::info('Accessing getFollowingPosts method');
        try {
            $posts = $this->postService->getFollowingPosts(auth()->id());
            return ResponseHelper::success($posts, 'Posts de usuarios seguidos recuperados exitosamente');
        } catch (\Exception $e) {
            Log::error('Error getting following posts: ' . $e->getMessage());
            return ResponseHelper::error('Error al recuperar los posts', 500);
        }
    }

    public function getPublicPosts()
    {
        try {
            $posts = $this->postService->getPublicPosts();
            return ResponseHelper::success($posts, 'Posts públicos recuperados exitosamente');
        } catch (\Exception $e) {
            Log::error('Error getting public posts: ' . $e->getMessage());
            return ResponseHelper::error('Error al recuperar los posts públicos', 500);
        }
    }
}
