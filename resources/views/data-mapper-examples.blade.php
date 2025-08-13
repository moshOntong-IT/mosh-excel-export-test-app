<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mapper Examples - Excel Export Streamer</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .section {
            background: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .example-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .example-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            background: #fafafa;
        }
        .example-card h3 {
            color: #495057;
            margin-top: 0;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }
        .code-block {
            background: #282c34;
            color: #abb2bf;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            margin: 1rem 0;
        }
        .download-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem 0.5rem 0.5rem 0;
            transition: background 0.3s;
        }
        .download-btn:hover {
            background: #218838;
        }
        .download-btn.xlsx {
            background: #007bff;
        }
        .download-btn.xlsx:hover {
            background: #0056b3;
        }
        .feature-highlight {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 1rem;
            margin: 1rem 0;
        }
        .performance-note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin: 1rem 0;
        }
        .nav-links {
            text-align: center;
            margin-bottom: 2rem;
        }
        .nav-links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔧 Data Mapper Examples</h1>
        <p>Transform complex data row-by-row during streaming exports</p>
        <p><strong>Memory Efficient • Flexible • Error Resilient</strong></p>
    </div>

    <div class="nav-links">
        <a href="/">← Back to Home</a>
        <a href="/users">Users Export</a>
        <a href="/products">Products Export</a>
        <a href="/orders">Orders Export</a>
    </div>

    <div class="section">
        <h2>🚀 What is Data Mapper?</h2>
        <p>Data Mapper allows you to transform data <strong>row-by-row during streaming</strong>, enabling complex calculations and transformations without loading the entire dataset into memory.</p>
        
        <div class="feature-highlight">
            <strong>Key Benefits:</strong>
            <ul>
                <li><strong>Memory Efficient:</strong> Transforms data during streaming (no memory bloat)</li>
                <li><strong>Flexible:</strong> Handle complex calculations, relationships, and custom formatting</li>
                <li><strong>Error Resilient:</strong> Automatic fallback to default column extraction</li>
                <li><strong>Backward Compatible:</strong> Optional parameter, existing code continues working</li>
            </ul>
        </div>
    </div>

    <div class="section">
        <h2>📊 Live Examples</h2>
        
        <div class="example-grid">
            <div class="example-card">
                <h3>💰 Financial Report</h3>
                <p>Export orders with complex revenue and profit calculations using relationships.</p>
                
                <div class="code-block">
function($order) {
    $revenue = $order->items->sum(fn($item) => 
        $item->quantity * $item->price);
    $cost = $order->items->sum(fn($item) => 
        $item->quantity * $item->product->cost);
    $profit = $revenue - $cost;
    
    return [
        $order->order_number,
        $order->customer->name,
        $order->items->count(),
        '$' . number_format($revenue, 2),
        '$' . number_format($profit, 2),
        number_format($profit / $revenue * 100, 1) . '%'
    ];
}
                </div>
                
                <a href="/export/orders-financial-report?format=csv" class="download-btn">Download CSV</a>
                <a href="/export/orders-financial-report?format=xlsx" class="download-btn xlsx">Download XLSX</a>
                
                <div class="performance-note">
                    <strong>Performance:</strong> Processes relationships efficiently during streaming - no pre-calculation needed!
                </div>
            </div>

            <div class="example-card">
                <h3>📈 Customer Summary</h3>
                <p>Aggregate customer data with order statistics and lifetime value calculations.</p>
                
                <div class="code-block">
function($customer) {
    $orders = $customer->orders;
    $totalSpent = $orders->sum('total');
    $avgOrderValue = $orders->avg('total');
    $lastOrder = $orders->sortByDesc('created_at')->first();
    
    return [
        $customer->name,
        $customer->email,
        $orders->count(),
        '$' . number_format($totalSpent, 2),
        '$' . number_format($avgOrderValue, 2),
        $lastOrder ? $lastOrder->created_at->format('Y-m-d') : 'Never'
    ];
}
                </div>
                
                <a href="/export/customer-summary?format=csv" class="download-btn">Download CSV</a>
                <a href="/export/customer-summary?format=xlsx" class="download-btn xlsx">Download XLSX</a>
            </div>

            <div class="example-card">
                <h3>📦 Product Performance</h3>
                <p>Export products with sales metrics, inventory calculations, and performance indicators.</p>
                
                <div class="code-block">
function($product) {
    $orderItems = $product->orderItems;
    $totalSold = $orderItems->sum('quantity');
    $revenue = $orderItems->sum(fn($item) => 
        $item->quantity * $item->price);
    $avgPrice = $orderItems->avg('price');
    
    return [
        $product->name,
        $product->price,
        $product->stock_quantity,
        $totalSold,
        '$' . number_format($revenue, 2),
        '$' . number_format($avgPrice, 2),
        $totalSold > 50 ? 'High Performer' : 'Standard'
    ];
}
                </div>
                
                <a href="/export/product-performance?format=csv" class="download-btn">Download CSV</a>
                <a href="/export/product-performance?format=xlsx" class="download-btn xlsx">Download XLSX</a>
            </div>

            <div class="example-card">
                <h3>🛡️ Error Handling Demo</h3>
                <p>Demonstrate data mapper error resilience with automatic fallback behavior.</p>
                
                <div class="code-block">
function($order) {
    // Intentionally cause some errors to show fallback
    if ($order->id % 10 === 0) {
        throw new Exception('Demo error for order ' . $order->id);
    }
    
    return [
        $order->order_number,
        $order->customer_name,
        $order->total,
        'Processed successfully'
    ];
}
                </div>
                
                <a href="/export/error-handling-demo?format=csv" class="download-btn">Download CSV</a>
                <a href="/export/error-handling-demo?format=xlsx" class="download-btn xlsx">Download XLSX</a>
                
                <div class="performance-note">
                    <strong>Note:</strong> When mapper fails, automatic fallback to default column extraction ensures export continues.
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>⚡ Performance Comparison</h2>
        <p>Compare data mapper performance against traditional approaches:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 1rem; border: 1px solid #dee2e6;">Approach</th>
                    <th style="padding: 1rem; border: 1px solid #dee2e6;">Memory Usage</th>
                    <th style="padding: 1rem; border: 1px solid #dee2e6;">Performance</th>
                    <th style="padding: 1rem; border: 1px solid #dee2e6;">Flexibility</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;"><strong>Data Mapper (CSV)</strong></td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟢 Constant (streaming)</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟢 Excellent</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟢 High</td>
                </tr>
                <tr>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;"><strong>Data Mapper (XLSX)</strong></td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟡 Optimized (temp files)</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟡 Good</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟢 High</td>
                </tr>
                <tr>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">Pre-process Array</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🔴 High (entire dataset)</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🔴 Poor</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟡 Medium</td>
                </tr>
                <tr>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">Simple Columns</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟢 Low</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🟢 Excellent</td>
                    <td style="padding: 1rem; border: 1px solid #dee2e6;">🔴 Limited</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>📝 How to Use</h2>
        <p>Adding data mapper to your exports is simple:</p>
        
        <div class="code-block">
// Basic usage
$exporter->streamFromQuery(
    $query,
    $headers,
    $filename,
    ['format' => 'csv'],
    function($record) {
        return [
            // Your custom transformations here
            $record->field1,
            number_format($record->calculation, 2),
            $record->relationship->field,
        ];
    }
);

// The callback receives the full Eloquent model
// Return array of values matching your headers
// Automatic error handling with fallback
        </div>
    </div>

    <script>
        // Add some interactivity
        document.querySelectorAll('.download-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                this.style.opacity = '0.7';
                this.textContent = 'Downloading...';
                
                setTimeout(() => {
                    this.style.opacity = '1';
                    this.textContent = this.textContent.replace('Downloading...', this.href.includes('csv') ? 'Download CSV' : 'Download XLSX');
                }, 2000);
            });
        });
    </script>
</body>
</html>