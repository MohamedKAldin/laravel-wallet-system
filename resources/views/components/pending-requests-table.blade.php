@props([
    'title' => 'Pending Requests',
    'color' => 'warning',
    'requests' => [],
    'currentAdmin' => null,
    'approveRoute' => '',
    'rejectRoute' => '',
    'pageParam' => 'page'
])

<div class="card">
    <div class="card-header bg-{{ $color }} text-{{ $color === 'warning' ? 'dark' : 'white' }}">{{ $title }}</div>
    <div class="card-body p-0 min-table-wrapper">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Requester</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $count = 0; @endphp
                @foreach($requests as $request)
                    <tr>
                        <td>
                            @php
                                $wallet = $request->wallet;
                                $owner = $wallet->user ?? $wallet->admin;
                                $ownerType = $wallet->user ? 'User' : 'Admin';
                            @endphp
                            <div>
                                <strong>{{ $owner->name ?? '-' }}</strong>
                                <!-- <br> <small class="text-muted">{{ $ownerType }}</small> -->
                            </div>
                        </td>
                        <td>{{ $request->amount }} EGP</td>
                        <td>{{ $request->created_at->diffForHumans() }}</td>
                        <td>
                            @php $isOwnRequest = $request->isCreatedByAdmin($currentAdmin); @endphp
                            @can('update', $request)
                                <form method="POST" action="{{ str_replace('{id}', $request->id, $approveRoute) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm" 
                                            {{ $isOwnRequest ? 'disabled' : '' }}
                                            title="{{ $isOwnRequest ? 'You cannot approve your own request' : 'Approve this request' }}">
                                        Accept
                                    </button>
                                </form>
                            @endcan
                            @can('update', $request)
                                <form method="POST" action="{{ str_replace('{id}', $request->id, $rejectRoute) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-danger btn-sm" 
                                            {{ $isOwnRequest ? 'disabled' : '' }}
                                            title="{{ $isOwnRequest ? 'You cannot reject your own request' : 'Reject this request' }}">
                                        Reject
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @php $count++; @endphp
                @endforeach
                @for($i = $count; $i < 5; $i++)
                    <tr><td colspan="4">&nbsp;</td></tr>
                @endfor
                @if($count === 0)
                    <tr><td colspan="4" class="text-center">No pending {{ strtolower(str_replace('Pending ', '', $title)) }}.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
        <div class="d-flex flex-column align-items-center my-3">
            <div class="mb-1 small text-muted">
                Showing {{ $requests->firstItem() }}–{{ $requests->lastItem() }} of {{ $requests->total() }}
            </div>
            <nav>
                {{ $requests->appends(request()->except($pageParam))->onEachSide(1)->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    @endif
</div> 