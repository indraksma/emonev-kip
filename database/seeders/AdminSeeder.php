<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([ 
            'name' => 'Super Admin',
            'email' => 'admin@emonev.com',
            'password' => Hash::make('admin12345678'),
            'role' => 'admin', 
        ]);
    }
}