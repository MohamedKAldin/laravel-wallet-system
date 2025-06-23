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
                        <p class="card-text display-6">{{ $pendingTopups }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pending Withdrawals</h5>
                        <p class="card-text display-6">{{ $pendingWithdrawals }}</p>
                    </div>
                </div>
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
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
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