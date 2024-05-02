<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(
            [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_APP_USER,
            ]
        )->each(function ($roleName) {
            Role::create(['name' => $roleName]);
        });

        $user = User::where('email', 'admin@cliq.com')->first();
        if (!$user) {
            $user = User::create(
                [
                    'first_name' => 'Cliq',
                    'last_name' => 'Admin',
                    'email' => 'admin@cliq.com',
                    'password' => Hash::make('Rspl123#'),
                    'remember_token' => Str::random(60),
                    'email_verified_at' => Carbon::now(),
                ]
            );
        }
        $role = Role::findByName(User::ROLE_SUPER_ADMIN);
        $user->assignRole($role->id);
    }
}
