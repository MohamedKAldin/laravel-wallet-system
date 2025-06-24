<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $admin = auth('admin')->user();
        
        // Set default pagination parameters to 1 (first page) if not provided
        $topupPage = request()->get('topup_page', 1);
        $withdrawalPage = request()->get('withdrawal_page', 1);
        $notificationPage = request()->get('notification_page', 1);
        $referralPage = request()->get('referral_page', 1);
        
        // Eager load notifications with pagination
        $notifications = $admin->notifications()
            ->latest()
            ->paginate(5, ['*'], 'notification_page');
        $unreadCount = $admin->unreadNotifications()->count();

        // Use Eloquent models for transaction counts
        $topupCount = Transaction::topUp()->count();
        $withdrawalCount = Transaction::withdrawal()->count();
        
        // Eager load pending transactions with relationships
        $pendingTopups = Transaction::topUp()
            ->pending()
            ->with(['wallet.user'])
            ->paginate(5, ['*'], 'topup_page');
            
        $pendingWithdrawals = Transaction::withdrawal()
            ->pending()
            ->with(['wallet.admin', 'wallet.user'])
            ->paginate(5, ['*'], 'withdrawal_page');

        // Eager load admin wallet with approved withdrawals
        $admin->load(['wallet.transactions' => function($query) {
            $query->withdrawal()->approved();
        }]);
        
        $adminWallet = $admin->wallet;
        $adminBalance = $adminWallet ? $adminWallet->balance : 0;
        $adminTotalWithdrawals = $adminWallet ? $adminWallet->transactions->sum('amount') : 0;
        $adminHeldAmount = $adminWallet ? $adminWallet->held_balance : 0;

        // Get users who registered through ANY referral codes (both admin and user codes)
        $referralUsers = User::whereHas('wallet.transactions', function($query) {
            $query->referralBonus()
                  ->approved()
                  ->where('description', 'like', 'Referral bonus from % (Code: %');
        })->with(['wallet.transactions' => function($query) {
            $query->referralBonus()
                  ->approved()
                  ->where('description', 'like', 'Referral bonus from % (Code: %');
        }])->get()->map(function($user) {
            // Get the referral transaction
            $referralTransaction = $user->wallet->transactions->where('type', 'referral_bonus')->first();
            
            // Extract referral code and referrer from transaction description
            $description = $referralTransaction->description ?? '';
            $referralCode = null;
            $referrerName = null;
            $referrerType = null;

            // Parse the description: "Referral bonus from [Name] (Code: [CODE])"
            if (preg_match('/Referral bonus from (.+?) \(Code: (.+?)\)/', $description, $matches)) {
                $referrerName = trim($matches[1]);
                $referralCode = trim($matches[2]);
                   
                // Determine if referrer is admin or user by checking the referral code
                $referralCodeModel = \App\Models\ReferralCode::where('code', $referralCode)->first();
                if ($referralCodeModel) {
                    if ($referralCodeModel->admin_id) {
                        $referrerType = 'Admin';
                    } elseif ($referralCodeModel->user_id) {
                        $referrerType = 'User';
                    }
                }
            }
            
            $user->referral_code_used = $referralCode;
            $user->referrer_name = $referrerName;
            $user->referrer_type = $referrerType;
            
            return $user;
        });

        $referralUsersCount = $referralUsers->count();
        
        // Paginate the referral users collection
        $referralUsersPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $referralUsers->forPage($referralPage, 5),
            $referralUsers->count(),
            5,
            $referralPage,
            ['path' => request()->url(), 'pageName' => 'referral_page']
        );

        $permissions = $admin->permissions ?? [];

        return view('admin.dashboard', compact(
            'admin',
            'notifications',
            'unreadCount',
            'topupCount',
            'withdrawalCount',
            'pendingTopups',
            'pendingWithdrawals',
            'adminBalance',
            'adminTotalWithdrawals',
            'adminHeldAmount',
            'referralUsersPaginated',
            'referralUsersCount',
            'permissions'
        ));
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 