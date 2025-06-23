<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">Pending Top-up Requests</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingTopups as $request)
                                    <tr>
                                        <td>{{ $request->wallet->user->name ?? '-' }}</td>
                                        <td>{{ $request->amount }} EGP</td>
                                        <td>{{ $request->created_at->diffForHumans() }}</td>
                                        <td>
                                            @can('admin-has-permission', 'can_accept_topup')
                                                <form method="POST" action="/admin/top-up-requests/{{ $request->id }}/approve" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm">Accept</button>
                                                </form>
                                            @endcan
                                            @can('admin-has-permission', 'can_reject_topup')
                                                <form method="POST" action="/admin/top-up-requests/{{ $request->id }}/reject" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-danger btn-sm">Reject</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No pending top-up requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">Pending Withdrawal Requests</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Admin</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingWithdrawals as $request)
                                    <tr>
                                        <td>{{ $request->wallet->admin->name ?? '-' }}</td>
                                        <td>{{ $request->amount }} EGP</td>
                                        <td>{{ $request->created_at->diffForHumans() }}</td>
                                        <td>
                                            @can('admin-has-permission', 'can_accept_withdrawals')
                                                <form method="POST" action="/admin/withdrawal-requests/{{ $request->id }}/approve" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm">Accept</button>
                                                </form>
                                            @endcan
                                            @can('admin-has-permission', 'can_reject_withdrawals')
                                                <form method="POST" action="/admin/withdrawal-requests/{{ $request->id }}/reject" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-danger btn-sm">Reject</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No pending withdrawal requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><strong>Notifications</strong> <span class="badge bg-info">{{ $unreadCount }} unread</span></span>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <li class="list-group-item @if(is_null($notification->read_at)) list-group-item-warning @endif">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if(isset($notification->data['message']))
                                    {{ $notification->data['message'] }}
                                @else
                                    {{ class_basename($notification->type) }}
                                @endif
                                <br>
                                <small class="text-muted">
                                    {{ $notification->created_at->format('Y-m-d H:i') }}
                                    @if(isset($notification->data['status']))
                                        &mdash; 
                                        <span class="badge 
                                            @if($notification->data['status'] === 'approved') bg-success
                                            @elseif($notification->data['status'] === 'rejected') bg-danger
                                            @elseif($notification->data['status'] === 'pending') bg-warning text-dark
                                            @else bg-secondary @endif">
                                            {{ ucfirst($notification->data['status']) }}
                                        </span>
                                    @endif
                                </small>
                            </div>
                            @if(is_null($notification->read_at))
                                <span class="badge bg-warning text-dark">Unread</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">No notifications found.</li>
                @endforelse
            </ul>
        </div>
    </div>
</body>
</html> 