<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use App\Models\ReferralCode;
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
        $admin = Admin::create([
            'name' => 'Example Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'permissions' => [
                'can_accept_withdrawals',
                'can_reject_withdrawals',
                'can_accept_topup',
                'can_reject_topup',
            ],
        ]);
        $admin->wallet()->create();
        $admin->wallet->update(['balance' => 1000]);

        // Create referral code for admin
        ReferralCode::create([
            'admin_id' => $admin->id,
            'code' => 'ADMIN123',
            'status' => 'active',
        ]);

        // Joe Doe Admin
        $joe = Admin::create([
            'name' => 'Joe Doe',
            'email' => 'joe@example.com',
            'password' => Hash::make('password'),
            'permissions' => [
                'can_accept_withdrawals',
                'can_reject_withdrawals',
                'can_accept_topup',
                'can_reject_topup',
            ],
        ]);
        $joe->wallet()->create();
        $joe->wallet->update(['balance' => 1000]);

        // Create referral code for Joe
        ReferralCode::create([
            'admin_id' => $joe->id,
            'code' => 'JOE456',
            'status' => 'active',
        ]);

        // Example User
        $user = User::create([
            'name' => 'Example User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->wallet()->create();
        $user->wallet->update(['balance' => 200]);

        // Create referral code for user
        ReferralCode::create([
            'user_id' => $user->id,
            'code' => 'USER789',
            'status' => 'active',
        ]);

        // Run additional data seeder
        // $this->call(AdditionalDataSeeder::class);
    }
}
