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
            'email' => 'admin@lsc.com',
        ], [
            'name' => 'Administrador LSC',
            'password' => \Illuminate\Support\Facades\Hash::make('secret123'),
        ]);
        $admin->assignRole('admin');
    }
}
