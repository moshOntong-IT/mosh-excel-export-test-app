<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Mosh Excel Export Streamer</title>
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
                <a class="nav-link active" href="{{ route('products.index') }}">Products</a>
                <a class="nav-link" href="{{ route('orders.index') }}">Orders</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-box-seam"></i> Products ({{ $products->total() }} total)</h2>
            <div class="btn-group" role="group">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Export by Category
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('export.products.category', ['category' => 'Electronics']) }}">Electronics</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.products.category', ['category' => 'Books']) }}">Books</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.products.category', ['category' => 'Clothing']) }}">Clothing</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.products.category', ['category' => 'Sports']) }}">Sports</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.products.category', ['category' => 'Home & Garden']) }}">Home & Garden</a></li>
                    </ul>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i> Custom Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('export.products.custom', ['format' => 'csv', 'chunk_size' => 500]) }}">CSV (500 chunks)</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.products.custom', ['format' => 'xlsx', 'chunk_size' => 1000]) }}">XLSX (1000 chunks)</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('export.products.custom', ['chunk_size' => 100]) }}">Small Chunks (100)</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.products.custom', ['chunk_size' => 5000]) }}">Large Chunks (5000)</a></li>
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
                                <th>Category</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="{{ !$product->is_active ? 'table-secondary' : '' }}">
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->category }}</span>
                                    </td>
                                    <td><code>{{ $product->sku }}</code></td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>
                                        @if($product->stock_quantity > 10)
                                            <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                        @elseif($product->stock_quantity > 0)
                                            <span class="badge bg-warning">{{ $product->stock_quantity }}</span>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No products found. Run <code>php artisan db:seed</code> to create test data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>

        <!-- Export Testing Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-funnel"></i> Category Export Testing</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $categories = ['Electronics', 'Books', 'Clothing', 'Sports', 'Home & Garden', 'Toys', 'Automotive', 'Health & Beauty'];
                    @endphp
                    @foreach($categories as $category)
                        <div class="col-md-3 mb-2">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('export.products.category', ['category' => $category, 'format' => 'csv']) }}" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-filetype-csv"></i> {{ $category }}
                                </a>
                                <a href="{{ route('export.products.category', ['category' => $category, 'format' => 'xlsx']) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-filetype-xlsx"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Performance Testing Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-speedometer2"></i> Performance Testing</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Test different chunk sizes to see how they affect export performance:</p>
                <div class="row">
                    <div class="col-md-4">
                        <h6>Small Chunks (Memory Efficient)</h6>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 50]) }}" class="btn btn-outline-info btn-sm me-1">50</a>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 100]) }}" class="btn btn-outline-info btn-sm me-1">100</a>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 250]) }}" class="btn btn-outline-info btn-sm">250</a>
                    </div>
                    <div class="col-md-4">
                        <h6>Medium Chunks (Balanced)</h6>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 500]) }}" class="btn btn-outline-warning btn-sm me-1">500</a>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 1000]) }}" class="btn btn-outline-warning btn-sm me-1">1000</a>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 2000]) }}" class="btn btn-outline-warning btn-sm">2000</a>
                    </div>
                    <div class="col-md-4">
                        <h6>Large Chunks (Performance)</h6>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 5000]) }}" class="btn btn-outline-danger btn-sm me-1">5000</a>
                        <a href="{{ route('export.products.custom', ['chunk_size' => 10000]) }}" class="btn btn-outline-danger btn-sm">10000</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add click handlers for export buttons
        document.querySelectorAll('a[href*="export"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Exporting...';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
    </script>
</body>
</html>