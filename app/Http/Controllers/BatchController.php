<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Follow;
use Illuminate\Support\Facades\Log;

class BatchController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function processBatch(Request $request)
    {
        $data = $request->validate([
            'likes' => 'array',
            'likes.*.post_id' => 'required|exists:posts,id',
            'comments' => 'array',
            'comments.*.post_id' => 'required|exists:posts,id',
            'comments.*.content' => 'required|string',
            'notifications' => 'array',
            'notifications.*.user_id' => 'required|exists:users,id',
            'notifications.*.type' => 'required|string',
            'notifications.*.message' => 'nullable|string',
            'follows' => 'array',
            'follows.*.user_id' => 'required|exists:users,id',
        ]);

        $results = [
            'likes' => [],
            'comments' => [],
            'notifications' => [],
            'follows' => [],
        ];

        try {
            // Procesar likes
            if (!empty($data['likes'])) {
                foreach ($data['likes'] as $like) {
                    $post = Post::find($like['post_id']);
                    if ($post && !$post->likes()->where('user_id', auth()->id())->exists()) {
                        $post->likes()->create(['user_id' => auth()->id()]);
                        $results['likes'][] = $post->id;
                    }
                }
            }

            // Procesar comentarios
            if (!empty($data['comments'])) {
                foreach ($data['comments'] as $commentData) {
                    $post = Post::find($commentData['post_id']);
                    if ($post) {
                        $comment = $post->comments()->create([
                            'content' => $commentData['content'],
                            'user_id' => auth()->id(),
                        ]);
                        $results['comments'][] = $comment->id;

                        // Crear notificación para el dueño del post
                        if ($post->user_id !== auth()->id()) {
                            $this->notificationService->createNotification(
                                $post->user_id,
                                'comment',
                                auth()->id(),
                                $post->id,
                                'Nuevo comentario en tu post: ' . $comment->content
                            );
                        }
                    }
                }
            }

            // Procesar notificaciones
            if (!empty($data['notifications'])) {
                foreach ($data['notifications'] as $notificationData) {
                    $this->notificationService->createNotification(
                        $notificationData['user_id'],
                        $notificationData['type'],
                        auth()->id(),
                        null,
                        $notificationData['message'] ?? ''
                    );
                    $results['notifications'][] = $notificationData['user_id'];
                }
            }

            // Procesar seguimientos
            if (!empty($data['follows'])) {
                foreach ($data['follows'] as $followData) {
                    $user = auth()->user();
                    $following = Follow::firstOrCreate([
                        'follower_id' => $user->id,
                        'following_id' => $followData['user_id'],
                    ], [
                        'status' => 'pending',
                    ]);
                    $results['follows'][] = $following->id;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Batch procesado exitosamente',
                'data' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error procesando batch: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error procesando batch',
            ], 500);
        }
    }
}