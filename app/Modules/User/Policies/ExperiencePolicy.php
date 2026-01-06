<?php

namespace App\Modules\User\Policies;

use App\Models\User;
use App\Modules\User\Models\Experience;

class ExperiencePolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function view(User $user, Experience $experience)
    {
        return $user->id === $experience->user_id;
    }

    public function create(User $user, User $targetUser)
    {
        return false;
    }

    public function update(User $user, Experience $experience)
    {
        return $user->id === $experience->user_id;
    }

    public function delete(User $user, Experience $experience)
    {
        return $user->id === $experience->user_id;
    }
}
