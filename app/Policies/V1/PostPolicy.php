<?php

namespace App\Policies\V1;

use App\Enums\UserTypeEnum;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Intercept all authorization checks.
     * Admins have access to all actions.
     */
    public function before(User $user, $ability): ?bool
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
     * Published posts are viewable by everyone.
     * Unpublished posts can only be viewed by the author.
     */
    public function view(?User $user, Post $post): bool
    {
        // Published posts are publicly viewable
        if ($post->published_at) {
            return true;
        }

        // Unpublished posts can only be viewed by the author
        if ($user === null) {
            return false;
        }

        return $post->author_id == $user->author->id;
    }

    /**
     * Determine whether the user can create posts.
     * Only Editors and Authors can create posts.
     */
    public function create(User $user): bool
    {
        return $user->hasRole([UserTypeEnum::Editor->value, UserTypeEnum::Author->value]);
    }

    /**
     * Determine whether the user can update the model.
     * Editors can update any post.
     * Authors can only update their own posts.
     */
    public function update(User $user, Post $post): bool
    {
        // Editors can update any post
        if ($user->hasRole(UserTypeEnum::Editor->value)) {
            return true;
        }

        // Authors can only update their own posts
        if ($user->author() && $post->author_id === $user->author->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * Editors can delete any post.
     * Authors can only delete their own posts.
     */
    public function delete(User $user, Post $post): bool
    {
        // Editors can delete any post
        if ($user->hasRole(UserTypeEnum::Editor->value)) {
            return true;
        }

        // Authors can only delete their own posts
        if ($user->author() && $post->author_id === $user->author->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can publish the model.
     * Admins and Editors can publish any post.
     * Authors can only publish their own posts.
     */
    public function publish(User $user, Post $post): bool
    {
        // Admins and Editors can publish any post
        if ($user->hasRole([UserTypeEnum::Admin->value, UserTypeEnum::Editor->value])) {
            return true;
        }

        // Authors can only publish their own posts
        if ($user->hasRole(UserTypeEnum::Author->value)) {
            return $user->author && $post->author_id === $user->author->id;
        }

        return false;
    }
}
