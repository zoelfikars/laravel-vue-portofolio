<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);
        $admin = User::firstOrCreate(
            ['email' => 'admin@laravue.com'],
            [
                'name' => 'Super Administrator',
                'password' => 'password',
            ]
        );
        $admin->assignRole($roleSuperAdmin);
        $user = User::firstOrCreate(
            ['email' => 'user@laravue.com'],
            [
                'name' => 'Regular User',
                'password' => 'password',
            ]
        );
        $user->assignRole($roleUser);
    }
}
