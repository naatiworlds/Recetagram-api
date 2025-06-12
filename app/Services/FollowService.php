<?php

namespace App\Services;

use App\Models\User;
use App\Models\Follow;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class FollowService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function checkFollowStatus($followerId, $followingId)
    {
        Log::info("Checking follow status between {$followerId} and {$followingId}");

        $follow = Follow::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->first();

        if (!$follow) {
            return ['status' => 'not_following'];
        }

        return ['status' => $follow->status]; // 'pending' o 'accepted'
    }

    public function follow(User $follower, User $userToFollow)
    {
        if ($follower->id === $userToFollow->id) {
            throw new \Exception('No puedes seguirte a ti mismo');
        }

        $status = $userToFollow->is_public ? 'accepted' : 'pending';

        $follow = Follow::firstOrCreate(
            [
                'follower_id' => $follower->id,
                'following_id' => $userToFollow->id,
            ],
            [
                'status' => $status
            ]
        );

        // Si ya existía, podemos verificar estado para no duplicar notificaciones:
        if ($follow->wasRecentlyCreated) {
            // Notificación solo si es nuevo
            $this->notificationService->createNotification(
                $userToFollow->id,
                $status === 'pending' ? 'follow_request' : 'new_follower',
                $follower->id,
                $follow->id,
                $status === 'pending'
                    ? "{$follower->name} quiere seguirte"
                    : "{$follower->name} ha comenzado a seguirte"
            );
        } else {
            // Ya existe follow, puedes lanzar excepción o devolver status actual
            if ($follow->status === 'accepted') {
                throw new \Exception('Ya sigues a este usuario');
            }
            if ($follow->status === 'pending') {
                throw new \Exception('Ya tienes una solicitud pendiente');
            }
        }

        return $follow;
    }
}
