<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Mosh Excel Export Streamer</title>
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
                <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                <a class="nav-link active" href="{{ route('orders.index') }}">Orders</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Orders ({{ $orders->total() }} total)</h2>
            <div class="btn-group" role="group">
                <a href="{{ route('export.orders.details') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Export Orders + Details
                </a>
                <a href="{{ route('export.orders.items') }}" class="btn btn-primary">
                    <i class="bi bi-download"></i> Export Order Items (XLSX)
                </a>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        Advanced Exports
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('export.orders.progress') }}">With Progress Headers</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.orders.items') }}">Detailed Order Items</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('export.custom.data') }}">System Metrics Report</a></li>
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
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Shipped</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><code>{{ $order->order_number }}</code></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('processing')
                                                <span class="badge bg-info">Processing</span>
                                                @break
                                            @case('shipped')
                                                <span class="badge bg-primary">Shipped</span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success">Delivered</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @if($order->shipped_at)
                                            {{ $order->shipped_at->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not shipped</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No orders found. Run <code>php artisan db:seed</code> to create test data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>

        <!-- Export Testing Cards -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-receipt-cutoff"></i> Order Export Types</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('export.orders.details') }}" class="btn btn-outline-success">
                                <i class="bi bi-table"></i> Orders with Customer Details (CSV)
                            </a>
                            <a href="{{ route('export.orders.items') }}" class="btn btn-outline-primary">
                                <i class="bi bi-list-check"></i> Order Items with Joins (XLSX)
                            </a>
                            <a href="{{ route('export.orders.progress') }}" class="btn btn-outline-info">
                                <i class="bi bi-info-circle"></i> Orders with Progress Info
                            </a>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <strong>Note:</strong> These exports demonstrate different types of data relationships and complex queries.
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-bar-chart"></i> Reports & Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('export.custom.data') }}" class="btn btn-outline-warning">
                                <i class="bi bi-graph-up"></i> System Metrics Report
                            </a>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle w-100" data-bs-toggle="dropdown">
                                    <i class="bi bi-calendar-range"></i> Date Range Reports
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li><h6 class="dropdown-header">Quick Ranges</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('export.orders.details') }}?from_date={{ now()->subDays(7)->format('Y-m-d') }}">Last 7 Days</a></li>
                                    <li><a class="dropdown-item" href="{{ route('export.orders.details') }}?from_date={{ now()->subDays(30)->format('Y-m-d') }}">Last 30 Days</a></li>
                                    <li><a class="dropdown-item" href="{{ route('export.orders.details') }}?from_date={{ now()->subDays(90)->format('Y-m-d') }}">Last 3 Months</a></li>
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <strong>Custom Data:</strong> These exports use array data and custom transformations.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Filter Testing -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-funnel"></i> Filter Testing</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Test exports with different order statuses (simulated filtering):</p>
                <div class="row">
                    <div class="col-md-6">
                        <h6>By Status</h6>
                        <div class="btn-group-vertical w-100" role="group">
                            <a href="{{ route('export.orders.details') }}?status=pending" class="btn btn-outline-warning btn-sm">Pending Orders</a>
                            <a href="{{ route('export.orders.details') }}?status=shipped" class="btn btn-outline-primary btn-sm">Shipped Orders</a>
                            <a href="{{ route('export.orders.details') }}?status=delivered" class="btn btn-outline-success btn-sm">Delivered Orders</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Advanced Exports</h6>
                        <div class="btn-group-vertical w-100" role="group">
                            <a href="{{ route('export.orders.items') }}?format=xlsx" class="btn btn-outline-info btn-sm">Detailed Items (XLSX)</a>
                            <a href="{{ route('export.orders.progress') }}" class="btn btn-outline-secondary btn-sm">With Headers & Progress</a>
                            <a href="{{ route('export.orders.details') }}?chunk_size=2000" class="btn btn-outline-danger btn-sm">Large Chunks (2000)</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complex Query Info -->
        <div class="alert alert-info mt-4">
            <h6><i class="bi bi-info-circle"></i> What This Tests</h6>
            <ul class="mb-0">
                <li><strong>Order Details Export:</strong> Tests joins between orders and users tables</li>
                <li><strong>Order Items Export:</strong> Tests complex 3-table joins (orders, users, products, order_items)</li>
                <li><strong>Progress Export:</strong> Tests custom headers and metadata in exports</li>
                <li><strong>Custom Data:</strong> Tests exporting calculated/aggregated data using arrays</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add loading states to export buttons
        document.querySelectorAll('a[href*="export"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Exporting...';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 3000);
            });
        });
    </script>
</body>
</html>