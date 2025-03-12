<?php

namespace App\Services;

use App\Models\Follow;
use Illuminate\Support\Facades\Log;

class FollowService
{
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
} 