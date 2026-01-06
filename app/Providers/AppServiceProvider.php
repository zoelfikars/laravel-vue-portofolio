<?php

namespace App\Providers;

use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Policies\ProjectPolicy;
use App\Modules\User\Models\Experience;
use App\Modules\User\Policies\ExperiencePolicy;
use App\Modules\User\Policies\UserPolicy;
use App\Modules\UserProfile\Models\UserProfile;
use App\Modules\UserProfile\Policies\UserProfilePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    public function boot(): void
    {
        Gate::policy(UserProfile::class, UserProfilePolicy::class);
        Gate::policy(Experience::class, ExperiencePolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
