<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        try {
            $notifications = auth()->user()
                ->notifications()
                ->with(['fromUser', 'post', 'follow'])
                ->orderBy('created_at', 'desc')
                ->get();

            return ResponseHelper::success($notifications, 'Notificaciones recuperadas exitosamente');
        } catch (\Exception $e) {
            Log::error('Error getting notifications: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ResponseHelper::error('Error al recuperar las notificaciones: ' . $e->getMessage(), 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = $this->notificationService->markAsRead($id);
            return ResponseHelper::success($notification, 'Notification marked as read');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error marking notification as read', 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $userId = auth()->user()->id;
            $result = $this->notificationService->markAllAsRead($userId);

            return ResponseHelper::success($result, 'Todas las notificaciones han sido marcadas como leídas');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al actualizar las notificaciones', 500);
        }
    }
}