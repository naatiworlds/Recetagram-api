<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Helpers\ResponseHelper;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function toggle(Post $post)
    {
        try {
            $user = auth()->user();
            
            if ($post->likes()->where('user_id', $user->id)->exists()) {
                $post->likes()->where('user_id', $user->id)->delete();
                return ResponseHelper::success([
                    'liked' => false,
                    'likes_count' => $post->likes()->count()
                ], 'Like removido exitosamente');
            }

            $post->likes()->create(['user_id' => $user->id]);

            // Solo crear notificación si el like es a un post de otro usuario
            if ($post->user_id !== $user->id) {
                try {
                    $this->notificationService->createNotification(
                        $post->user_id,
                        'like',
                        $user->id,
                        $post->id,
                        "{$user->name} ha dado like a tu post"
                    );
                } catch (\Exception $e) {
                    // Log el error pero no interrumpir la operación de like
                    Log::error('Error creating notification: ' . $e->getMessage());
                }
            }

            return ResponseHelper::success([
                'liked' => true,
                'likes_count' => $post->likes()->count()
            ], 'Like agregado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error en toggle like: ' . $e->getMessage());
            return ResponseHelper::error('Error al procesar el like', 500);
        }
    }
}