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

        $existingFollow = Follow::where('follower_id', $follower->id)
            ->where('following_id', $userToFollow->id)
            ->first();

        if ($existingFollow) {
            if ($existingFollow->status === 'accepted') {
                throw new \Exception('Ya sigues a este usuario');
            }
            throw new \Exception('Ya tienes una solicitud pendiente');
        }

        $status = $userToFollow->is_public ? 'accepted' : 'pending';

        $follow = new Follow();
        $follow->follower_id = $follower->id;
        $follow->following_id = $userToFollow->id;
        $follow->status = $status;
        $follow->save();

        // Notificación
        $this->notificationService->createNotification(
            $userToFollow->id,
            $status === 'pending' ? 'follow_request' : 'new_follower',
            $follower->id,
            $follow->id,
            $status === 'pending' 
                ? "{$follower->name} quiere seguirte"
                : "{$follower->name} ha comenzado a seguirte"
        );

        return $follow;
    }
}