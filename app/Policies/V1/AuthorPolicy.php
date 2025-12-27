<?php

namespace App\Policies\V1;

use App\Enums\UserTypeEnum;
use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
    /**
     * Global before check. Admins can do everything.
     */
    public function before(User $user, $ability)
    {
        $role = $user->roles->first()->name ?? UserTypeEnum::User->value;
        if ($role === UserTypeEnum::Admin->value) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can create an author.
     * - Users can become authors
     * - Authors and Editors should not create another author for themselves
     * - Admins handled in before()
     */
    public function create(User $user): bool
    {
        $role = $user->roles->first()->name ?? UserTypeEnum::User->value;
        return !in_array($role, [UserTypeEnum::Author->value, UserTypeEnum::Editor->value], true);
    }

    /**
     * Determine whether the user can update the author.
     * - Owner (author.user_id === user.id) can update
     * - Editors may be allowed depending on app policy (here denied)
     * - Admins allowed in before()
     */
    public function update(User $user, Author $author): bool
    {
        if ($author->user_id === $user->id) {
            return true;
        }

        // Editors are not allowed by default to update authors in this policy.
        return false;
    }

    /**
     * Determine whether the user can view the author.
     */
    public function view(User $user, Author $author): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the author.
     */
    public function delete(User $user, Author $author): bool
    {
        // Only owner or admin (admin handled by before)
        return $author->user_id === $user->id;
    }
}
