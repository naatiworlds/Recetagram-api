<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected FCMService $fcm;

    public function __construct(FCMService $fcm)
    {
        $this->fcm = $fcm;
    }

    public function createNotification($userId, $type, $fromUserId, $referenceId = null, $message = '')
    {
        try {
            Log::info('Creating notification', [
                'user_id' => $userId,
                'type' => $type,
                'from_user_id' => $fromUserId,
                'reference_id' => $referenceId,
                'message' => $message
            ]);

            $notification = new Notification();
            $notification->user_id = $userId;
            $notification->type = $type;
            $notification->from_user_id = $fromUserId;
            $notification->message = $message;

            // Asignar el ID de referencia según el tipo
            if (in_array($type, ['like', 'comment'])) {
                $notification->post_id = $referenceId;
            } elseif (in_array($type, ['follow_request', 'follow_accepted', 'follow_rejected'])) {
                $notification->follow_id = $referenceId;
            }

            $notification->save();

            // 🔥 ENVÍO FCM
            $user = User::find($userId);
            $tokens = $user->notification_tokens ?? []; // Asegúrate de que el usuario tenga este atributo

            if (!empty($tokens)) {
                $this->fcm->send(
                    $tokens,
                    'Nueva notificación',
                    $message,
                    [
                        'type' => $type,
                        'from_user_id' => $fromUserId,
                        'reference_id' => $referenceId,
                    ]
                );
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUserNotifications($userId)
    {
        return Notification::with(['fromUser', 'post'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAllAsRead($userId)
    {
        // Marca las notificaciones como leídas y devuelve el número de registros actualizados
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);
    }
}
