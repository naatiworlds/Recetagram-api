<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use App\Helpers\ResponseHelper;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use App\Services\FollowService;

class FollowController extends Controller
{
    protected $notificationService;
    protected $followService;

    public function __construct(NotificationService $notificationService, FollowService $followService)
    {
        $this->notificationService = $notificationService;
        $this->followService = $followService;
    }

    public function follow(User $user)
    {
        try {
            $follower = auth()->user();

            // Log para debugging
            Log::info('Follow attempt', [
                'follower_id' => $follower->id,
                'following_id' => $user->id
            ]);

            // Usar el servicio para la lógica de follow
            $follow = $this->followService->follow($follower, $user);

            // Log de notificación
            Log::info('Creating follow notification', [
                'user_id' => $user->id,
                'type' => $follow->status === 'pending' ? 'follow_request' : 'new_follower',
                'from_user_id' => $follower->id,
                'follow_id' => $follow->id,
                'message' => $follow->status === 'pending'
                    ? "{$follower->name} quiere seguirte"
                    : "{$follower->name} ha comenzado a seguirte"
            ]);

            return ResponseHelper::success([
                'status' => $follow->status,
                'follow_id' => $follow->id
            ], $follow->status === 'pending' ? 'Solicitud enviada' : 'Siguiendo');

        } catch (\Exception $e) {
            Log::error('Error in follow action: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ResponseHelper::error('Error al procesar la solicitud: ' . $e->getMessage(), 400);
        }
    }

    public function acceptFollow($followId)
    {
        try {
            $follow = Follow::findOrFail($followId);
            
            if ($follow->following_id !== auth()->id()) {
                return ResponseHelper::error('No autorizado', 403);
            }

            $follow->update(['status' => 'accepted']);

            // Notificar al seguidor que su solicitud fue aceptada
            try {
                $this->notificationService->createNotification(
                    $follow->follower_id,
                    'follow_accepted',
                    auth()->id(),
                    null,
                    auth()->user()->name . " ha aceptado tu solicitud de seguimiento"
                );
            } catch (\Exception $e) {
                Log::error('Error creating follow acceptance notification: ' . $e->getMessage());
            }

            return ResponseHelper::success(null, 'Solicitud aceptada');
        } catch (\Exception $e) {
            Log::error('Error al aceptar solicitud: ' . $e->getMessage());
            return ResponseHelper::error('Error al aceptar la solicitud', 500);
        }
    }

    public function rejectFollow($followId)
    {
        try {
            $follow = Follow::findOrFail($followId);
            
            if ($follow->following_id !== auth()->id()) {
                return ResponseHelper::error('No autorizado', 403);
            }

            // Notificar al seguidor que su solicitud fue rechazada
            try {
                $this->notificationService->createNotification(
                    $follow->follower_id,
                    'follow_rejected',
                    auth()->id(),
                    null,
                    auth()->user()->name . " ha rechazado tu solicitud de seguimiento"
                );
            } catch (\Exception $e) {
                Log::error('Error creating follow rejection notification: ' . $e->getMessage());
            }

            $follow->delete();

            return ResponseHelper::success(null, 'Solicitud rechazada');
        } catch (\Exception $e) {
            Log::error('Error al rechazar solicitud: ' . $e->getMessage());
            return ResponseHelper::error('Error al rechazar la solicitud', 500);
        }
    }

    public function unfollow(User $user)
    {
        try {
            $follower = auth()->user();
            
            $follow = Follow::where('follower_id', $follower->id)
                          ->where('following_id', $user->id)
                          ->first();

            if (!$follow) {
                return ResponseHelper::error('No sigues a este usuario', 400);
            }

            $follow->delete();

            // Añadir notificación de unfollow
            try {
                $this->notificationService->createNotification(
                    $user->id,
                    'unfollow',
                    $follower->id,
                    null,
                    "{$follower->name} ha dejado de seguirte"
                );
            } catch (\Exception $e) {
                Log::error('Error creating unfollow notification: ' . $e->getMessage());
            }

            return ResponseHelper::success(null, 'Has dejado de seguir al usuario');
        } catch (\Exception $e) {
            Log::error('Error al dejar de seguir: ' . $e->getMessage());
            return ResponseHelper::error('Error al dejar de seguir', 500);
        }
    }

    public function getFollowers(User $user)
    {
        try {
            $followers = $user->followers()
                            ->where('status', 'accepted')
                            ->with('follower')
                            ->get()
                            ->pluck('follower');

            return ResponseHelper::success($followers, 'Seguidores recuperados exitosamente');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al recuperar seguidores', 500);
        }
    }

    public function getFollowing(User $user)
    {
        try {
            $following = $user->following()
                            ->where('status', 'accepted')
                            ->with('following')
                            ->get()
                            ->pluck('following');

            return ResponseHelper::success($following, 'Seguidos recuperados exitosamente');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al recuperar seguidos', 500);
        }
    }

    public function getPendingRequests()
    {
        try {
            $pendingRequests = auth()->user()
                ->followers()
                ->where('status', 'pending')
                ->with('follower')
                ->get();

            return ResponseHelper::success($pendingRequests, 'Solicitudes pendientes recuperadas');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al recuperar solicitudes', 500);
        }
    }

    public function checkStatus($followingId)
    {
        try {
            $followerId = auth()->id();
            $status = $this->followService->checkFollowStatus($followerId, $followingId);
            return ResponseHelper::success($status, 'Estado de seguimiento recuperado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error checking follow status: ' . $e->getMessage());
            return ResponseHelper::error('Error al verificar el estado de seguimiento', 500);
        }
    }
}