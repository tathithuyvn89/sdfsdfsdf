<?php

use Illuminate\Database\Seeder;
use App\Laravue\Acl;
use App\Laravue\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // foreach ($userList as $fullName) {
        //     $name = str_replace(' ', '.', $fullName);
        //     $roleName = \App\Laravue\Faker::randomInArray([
        //         Acl::ROLE_USER
        //     ]);
        //     $user = \App\Laravue\Models\User::create([
        //         'name' => $fullName,
        //         'email' => strtolower($name) . '@laravue.dev',
        //         'password' => \Illuminate\Support\Facades\Hash::make('laravue'),
        //         'avatar' => 'default-avatar.jpg'
        //     ]);

        //     $role = Role::findByName($roleName);
        //     $user->syncRoles($role);
        
    }
}
