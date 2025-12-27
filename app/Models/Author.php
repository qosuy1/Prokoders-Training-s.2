<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'name',
        'email',
        'user_id'
    ];
    
    public function user(){
        $this->belongsTo(User::class);
    }

    public function posts(){
        $this->hasMany(Post::class);
    }
}
