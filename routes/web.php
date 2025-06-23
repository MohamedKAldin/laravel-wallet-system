<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    $admin = auth('admin')->user();
    $notifications = $admin->notifications()->latest()->take(10)->get();
    $unreadCount = $admin->unreadNotifications()->count();

    $topupCount = Transaction::where('type', 'top-up')->count();
    $withdrawalCount = Transaction::where('type', 'withdrawal')->count();
    $pendingTopups = Transaction::where('type', 'top-up')->where('status', 'pending')->with('wallet.user')->get();
    $pendingWithdrawals = Transaction::where('type', 'withdrawal')->where('status', 'pending')->with('wallet.admin')->get();

    $permissions = $admin->permissions ?? [];

    return view('admin.dashboard', compact(
        'admin',
        'notifications',
        'unreadCount',
        'topupCount',
        'withdrawalCount',
        'pendingTopups',
        'pendingWithdrawals',
        'permissions'
    ));
})->middleware('auth:admin')->name('admin.dashboard');

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::guard('admin')->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('admin.dashboard'));
    }
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->withInput();
});

Route::post('/admin/logout', function (Request $request) {
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('admin.login');
});
