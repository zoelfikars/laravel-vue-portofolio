<?php

namespace App\Modules\User\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function view(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id;
    }

    public function delete(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id;
    }
}
