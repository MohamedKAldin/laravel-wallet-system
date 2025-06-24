<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .min-table-wrapper {
            height: 290px; 
            min-height: 290px;
            max-height: 290px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Admin Dashboard</h1>
            <form method="POST" action="/admin/logout">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
        <div class="alert alert-success">
            Welcome, {{ $admin->name }}!
        </div>
        <div class="row mb-10">
            <div class="col-md-4">
                <div class="card text-bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">My Wallet Balance</h5>
                        <p class="card-text display-6">{{ number_format($adminBalance, 2) }} EGP</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Held Amount</h5>
                        <p class="card-text display-6">{{ number_format($adminHeldAmount, 2) }} EGP</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">My Total Withdrawals</h5>
                        <p class="card-text display-6">{{ number_format($adminTotalWithdrawals, 2) }} EGP</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Top-ups</h5>
                        <p class="card-text display-6">{{ $topupCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Withdrawals</h5>
                        <p class="card-text display-6">{{ $withdrawalCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pending Top-ups</h5>
                        <p class="card-text display-6">{{ count($pendingTopups) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pending Withdrawals</h5>
                        <p class="card-text display-6">{{ count($pendingWithdrawals) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Referral Users</h5>
                        <p class="card-text display-6">{{ $referralUsersCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-6">{{ \App\Models\User::count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-dark mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Admins</h5>
                        <p class="card-text display-6">{{ \App\Models\Admin::count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-light mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Wallets</h5>
                        <p class="card-text display-6">{{ \App\Models\Wallet::count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <x-pending-requests-table 
                    title="Pending Top-up Requests"
                    color="warning"
                    :requests="$pendingTopups"
                    :currentAdmin="$admin"
                    approveRoute="/admin/top-up-requests/{id}/approve"
                    rejectRoute="/admin/top-up-requests/{id}/reject"
                    pageParam="topup_page"
                />
            </div>
            <div class="col-md-6">
                <x-pending-requests-table 
                    title="Pending Withdrawal Requests"
                    color="danger"
                    :requests="$pendingWithdrawals"
                    :currentAdmin="$admin"
                    approveRoute="/admin/withdrawal-requests/{id}/approve"
                    rejectRoute="/admin/withdrawal-requests/{id}/reject"
                    pageParam="withdrawal_page"
                />
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">Request Withdrawal</div>
            <div class="card-body">
                <form method="POST" action="/admin/withdrawal-requests">
                    @csrf
                    <div class="mb-3">
                        <label for="withdrawal_amount" class="form-label">Amount</label>
                        <input type="number" min="1" class="form-control" id="withdrawal_amount" name="amount" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Request Withdrawal</button>
                </form>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">Referral Code Management</div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Generate New Referral Code</h6>
                        <p class="text-muted">Generate a new referral code. This will deactivate your previous code.</p>
                        <form method="POST" action="/admin/referral-codes/generate">
                            @csrf
                            <button type="submit" class="btn btn-success">Generate New Code</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h6>Current Active Code</h6>
                        @php
                            $currentCode = \App\Models\ReferralCode::getLatestForOwner($admin);
                        @endphp
                        @if($currentCode)
                            <div class="alert alert-info">
                                <strong>Code:</strong> {{ $currentCode->code }}<br>
                                <small>Generated: {{ $currentCode->created_at->format('Y-m-d H:i') }}</small>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No active referral code found. Generate one to get started.
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h6>Users Registered Through Referral Codes</h6>
                        <div class="table-responsive min-table-wrapper">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Email</th>
                                        <th>Referral Code Owner</th>
                                        <th>Referral Code Used</th>
                                        <th>Referral Bonus Received</th>
                                        <th>Registration Date</th>
                                        <th>Current Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 0; @endphp
                                    @foreach($referralUsersPaginated as $user)
                                        @php
                                            $referralTransaction = $user->wallet->transactions->where('type', 'referral_bonus')->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->referrer_name)
                                                    <span class="badge bg-primary">{{ $user->referrer_name }}</span>
                                                    @if($user->referrer_type)
                                                        <br><small class="text-muted">{{ $user->referrer_type }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->referral_code_used)
                                                    <span class="badge bg-warning text-dark">{{ $user->referral_code_used }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ number_format($referralTransaction->amount ?? 0, 2) }} EGP
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ number_format($user->wallet->balance, 2) }} EGP
                                                </span>
                                            </td>
                                        </tr>
                                        @php $count++; @endphp
                                    @endforeach
                                    @for($i = $count; $i < 5; $i++)
                                        <tr><td colspan="7">&nbsp;</td></tr>
                                    @endfor
                                    @if($count === 0)
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                No users have registered through referral codes yet.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if($referralUsersPaginated->hasPages())
                            <div class="d-flex flex-column align-items-center my-3">
                                <div class="mb-1 small text-muted">
                                    Showing {{ $referralUsersPaginated->firstItem() }}–{{ $referralUsersPaginated->lastItem() }} of {{ $referralUsersPaginated->total() }}
                                </div>
                                <nav>
                                    {{ $referralUsersPaginated->appends(request()->except('referral_page'))->onEachSide(1)->links('pagination::bootstrap-5') }}
                                </nav>
                            </div>
                        @endif
                        @if($referralUsersPaginated->count() > 0)
                            <div class="mt-2">
                                <small class="text-muted">
                                    Total users registered through referrals: <strong>{{ $referralUsersPaginated->total() }}</strong>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><strong> Sent Email Notifications</strong> <span class="badge bg-info">{{ $unreadCount }} </span></span>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <li class="list-group-item @if(is_null($notification->read_at)) list-group-item @endif">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @php
                                    $notificationType = class_basename($notification->type);
                                    $requesterName = '';
                                    $requesterType = '';
                                    
                                    if ($notificationType === 'NewTopUpRequest') {
                                        $displayMessage = '💸 Top Up';
                                        $requesterName = $notification->data['user_name'] ?? 'Unknown User';
                                        $requesterType = 'User';
                                    } elseif ($notificationType === 'NewWithdrawalRequest') {
                                        $displayMessage = '💳 Withdraw Request';
                                        $requesterName = $notification->data['requester_name'] ?? 'Unknown';
                                        $requesterType = $notification->data['requester_type'] ?? 'Unknown';
                                    } else {
                                        $displayMessage = $notification->data['message'] ?? $notificationType;
                                    }
                                @endphp
                                
                                <strong>{{ $displayMessage }}</strong>
                                @if($requesterName && $requesterType)
                                    <br>
                                    <span class="text-muted">{{ ucfirst($requesterType) }}: {{ $requesterName }}</span>
                                @endif
                                <br>
                                <small class="text-muted">
                                    {{ $notification->created_at->format('M j, Y g:i A') }}
                                </small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">No notifications found.</li>
                @endforelse
            </ul>
            @if($notifications->hasPages())
                <div class="d-flex flex-column align-items-center my-3">
                    <div class="mb-1 small text-muted">
                        Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} of {{ $notifications->total() }}
                    </div>
                    <nav>
                        {{ $notifications->appends(request()->except('notification_page'))->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            @endif
        </div>
    </div>
</body>
</html> 