<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\Hobby;

class HobbyService
{
    public function search(array $params)
    {
        $search = $params['search'] ?? null;

        return Hobby::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'name']);
    }
}
