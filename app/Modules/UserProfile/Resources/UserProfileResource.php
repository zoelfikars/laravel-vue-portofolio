<?php

namespace App\Modules\UserProfile\Resources;

use App\Modules\Project\Resources\ProjectListResource;
use App\Modules\Project\Resources\ProjectResource;
use App\Modules\User\Resources\ExperienceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum');

        $isPrivileged = $user && (
            $user->hasRole('super-admin') ||
            $user->id === $this->user_id
        );

        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'place_of_birth' => $this->place_of_birth,
            'date_of_birth' => $this->date_of_birth->format('Y-m-d'),
            'work_interest' => $this->work_interest,
            'summary' => $this->summary,
            'is_active' => (bool) $this->is_active,
            'photo_url' => $this->photo_path
                ? route('public.profile.photo', ['id' => $this->id])
                : null,
            'cv_url' => $this->resolveCvUrl($isPrivileged),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
        if ($this->relationLoaded('user')) {
            $data['skills'] = $this->user->skills->pluck('name');
            $data['hobbies'] = $this->user->hobbies->pluck('name');
            $data['experiences'] = ExperienceResource::collection($this->user->experiences);
            $data['projects'] = ProjectListResource::collection($this->user->projects);
            $data['contacts'] = $this->user->contacts;
        }
        return $data;
    }
    protected function resolveCvUrl(bool $isPrivileged): ?string
    {
        if (!$this->cv_path)
            return null;

        if ($isPrivileged) {
            return route('users.profile.cv', ['user' => $this->user_id]);
        }

        return route('public.profile.cv', ['id' => $this->id]);
    }
}
