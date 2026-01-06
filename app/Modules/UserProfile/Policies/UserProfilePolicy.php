<?php
namespace App\Modules\UserProfile\Policies;
use App\Models\User;
use App\Modules\UserProfile\Models\UserProfile;
class UserProfilePolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }
    public function view(User $user, UserProfile $userProfile)
    {
        return $user->id === $userProfile->user_id;
    }
    public function create(User $user, UserProfile $userProfile)
    {
        return $user->id === $userProfile->user_id;

    }
}
