<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin1',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin', 
            'status'=> 'aktif',
            'position' => 'admin',
            'phone' => '081142389833',
            'photo' => 'default.jpg',
        ]);

        User::create([
            'name' => 'Gudang',
            'username' => 'Rafly',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'gudang', 
            'status'=> 'aktif',
            'position' => 'admin',
            'phone' => '081142389833',
            'photo' => 'default.jpg',
        ]);
    }
}
