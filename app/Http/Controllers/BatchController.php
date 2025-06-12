<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Services\FollowService;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BatchController extends Controller
{
    protected $notificationService;
    protected $followService;

    public function __construct(NotificationService $notificationService, FollowService $followService)
    {
        $this->notificationService = $notificationService;
        $this->followService = $followService;
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
                    try {
                        if ($post) {
                            $existingLike = $post->likes()->where('user_id', auth()->id())->first();
                            if ($existingLike) {
                                // Si ya existe el like, eliminarlo (dislike)
                                $existingLike->delete();
                                $results['likes'][] = ['post_id' => $post->id, 'action' => 'dislike'];
                            } else {
                                // Si no existe el like, crearlo
                                $post->likes()->create(['user_id' => auth()->id()]);
                                $results['likes'][] = ['post_id' => $post->id, 'action' => 'like'];
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error procesando like/dislike para el post ' . $post->id . ': ' . $e->getMessage());
                        $results['likes'][] = ['post_id' => $post->id, 'action' => 'error', 'message' => $e->getMessage()];
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
                    try {
                        $userToFollow = User::findOrFail($followData['user_id']);
                        $follow = $this->followService->follow(auth()->user(), $userToFollow);
                        $results['follows'][] = [
                            'user_id' => $userToFollow->id,
                            'status' => $follow->status,
                            'follow_id' => $follow->id
                        ];
                    } catch (\Exception $e) {
                        $results['follows'][] = [
                            'user_id' => $followData['user_id'],
                            'error' => $e->getMessage()
                        ];
                    }
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