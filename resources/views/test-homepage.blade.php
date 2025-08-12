<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mosh Excel Export Streamer - Test Interface</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .export-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .export-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .export-btn {
            margin: 2px;
        }
        .demo-section {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
        }
        .memory-section {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-file-earmark-excel"></i> 
                Mosh Excel Export Streamer
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                <a class="nav-link" href="{{ route('orders.index') }}">Orders</a>
                <a class="nav-link" href="/api/stats" target="_blank">API Stats</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="jumbotron bg-primary text-white p-4 rounded">
                    <h1 class="display-4">🚀 Excel Export Streamer Test Suite</h1>
                    <p class="lead">Test the memory-efficient streaming Excel export plugin with various scenarios</p>
                    <hr class="my-4">
                    <p>This test suite demonstrates all plugin features including CSV/XLSX exports, chunked processing, and memory-efficient streaming.</p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-people-fill h2"></i>
                        <h5>{{ number_format($stats['users']) }}</h5>
                        <small>Users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-box-seam h2"></i>
                        <h5>{{ number_format($stats['products']) }}</h5>
                        <small>Products</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-receipt h2"></i>
                        <h5>{{ number_format($stats['orders']) }}</h5>
                        <small>Orders</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-list-ul h2"></i>
                        <h5>{{ number_format($stats['order_items']) }}</h5>
                        <small>Order Items</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-check-circle h2"></i>
                        <h5>{{ number_format($stats['verified_users']) }}</h5>
                        <small>Verified</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card text-center">
                    <div class="card-body">
                        <i class="bi bi-toggle-on h2"></i>
                        <h5>{{ number_format($stats['active_products']) }}</h5>
                        <small>Active</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demo Exports -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card export-card demo-section">
                    <div class="card-header">
                        <h4><i class="bi bi-play-circle"></i> Quick Demo Exports</h4>
                        <small class="text-muted">Perfect for initial testing and demonstrations</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>User Exports</h6>
                                <a href="{{ route('export.users.csv') }}" class="btn btn-success export-btn">
                                    <i class="bi bi-file-earmark-text"></i> All Users (CSV)
                                </a>
                                <a href="{{ route('export.users.excel') }}" class="btn btn-primary export-btn">
                                    <i class="bi bi-file-earmark-excel"></i> Verified Users (XLSX)
                                </a>
                                <a href="{{ route('export.users.interface') }}" class="btn btn-info export-btn">
                                    <i class="bi bi-gear"></i> Using Interface
                                </a>
                            </div>
                            <div class="col-md-4">
                                <h6>Product Exports</h6>
                                <a href="{{ route('export.products.category', ['category' => 'Electronics', 'format' => 'csv']) }}" class="btn btn-success export-btn">
                                    <i class="bi bi-cpu"></i> Electronics (CSV)
                                </a>
                                <a href="{{ route('export.products.category', ['category' => 'Books', 'format' => 'xlsx']) }}" class="btn btn-primary export-btn">
                                    <i class="bi bi-book"></i> Books (XLSX)
                                </a>
                                <a href="{{ route('export.products.custom', ['chunk_size' => 500, 'format' => 'csv']) }}" class="btn btn-warning export-btn">
                                    <i class="bi bi-sliders"></i> Custom Settings
                                </a>
                            </div>
                            <div class="col-md-4">
                                <h6>Order Exports</h6>
                                <a href="{{ route('export.orders.details') }}" class="btn btn-success export-btn">
                                    <i class="bi bi-receipt-cutoff"></i> Orders + Details
                                </a>
                                <a href="{{ route('export.orders.items') }}" class="btn btn-primary export-btn">
                                    <i class="bi bi-list-check"></i> Order Items (XLSX)
                                </a>
                                <a href="{{ route('export.custom.data') }}" class="btn btn-secondary export-btn">
                                    <i class="bi bi-bar-chart"></i> System Metrics
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Multi-Sheet Exports (NEW!) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card export-card" style="background: #e8f5e8; border-left: 4px solid #28a745;">
                    <div class="card-header">
                        <h4><i class="bi bi-layers"></i> Multi-Sheet XLSX Exports ⭐ NEW!</h4>
                        <small class="text-muted">Create XLSX files with multiple tabs/sheets - Perfect for comprehensive reports</small>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="bi bi-lightbulb"></i>
                            <strong>Feature:</strong> The <code>streamWrapAsSheets()</code> method combines multiple data sources into a single XLSX file with separate tabs. Each sheet can have different queries and columns!
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Basic Multi-Sheet</h6>
                                <a href="{{ route('export.multisheet.basic') }}" class="btn btn-success export-btn">
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Users + Products + Orders
                                </a>
                                <small class="d-block text-muted">3 sheets: User data, active products, and order summaries</small>
                            </div>
                            <div class="col-md-6">
                                <h6>Advanced Multi-Sheet</h6>
                                <a href="{{ route('export.multisheet.complex') }}" class="btn btn-primary export-btn">
                                    <i class="bi bi-diagram-3"></i> Complex Report
                                </a>
                                <small class="d-block text-muted">4 sheets: Filtered users, electronics, recent orders, and sales summary</small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="bg-light p-3 rounded">
                                    <h6><i class="bi bi-code-square"></i> API Example:</h6>
                                    <pre class="small mb-0"><code>$exporter->streamWrapAsSheets([
    'Users' => ['query' => User::query(), 'columns' => ['name', 'email']],
    'Products' => ['query' => Product::where('active', true), 'columns' => ['name', 'price']],
    'Orders' => ['query' => Order::with('user'), 'columns' => ['order_number', 'total']]
], 'multi-report.xlsx');</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Memory Testing -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card export-card memory-section">
                    <div class="card-header">
                        <h4><i class="bi bi-memory"></i> Memory Efficiency Testing</h4>
                        <small class="text-muted">Test streaming with large datasets without memory issues</small>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Note:</strong> These exports may take longer but should NOT cause memory issues regardless of dataset size.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Large Dataset Exports</h6>
                                <a href="{{ route('export.large.dataset') }}" class="btn btn-danger export-btn">
                                    <i class="bi bi-database"></i> All Users (Large Dataset)
                                </a>
                                <a href="{{ route('export.orders.progress') }}" class="btn btn-warning export-btn">
                                    <i class="bi bi-speedometer2"></i> With Progress Info
                                </a>
                            </div>
                            <div class="col-md-6">
                                <h6>Custom Chunk Sizes</h6>
                                <a href="{{ route('export.products.custom', ['chunk_size' => 100]) }}" class="btn btn-outline-primary export-btn">
                                    Small Chunks (100)
                                </a>
                                <a href="{{ route('export.products.custom', ['chunk_size' => 2000]) }}" class="btn btn-outline-success export-btn">
                                    Large Chunks (2000)
                                </a>
                                <a href="{{ route('export.products.custom', ['chunk_size' => 5000]) }}" class="btn btn-outline-danger export-btn">
                                    XL Chunks (5000)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Features -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card export-card">
                    <div class="card-header">
                        <h4><i class="bi bi-gear-wide-connected"></i> Advanced Features</h4>
                        <small class="text-muted">Filtered exports, custom formats, and complex queries</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Filtered Exports</h6>
                                <a href="{{ route('export.users.filtered', ['verified' => 1, 'format' => 'csv']) }}" class="btn btn-outline-success export-btn">
                                    <i class="bi bi-funnel"></i> Verified Users Only
                                </a>
                                <a href="{{ route('export.users.filtered', ['from_date' => now()->subDays(30)->format('Y-m-d')]) }}" class="btn btn-outline-info export-btn">
                                    <i class="bi bi-calendar"></i> Last 30 Days
                                </a>
                                <a href="{{ route('export.products.category', ['category' => 'Sports']) }}" class="btn btn-outline-warning export-btn">
                                    <i class="bi bi-trophy"></i> Sports Products
                                </a>
                            </div>
                            <div class="col-md-6">
                                <h6>Format Testing</h6>
                                <a href="{{ route('export.users.csv') }}" class="btn btn-outline-success export-btn">
                                    <i class="bi bi-filetype-csv"></i> CSV Format
                                </a>
                                <a href="{{ route('export.users.excel') }}" class="btn btn-outline-primary export-btn">
                                    <i class="bi bi-filetype-xlsx"></i> XLSX Format
                                </a>
                                <a href="{{ route('export.orders.items') }}" class="btn btn-outline-secondary export-btn">
                                    <i class="bi bi-diagram-3"></i> Complex Joins
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-info-circle"></i> Testing Instructions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🚀 Getting Started</h6>
                                <ol>
                                    <li>Click any export button above</li>
                                    <li>Your browser will start downloading the file</li>
                                    <li>Open the downloaded file to verify data</li>
                                    <li>Check browser network tab for streaming behavior</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h6>🔍 What to Test</h6>
                                <ul>
                                    <li><strong>Memory Usage:</strong> Try large dataset exports</li>
                                    <li><strong>File Formats:</strong> Test both CSV and XLSX</li>
                                    <li><strong>Browser Downloads:</strong> Verify files download correctly</li>
                                    <li><strong>Data Integrity:</strong> Check exported data accuracy</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3 mt-5">
        <p>&copy; 2024 Mosh Excel Export Streamer Plugin - Memory Efficient Laravel Excel Exports</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add loading states to export buttons
        document.querySelectorAll('.export-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Exporting...';
                this.disabled = true;
                
                // Re-enable after 3 seconds (export should start by then)
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 3000);
            });
        });

        // Show memory usage info
        const memoryInfo = `Current Memory: ${(performance.memory?.usedJSHeapSize / 1024 / 1024).toFixed(2) || 'N/A'} MB`;
        console.log('🔧 Plugin Test Suite Loaded');
        console.log('💾 ' + memoryInfo);
    </script>
</body>
</html>