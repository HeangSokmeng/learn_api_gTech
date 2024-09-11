<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'reply_id',
    ];

    public function posts()
    {
        return $this->belongsTo(Posts::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
