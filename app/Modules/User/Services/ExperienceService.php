<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\User\Models\Experience;

class ExperienceService
{
    public function list(User $user, array $params)
    {
        $query = $user->experiences()
            ->orderBy('is_current', 'desc')
            ->orderBy('start_date', 'desc');

        if (isset($params['search']) && $params['search']) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company', 'ilike', "%{$search}%")
                    ->orWhere('position', 'ilike', "%{$search}%")
                    ->orWhere('location', 'ilike', "%{$search}%");
            });
        }

        $perPage = $params['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    public function listPublic(User $user, array $params)
    {
        $query = $user->experiences()
            ->orderBy('is_current', 'desc')
            ->orderBy('start_date', 'desc');

        if (isset($params['search']) && $params['search']) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company', 'ilike', "%{$search}%")
                    ->orWhere('position', 'ilike', "%{$search}%")
                    ->orWhere('location', 'ilike', "%{$search}%");
            });
        }

        $perPage = $params['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    public function create(User $user, array $data): Experience
    {
        if ($data['is_current'] ?? false) {
            $data['end_date'] = null;
        }

        return $user->experiences()->create($data);
    }

    public function update(Experience $experience, array $data): Experience
    {
        if ($data['is_current'] ?? false) {
            $data['end_date'] = null;
        }

        $experience->update($data);

        return $experience;
    }

    public function delete(Experience $experience): void
    {
        $experience->delete();
    }
}
