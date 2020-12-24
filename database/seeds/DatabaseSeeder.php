<?php

use App\Laravue\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Laravue\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@laravue.dev',
            'password' => Hash::make('laravue'),
            'avatar' => 'default-avatar.jpg'
        ]);
        $user = User::create([
            'name' => 'User',
            'email' => 'user@laravue.dev',
            'password' => Hash::make('laravue'),
            'avatar' => 'default-avatar.jpg'
        ]);

        $adminRole = Role::findByName(\App\Laravue\Acl::ROLE_ADMIN);
        $userRole = Role::findByName(\App\Laravue\Acl::ROLE_USER);
        $admin->syncRoles($adminRole);
        $user->syncRoles($userRole);
        $this->call(UsersTableSeeder::class);
    }
}
