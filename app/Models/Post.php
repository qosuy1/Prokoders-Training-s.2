<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $fillable = [
        'author_id',
        'title',
        'body',
        'published_at'
    ];

    /**
     * Get the categories for this post.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_category', 'post_id', 'category_id');
    }

    /**
     * Get the author of this post.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get the comments for this post.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if the post is published.
     */
    public function getPublishedAttribute()
    {
        return $this->published_at != null;
    }
}
