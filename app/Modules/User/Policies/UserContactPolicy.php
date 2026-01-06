<?php

namespace App\Modules\User\Policies;

use App\Models\User;
use App\Modules\User\Models\UserContact;

class UserContactPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function viewAny(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id;
    }

    public function view(User $user, UserContact $contact)
    {
        return $user->id === $contact->user_id;
    }

    public function create(User $user, User $targetUser)
    {
        return false; // Only super-admin via before()
    }

    public function update(User $user, UserContact $contact)
    {
        return $user->id === $contact->user_id;
    }

    public function delete(User $user, UserContact $contact)
    {
        return $user->id === $contact->user_id;
    }
}
