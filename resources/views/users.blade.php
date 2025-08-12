<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Mosh Excel Export Streamer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-file-earmark-excel"></i> 
                Excel Export Streamer
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Home</a>
                <a class="nav-link active" href="{{ route('users.index') }}">Users</a>
                <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                <a class="nav-link" href="{{ route('orders.index') }}">Orders</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Users ({{ $users->total() }} total)</h2>
            <div class="btn-group" role="group">
                <a href="{{ route('export.users.csv') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Export CSV
                </a>
                <a href="{{ route('export.users.excel') }}" class="btn btn-primary">
                    <i class="bi bi-download"></i> Export Excel
                </a>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        More Exports
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('export.users.interface') }}">Using Interface</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.users.filtered', ['verified' => 1]) }}">Verified Only</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.users.filtered', ['verified' => 0]) }}">Unverified Only</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('export.large.dataset') }}">Large Dataset Test</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Verified</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No users found. Run <code>php artisan db:seed</code> to create test data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Export Testing Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-gear"></i> Export Testing Options</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Filter Options</h6>
                        <div class="mb-3">
                            <form action="{{ route('export.users.filtered') }}" method="GET" class="d-inline">
                                <input type="date" name="from_date" class="form-control form-control-sm d-inline-block" style="width: auto;" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                                <input type="date" name="to_date" class="form-control form-control-sm d-inline-block" style="width: auto;" value="{{ now()->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Export Date Range</button>
                            </form>
                        </div>
                        <div class="mb-3">
                            <form action="{{ route('export.users.filtered') }}" method="GET" class="d-inline">
                                <select name="format" class="form-select form-select-sm d-inline-block" style="width: auto;">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">XLSX</option>
                                </select>
                                <input type="hidden" name="verified" value="1">
                                <button type="submit" class="btn btn-sm btn-outline-success">Export Verified Users</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Performance Testing</h6>
                        <p class="small text-muted">Test different chunk sizes for export performance:</p>
                        <a href="{{ route('export.users.filtered', ['chunk_size' => 100]) }}" class="btn btn-sm btn-outline-warning me-1">Small Chunks</a>
                        <a href="{{ route('export.users.filtered', ['chunk_size' => 1000]) }}" class="btn btn-sm btn-outline-info me-1">Default</a>
                        <a href="{{ route('export.users.filtered', ['chunk_size' => 5000]) }}" class="btn btn-sm btn-outline-danger">Large Chunks</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>