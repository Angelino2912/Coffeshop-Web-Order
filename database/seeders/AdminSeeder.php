<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Accounts
        Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ]
        );

        Admin::updateOrCreate(
            ['email' => 'admin1@gmail.com'],
            [
                'name' => 'angelinoo',
                'password' => Hash::make('admin1'),
                'role' => 'admin',
            ]
        );

        // Kasir Accounts
        Admin::updateOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('kasir'),
                'role' => 'kasir',
            ]
        );
    }
}
