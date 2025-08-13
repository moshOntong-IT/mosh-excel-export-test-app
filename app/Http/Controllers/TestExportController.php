<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mosh\ExcelExportStreamer\Services\ExcelStreamExporter;
use Mosh\ExcelExportStreamer\Services\ChunkedQueryProcessor;

class TestExportController extends Controller
{
    protected ExcelStreamExporter $exporter;

    public function __construct(ExcelStreamExporter $exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * Export all users to CSV
     */
    public function exportUsersCSV()
    {
        return $this->exporter->streamFromQuery(
            User::query()->orderBy('created_at'),
            ['id', 'name', 'email', 'email_verified_at', 'created_at'],
            'all-users.csv'
        );
    }

    /**
     * Export verified users to Excel
     */
    public function exportVerifiedUsersExcel()
    {
        return $this->exporter->streamFromQuery(
            User::whereNotNull('email_verified_at')->orderBy('name'),
            ['id', 'name', 'email', 'created_at'],
            'verified-users.xlsx',
            ['format' => 'xlsx']
        );
    }

    /**
     * Export products by category
     */
    public function exportProductsByCategory(Request $request)
    {
        $category = $request->get('category', 'Electronics');
        $format = $request->get('format', 'csv');
        
        $query = Product::where('category', $category)
                       ->where('is_active', true)
                       ->orderBy('name');

        $filename = "products-{$category}.{$format}";

        return $this->exporter->streamFromQuery(
            $query,
            ['name', 'sku', 'price', 'stock_quantity', 'created_at'],
            $filename,
            ['format' => $format]
        );
    }

    /**
     * Export orders with custom transformation
     */
    public function exportOrdersWithDetails()
    {
        $query = Order::with('user')
                     ->selectRaw('orders.*, users.name as customer_name')
                     ->join('users', 'orders.user_id', '=', 'users.id')
                     ->orderBy('orders.created_at', 'desc');

        return $this->exporter->streamFromQuery(
            $query,
            ['order_number', 'customer_name', 'status', 'total_amount', 'created_at'],
            'orders-with-details.csv'
        );
    }

    /**
     * Export using models that implement ExportableInterface
     */
    public function exportUsersUsingInterface()
    {
        // This will use the transformForExport() method from User model
        $processor = new ChunkedQueryProcessor(
            User::query()->orderBy('id'), 
            [], // Empty means use model's getExportColumns()
            500
        );

        return $this->exporter->streamFromProvider(
            $processor,
            'users-with-interface.xlsx',
            ['format' => 'xlsx']
        );
    }

    /**
     * Export custom array data
     */
    public function exportCustomData()
    {
        $data = [
            ['metric' => 'Total Users', 'value' => User::count(), 'date' => now()->format('Y-m-d')],
            ['metric' => 'Total Products', 'value' => Product::count(), 'date' => now()->format('Y-m-d')],
            ['metric' => 'Total Orders', 'value' => Order::count(), 'date' => now()->format('Y-m-d')],
            ['metric' => 'Active Products', 'value' => Product::where('is_active', true)->count(), 'date' => now()->format('Y-m-d')],
            ['metric' => 'Verified Users', 'value' => User::whereNotNull('email_verified_at')->count(), 'date' => now()->format('Y-m-d')],
        ];

        $headers = ['Metric', 'Value', 'Report Date'];

        return $this->exporter->streamFromArray(
            $data,
            $headers,
            'system-metrics.csv'
        );
    }

    /**
     * Export large dataset - Memory efficiency test
     */
    public function exportLargeDataset()
    {
        $query = User::query()->orderBy('id');
        
        return $this->exporter->streamFromQuery(
            $query,
            ['id', 'name', 'email', 'created_at'],
            'large-user-dataset.csv',
            [
                'chunk_size' => 2000, // Larger chunks for better performance
                'headers' => [
                    'X-Export-Type' => 'Large Dataset',
                    'X-Memory-Efficient' => 'true'
                ]
            ]
        );
    }

    /**
     * Export with custom chunking and error handling
     */
    public function exportWithCustomSettings(Request $request)
    {
        $chunkSize = $request->get('chunk_size', 1000);
        $format = $request->get('format', 'csv');
        
        try {
            return $this->exporter->streamFromQuery(
                Product::query()->orderBy('category')->orderBy('name'),
                ['name', 'category', 'price', 'sku', 'stock_quantity'],
                "products-custom-{$chunkSize}.{$format}",
                [
                    'format' => $format,
                    'chunk_size' => $chunkSize
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Export failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export order items with joins - Complex query test
     */
    public function exportOrderItemsWithJoins()
    {
        $query = OrderItem::query()
            ->select([
                'order_items.id',
                'orders.order_number',
                'users.name as customer_name',
                'users.email as customer_email',
                'products.name as product_name',
                'products.sku',
                'order_items.quantity',
                'order_items.price',
                'order_items.created_at'
            ])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'orders.user_id', '=', 'users.id')  
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->orderBy('order_items.created_at', 'desc');

        return $this->exporter->streamFromQuery(
            $query,
            [
                'order_number', 'customer_name', 'customer_email', 
                'product_name', 'sku', 'quantity', 'price', 'created_at'
            ],
            'order-items-detailed.xlsx',
            ['format' => 'xlsx']
        );
    }

    /**
     * Export filtered data with date ranges
     */
    public function exportUsersFiltered(Request $request)
    {
        $query = User::query();

        // Apply date filters
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Apply verification filter
        if ($request->filled('verified')) {
            if ($request->boolean('verified')) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $format = $request->get('format', 'csv');
        $filename = "filtered-users-" . date('Y-m-d') . ".{$format}";

        return $this->exporter->streamFromQuery(
            $query->orderBy('created_at'),
            ['id', 'name', 'email', 'email_verified_at', 'created_at'],
            $filename,
            ['format' => $format]
        );
    }

    /**
     * Export multiple sheets in a single XLSX file - Multi-sheet demo
     */
    public function exportMultipleSheets()
    {
        return $this->exporter->streamWrapAsSheets([
            'Users' => [
                'query' => User::query()->orderBy('created_at'),
                'columns' => ['id', 'name', 'email', 'created_at']
            ],
            'Products' => [
                'query' => Product::where('is_active', true)->orderBy('name'),
                'columns' => ['id', 'name', 'sku', 'category', 'price', 'stock_quantity']
            ],
            'Orders' => [
                'query' => Order::with('user')->orderBy('created_at', 'desc'),
                'columns' => ['id', 'order_number', 'status', 'total_amount', 'created_at']
            ]
        ], 'multi-sheet-report.xlsx');
    }

    /**
     * Export complex multi-sheet with filtered data
     */
    public function exportComplexMultiSheet()
    {
        return $this->exporter->streamWrapAsSheets([
            'Active Users' => [
                'query' => User::whereNotNull('email_verified_at')->orderBy('name'),
                'columns' => ['name', 'email', 'email_verified_at']
            ],
            'Electronics' => [
                'query' => Product::where('category', 'Electronics')
                                 ->where('is_active', true)
                                 ->orderBy('price', 'desc'),
                'columns' => ['name', 'sku', 'price', 'stock_quantity']
            ],
            'Recent Orders' => [
                'query' => Order::where('created_at', '>=', now()->subDays(30))
                               ->with('user')
                               ->orderBy('created_at', 'desc'),
                'columns' => ['order_number', 'status', 'total_amount', 'created_at']
            ],
            'Order Summary' => [
                'query' => OrderItem::selectRaw('
                    products.name as product_name,
                    SUM(order_items.quantity) as total_sold,
                    SUM(order_items.quantity * order_items.price) as total_revenue,
                    COUNT(DISTINCT orders.id) as order_count
                ')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_revenue', 'desc'),
                'columns' => ['product_name', 'total_sold', 'total_revenue', 'order_count'],
                'options' => ['chunk_size' => 100] // Custom chunk size for complex query
            ]
        ], 'complex-multi-sheet-report.xlsx');
    }

    /**
     * Export with progress tracking info
     */
    public function exportWithProgress()
    {
        $processor = new ChunkedQueryProcessor(
            Order::query()->orderBy('id'),
            ['id', 'order_number', 'status', 'total_amount', 'created_at'],
            1000
        );

        $totalRecords = $processor->getTotalCount();
        
        return $this->exporter->streamFromProvider(
            $processor,
            'orders-with-progress.csv',
            [
                'headers' => [
                    'X-Total-Records' => $totalRecords,
                    'X-Export-Started' => now()->toISOString(),
                    'X-Chunk-Size' => '1000'
                ]
            ]
        );
    }

    // =============================================
    // DATA MAPPER EXAMPLES
    // =============================================

    /**
     * Show data mapper examples page
     */
    public function showDataMapperExamples()
    {
        return view('data-mapper-examples');
    }

    /**
     * Export orders with financial calculations using data mapper
     */
    public function exportOrdersFinancialReport(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = Order::with(['user', 'orderItems.product'])
                     ->where('status', 'completed')
                     ->orderBy('created_at', 'desc');

        $headers = ['Order #', 'Customer', 'Items Count', 'Revenue', 'Cost', 'Profit', 'Margin %', 'Date'];

        return $this->exporter->streamFromQuery(
            $query,
            $headers,
            "orders-financial-report.{$format}",
            ['format' => $format, 'chunk_size' => 500],
            function($order) {
                // Complex financial calculations using relationships
                $items = $order->orderItems;
                $revenue = $items->sum(function($item) {
                    return $item->quantity * $item->price;
                });
                $cost = $items->sum(function($item) {
                    return $item->quantity * ($item->product->cost ?? $item->price * 0.6);
                });
                $profit = $revenue - $cost;
                $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

                return [
                    $order->order_number,
                    $order->user->name,
                    $items->count(),
                    '$' . number_format($revenue, 2),
                    '$' . number_format($cost, 2),
                    '$' . number_format($profit, 2),
                    number_format($margin, 1) . '%',
                    $order->created_at->format('Y-m-d')
                ];
            }
        );
    }

    /**
     * Export customer summary TEST - Limited to 200 records for fast debugging
     */
    public function exportCustomerSummaryTest(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = User::with(['orders.orderItems'])
                    ->whereHas('orders')
                    ->orderBy('name')
                    ->limit(200); // LIMITED FOR TESTING
        

        $headers = ['Name', 'Email', 'Orders Count', 'Total Spent', 'Avg Order Value', 'Last Order', 'Status'];

        return $this->exporter->streamFromQuery(
            $query,
            $headers,
            "customer-summary-test.{$format}",
            ['format' => $format, 'chunk_size' => 100], // Smaller chunks for testing
            function($customer) {
                $orders = $customer->orders;
                $totalSpent = $orders->sum('total_amount');
                $avgOrderValue = $orders->avg('total_amount');
                $lastOrder = $orders->sortByDesc('created_at')->first();

                return [
                    $customer->name,
                    $customer->email,
                    $orders->count(),
                    '$' . number_format($totalSpent, 2),
                    '$' . number_format($avgOrderValue ?? 0, 2),
                    $lastOrder ? $lastOrder->created_at->format('Y-m-d') : 'Never',
                    $totalSpent > 1000 ? 'VIP' : ($totalSpent > 500 ? 'Regular' : 'New')
                ];
            }
        );
    }

    /**
     * Export customer summary with aggregated data using data mapper
     */
    public function exportCustomerSummary(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = User::with(['orders.orderItems'])
                    ->whereHas('orders')
                    ->orderBy('name');

        $headers = ['Name', 'Email', 'Orders Count', 'Total Spent', 'Avg Order Value', 'Last Order', 'Status'];

        return $this->exporter->streamFromQuery(
            $query,
            $headers,
            "customer-summary.{$format}",
            ['format' => $format, 'chunk_size' => 1000],
            function($customer) {
                $orders = $customer->orders;
                $totalSpent = $orders->sum('total_amount');
                $avgOrderValue = $orders->avg('total_amount');
                $lastOrder = $orders->sortByDesc('created_at')->first();

                return [
                    $customer->name,
                    $customer->email,
                    $orders->count(),
                    '$' . number_format($totalSpent, 2),
                    '$' . number_format($avgOrderValue ?? 0, 2),
                    $lastOrder ? $lastOrder->created_at->format('Y-m-d') : 'Never',
                    $totalSpent > 1000 ? 'VIP' : ($totalSpent > 500 ? 'Regular' : 'New')
                ];
            }
        );
    }

    /**
     * Export product performance with sales metrics using data mapper
     */
    public function exportProductPerformance(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = Product::with(['orderItems'])
                       ->where('is_active', true)
                       ->orderBy('name');

        $headers = ['Product', 'SKU', 'Current Price', 'Stock', 'Units Sold', 'Revenue', 'Avg Sale Price', 'Performance'];

        return $this->exporter->streamFromQuery(
            $query,
            $headers,
            "product-performance.{$format}",
            ['format' => $format, 'chunk_size' => 1000],
            function($product) {
                $orderItems = $product->orderItems;
                $totalSold = $orderItems->sum('quantity');
                $revenue = $orderItems->sum(function($item) {
                    return $item->quantity * $item->price;
                });
                $avgSalePrice = $orderItems->avg('price');
                
                $performance = 'Low';
                if ($totalSold > 100) $performance = 'High';
                elseif ($totalSold > 50) $performance = 'Medium';

                return [
                    $product->name,
                    $product->sku,
                    '$' . number_format($product->price, 2),
                    $product->stock_quantity,
                    $totalSold,
                    '$' . number_format($revenue, 2),
                    '$' . number_format($avgSalePrice ?? 0, 2),
                    $performance
                ];
            }
        );
    }

    /**
     * Export with error handling demonstration using data mapper
     */
    public function exportErrorHandlingDemo(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = Order::with('user')
                     ->orderBy('id');

        $headers = ['Order #', 'Customer', 'Total', 'Status', 'Processing Result'];

        return $this->exporter->streamFromQuery(
            $query,
            $headers,
            "error-handling-demo.{$format}",
            ['format' => $format, 'chunk_size' => 500],
            function($order) {
                // Intentionally cause errors on every 10th record to demonstrate fallback
                if ($order->id % 10 === 0) {
                    throw new \Exception('Demo error for order ' . $order->id);
                }

                // Complex calculation that could fail
                $complexCalculation = $order->total_amount * 1.15; // Add tax

                return [
                    $order->order_number,
                    $order->user->name ?? 'Unknown Customer',
                    '$' . number_format($order->total_amount, 2),
                    ucfirst($order->status),
                    'Processed successfully'
                ];
            }
        );
    }

    /**
     * Export orders with complex nested data using data mapper
     */
    public function exportOrdersWithItemDetails(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = Order::with(['user', 'orderItems.product'])
                     ->orderBy('created_at', 'desc');

        $headers = ['Order #', 'Customer', 'Items Summary', 'Top Product', 'Order Value', 'Item Count', 'Date'];

        return $this->exporter->streamFromQuery(
            $query,
            $headers,
            "orders-with-item-details.{$format}",
            ['format' => $format, 'chunk_size' => 300],
            function($order) {
                $items = $order->orderItems;
                $topProduct = $items->sortByDesc(function($item) {
                    return $item->quantity * $item->price;
                })->first();

                $itemsSummary = $items->map(function($item) {
                    return $item->product->name . ' (x' . $item->quantity . ')';
                })->take(3)->implode(', ');

                if ($items->count() > 3) {
                    $itemsSummary .= ' +' . ($items->count() - 3) . ' more';
                }

                return [
                    $order->order_number,
                    $order->user->name,
                    $itemsSummary,
                    $topProduct ? $topProduct->product->name : 'N/A',
                    '$' . number_format($order->total_amount, 2),
                    $items->count(),
                    $order->created_at->format('Y-m-d H:i')
                ];
            }
        );
    }

    /**
     * Export user activity report using data mapper
     */
    public function exportUserActivityReport(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = User::with(['orders'])
                    ->orderBy('created_at');

        $headers = ['Name', 'Email', 'Joined', 'Orders', 'Last Activity', 'Total Spent', 'Activity Level'];

        return $this->exporter->streamFromQuery(
            $query,
            $headers,
            "user-activity-report.{$format}",
            ['format' => $format, 'chunk_size' => 1500],
            function($user) {
                $orders = $user->orders;
                $totalSpent = $orders->sum('total_amount');
                $lastOrder = $orders->sortByDesc('created_at')->first();
                
                $activityLevel = 'Inactive';
                if ($orders->count() > 10) $activityLevel = 'Very Active';
                elseif ($orders->count() > 5) $activityLevel = 'Active'; 
                elseif ($orders->count() > 0) $activityLevel = 'Low Activity';

                return [
                    $user->name,
                    $user->email,
                    $user->created_at->format('Y-m-d'),
                    $orders->count(),
                    $lastOrder ? $lastOrder->created_at->format('Y-m-d') : 'Never',
                    '$' . number_format($totalSpent, 2),
                    $activityLevel
                ];
            }
        );
    }
}