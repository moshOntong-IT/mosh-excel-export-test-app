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
}