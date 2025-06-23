<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Example Admin
        Admin::create([
            'name' => 'Example Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'permissions' => json_encode([
                'can_accept_withdrawals',
                'can_reject_withdrawals',
                'can_accept_topup',
                'can_reject_topup',
            ]),
        ]);

        // Example User
        User::create([
            'name' => 'Example User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
