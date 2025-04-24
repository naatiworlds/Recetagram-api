<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function createNotification($userId, $type, $fromUserId, $referenceId = null, $message = '')
    {
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
        return $notification;
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
