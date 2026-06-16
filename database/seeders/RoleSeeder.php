<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::firstOrCreate(['name' => 'manage cities']);
        Permission::firstOrCreate(['name' => 'view weather']);

        // create roles and assign existing permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->givePermissionTo('manage cities');
        $roleAdmin->givePermissionTo('view weather');

        $roleUser = Role::firstOrCreate(['name' => 'user']);
        $roleUser->givePermissionTo('view weather');

        // create a default admin
        if (!User::where('email', 'admin@weather.com')->exists()) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@weather.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $admin->assignRole($roleAdmin);
        }

        // create a default user
        if (!User::where('email', 'user@weather.com')->exists()) {
            $user = User::create([
                'name' => 'User',
                'email' => 'user@weather.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole($roleUser);
        }
    }
}
