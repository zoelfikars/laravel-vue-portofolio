<?php

namespace App\Modules\User\Policies;

use App\Models\User;
use App\Modules\User\Models\Experience;

class ExperiencePolicy
{
    public function viewAny(User $user, User $targetUser)
    {
        return $user->hasRole('super-admin') || $user->id === $targetUser->id;
    }

    public function view(User $user, Experience $experience)
    {
        return $user->hasRole('super-admin') || $user->id === $experience->user_id;
    }

    public function create(User $user, User $targetUser)
    {
        return $user->hasRole('super-admin') || $user->id === $targetUser->id;
    }

    public function update(User $user, Experience $experience)
    {
        return $user->hasRole('super-admin') || $user->id === $experience->user_id;
    }

    public function delete(User $user, Experience $experience)
    {
        return $user->hasRole('super-admin') || $user->id === $experience->user_id;
    }
}
