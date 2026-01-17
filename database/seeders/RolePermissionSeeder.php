<?php
namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);
        $admin = User::firstOrCreate(
            ['email' => 'admin@laravue.com'],
            [
                'name' => 'Super Administrator',
                'password' => env('SUPER_ADMIN_PASSWORD', 'password'),
            ]
        );
        $admin->assignRole($roleSuperAdmin);
        $user = User::firstOrCreate(
            ['email' => 'user@laravue.com'],
            [
                'name' => 'Regular User',
                'password' => env('USER_PASSWORD', 'password'),
            ]
        );
        $user->assignRole($roleUser);
    }
}
