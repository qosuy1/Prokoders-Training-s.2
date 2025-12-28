<?php

namespace App\Policies\V1;

use App\Enums\UserTypeEnum;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function viewAny(?User $user)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Comment $comment)
    {
        if ($user->hasRole([UserTypeEnum::Admin->value, UserTypeEnum::Editor->value])) {
            return true;
        }

        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Post $post, Comment $comment)
    {
        if ($user->hasRole([UserTypeEnum::Admin->value, UserTypeEnum::Editor->value])) {
            return true;
        }
        if ($post->author->id == $user->author->id)
            return true;

        return $comment->user_id === $user->id;
    }
}

