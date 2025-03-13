<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'from_user_id',
        'post_id',
        'follow_id',
        'message',
        'read'
    ];

    protected $casts = [
        'read' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function follow()
    {
        return $this->belongsTo(Follow::class);
    }
}