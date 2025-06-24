<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;

class AdditionalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 22 additional users with top-up requests
        for ($i = 1; $i <= 22; $i++) {
            $user = User::create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
            ]);

            // Create wallet for the user
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'admin_id' => null,
                'balance' => 0,
                'held_balance' => 0,
            ]);

            // Create a top-up request for each user
            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'top-up',
                'amount' => rand(50, 500), // Random amount between 50-500 EGP
                'status' => 'pending',
                'description' => "User {$i} top-up request.",
                'created_at' => now()->subDays(rand(1, 30)), // Random date within last 30 days
            ]);
        }

        // Get existing admins
        $admins = Admin::all();

        // Create 16 withdrawal requests for admins
        for ($i = 1; $i <= 16; $i++) {
            // Randomly select an admin
            $admin = $admins->random();
            $adminWallet = $admin->wallet;

            // Ensure admin has enough balance for withdrawal
            if ($adminWallet->balance < 100) {
                // Add some balance to the admin if needed
                $adminWallet->increment('balance', 200);
            }

            $withdrawalAmount = rand(50, 200); // Random amount between 50-200 EGP

            // Create withdrawal request
            $transaction = Transaction::create([
                'wallet_id' => $adminWallet->id,
                'type' => 'withdrawal',
                'amount' => $withdrawalAmount,
                'status' => 'pending',
                'description' => "Admin {$admin->name} withdrawal request.",
                'created_at' => now()->subDays(rand(1, 30)), // Random date within last 30 days
            ]);

            // Hold the amount and deduct from balance
            $adminWallet->decrement('balance', $withdrawalAmount);
            $adminWallet->increment('held_balance', $withdrawalAmount);
        }

        $this->command->info('Created 22 users with top-up requests and 16 admin withdrawal requests.');
    }
}
