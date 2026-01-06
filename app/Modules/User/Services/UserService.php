<?php

namespace App\Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class UserService
{
    public function list(array $params): LengthAwarePaginator
    {
        $query = User::query()
            ->with('roles')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super-admin');
            });

        if (isset($params['search']) && $params['search']) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $perPage = $params['per_page'] ?? 10;

        return $query->latest()->paginate($perPage);
    }
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $user->assignRole($userRole);
        return $user;
    }
    public function update(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }
        $user->update($updateData);
        return $user;
    }
    public function delete(User $user): void
    {
        $user->delete();
    }
}
