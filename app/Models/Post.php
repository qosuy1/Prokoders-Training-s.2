<?php

namespace App\Models;

use Dom\Comment;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    public $fillable = [
        'author_id',
        'title',
        'body',
        'published_at'
    ];

    // Relations
    public function category(){
        $this->belongsToMany(Category::class );
    }

    public function author(){
        $this->belongsTo(Author::class);
    }

    public function comments(){
        $this->hasMany(Comment::class);
    }

    // Accessors
    public function getPublishedAttribute(){
        $this->published_at != null ;
    }
    
}
