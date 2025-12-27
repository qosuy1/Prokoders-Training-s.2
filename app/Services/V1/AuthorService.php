<?php

namespace App\Services\V1;

use App\Enums\UserTypeEnum;
use App\Models\Author;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class AuthorService
{
    /**
     * Create an author and assign roles accordingly.
     *
     * @param User $actor The user performing the action
     * @param array $data Validated request data
     * @return Author
     *
     * @throws AuthorizationException
     */
    public function createAuthor(User $actor, array $data): Author
    {
        $role = $this->getActorRoleName($actor);

        if ($this->isAlreadyAuthorOrEditor($role)) {
            throw new AuthorizationException("Forbidden: you are already {$role}");
        }

        return DB::transaction(function () use ($actor, $data, $role) {
            $targetUser = $this->determineTargetUser($actor, $data, $role);
            $author = Author::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'user_id' => $targetUser->id,
            ]);

            return $author;
        });
    }

    /**
     * Get the actor primary role name or default to 'user'.
     */
    private function getActorRoleName(User $actor): string
    {
        return $actor->roles->first()->name ?? UserTypeEnum::User->value;
    }

    /**
     * Returns true when the role indicates the actor is already an author or editor.
     */
    private function isAlreadyAuthorOrEditor(string $role): bool
    {
        return in_array($role, [UserTypeEnum::Author->value, UserTypeEnum::Editor->value], true);
    }

    /**
     * Determine which user should be assigned the author role and ensure roles sync.
     *
     * @throws \InvalidArgumentException
     */
    private function determineTargetUser(User $actor, array $data, string $role): User
    {

        // Owner
        if ($role === UserTypeEnum::User->value) {
            $actor->roles()->detach();
            $actor->syncRoles(UserTypeEnum::Author->value);
            return $actor;
        }

        // Admin whant to update user role
        if ($role === UserTypeEnum::Admin->value) {
            $userId = $data['user_id'] ?? null;
            if (!$userId) {
                throw new \InvalidArgumentException('user_id is required for admin to assign author role');
            }
            $userToEdit = User::findOrFail($userId);
            $userToEdit->roles()->detach();
            $userToEdit->syncRoles(UserTypeEnum::Author->value);
            return $userToEdit;
        }

        // Fallback: assign to actor
        $actor->roles()->detach();
        $actor->syncRoles(UserTypeEnum::Author->value);
        return $actor;
    }

    /**
     * Update an author with new data.
     *
     * @param Author $author
     * @param array $data Validated request data
     * @return Author
     */
    public function updateAuthor(Author $author, array $data): Author
    {
        return DB::transaction(function () use ($author, $data) {
            $author->update([
                'name' => $data['name'] ?? $author->name,
                'email' => $data['email'] ?? $author->email,
            ]);

            return $author->fresh();
        });
    }
}
