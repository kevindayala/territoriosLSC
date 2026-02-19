<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $admin = \App\Models\User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // City
        $city = \App\Models\City::create(['name' => 'Bucaramanga', 'slug' => 'bucaramanga', 'is_active' => true]);


    }
}
