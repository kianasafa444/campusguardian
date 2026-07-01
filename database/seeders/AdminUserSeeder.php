<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Satgas',
            'email' => 'admin@kampus.ac.id',
            'password' => 'password',
            'role' => 'admin_satgas',
            'is_active' => true,
        ]);
    }
}
