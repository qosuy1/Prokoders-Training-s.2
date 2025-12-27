<?php

namespace App\Policies\V1;

use App\Enums\UserTypeEnum;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    public function before(User $user, $ability)
    {
        $role = $user->roles->first()->name ?? UserTypeEnum::User->value;
        if ($role === UserTypeEnum::Admin->value) {
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        if ($post->published)
            return true;
        if ($user === null)
            return false;

        // authors can view their own unpublished posts
        return $user->id == $post->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([UserTypeEnum::Editor->value, UserTypeEnum::Author->value]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        if (
            $user->hasRole(UserTypeEnum::Editor->value)
            || ($user->author() && $post->author_id == $user->author->id)
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {

        if (
            $user->hasRole(UserTypeEnum::Editor->value)
            || ($user->author() && $post->author_id == $user->author->id)
        )
            return true;


        return false;
    }

    public function publish(User $user, Post $post)
    {
        if ($user->hasRole([UserTypeEnum::Admin->value, UserTypeEnum::Editor->value]))
            return true;
        if ($user->hasRole(UserTypeEnum::Author->value)) {
            return $user->author
                && $post->author_id === $user->author->id;
        }
    }

}
