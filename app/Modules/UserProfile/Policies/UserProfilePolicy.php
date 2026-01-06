<?php
namespace App\Modules\UserProfile\Policies;
use App\Models\User;
use App\Modules\UserProfile\Models\UserProfile;
class UserProfilePolicy
{
    public function view(User $user, UserProfile $userProfile)
    {
        return $user->hasRole('super-admin') || $user->id === $userProfile->user_id;
    }
    public function update(User $user, UserProfile $userProfile)
    {
        return $user->hasRole('super-admin') || $user->id === $userProfile->user_id;
    }
}
