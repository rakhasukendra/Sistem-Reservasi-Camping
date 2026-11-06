<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@demo.test',
            'password' => bcrypt('Admin#2026'),
            'phone' => '081234567890',
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Staff Demo',
            'email' => 'staff@demo.test',
            'password' => bcrypt('Staff#2026'),
            'phone' => '081234567891',
            'role' => 'staff',
        ]);

        \App\Models\User::create([
            'name' => 'User Demo',
            'email' => 'user@demo.test',
            'password' => bcrypt('User#2026'),
            'phone' => '081234567892',
            'role' => 'user',
        ]);
    }
}
