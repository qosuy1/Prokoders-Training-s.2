<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //

    protected $fillable = [
        'body',
        'user_id'
    ];
    public function post(){
        $this->belongsTo(Post::class);
    }
}
