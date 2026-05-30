<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'first_name'  => 'Admin',
                'last_name'   => 'User',
                'middle_name' => '',
                'email'       => 'admin@worldciti.edu.ph',
                'username'    => 'admin',
                'password'    => Hash::make('admin'),
                'role'        => 'master_admin',
                'department'  => 'IT',
                'status'      => 'Active',
            ],
            [
                'first_name'  => 'Juan',
                'last_name'   => 'Dela Cruz',
                'middle_name' => 'Santos',
                'email'       => 'juan.delacruz@worldciti.edu.ph',
                'username'    => 'juan.delacruz',
                'password'    => Hash::make('password123'),
                'role'        => 'faculty',
                'department'  => 'College of Arts and Sciences',
                'status'      => 'Active',
            ],
            [
                'first_name'  => 'Maria',
                'last_name'   => 'Santos',
                'middle_name' => 'Reyes',
                'email'       => 'maria.santos@worldciti.edu.ph',
                'username'    => 'maria.santos',
                'password'    => Hash::make('password123'),
                'role'        => 'faculty',
                'department'  => 'College of Business',
                'status'      => 'Active',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
