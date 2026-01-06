<?php
namespace App\Modules\Project\Policies;
use App\Models\User;
use App\Modules\Project\Models\Project;
class ProjectPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }
    public function view(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }
    public function create(User $user)
    {
        return false;
    }
    public function update(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }
    public function delete(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }
}
