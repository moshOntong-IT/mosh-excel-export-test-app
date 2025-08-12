<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the test homepage with export links
     */
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'products' => Product::count(), 
            'orders' => Order::count(),
            'order_items' => OrderItem::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'active_products' => Product::where('is_active', true)->count(),
        ];

        return view('test-homepage', compact('stats'));
    }

    /**
     * Show a specific model's data
     */
    public function showUsers()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('users', compact('users'));
    }

    public function showProducts()
    {
        $products = Product::where('is_active', true)
                          ->orderBy('category', 'name')
                          ->paginate(20);
        return view('products', compact('products'));
    }

    public function showOrders()
    {
        $orders = Order::with('user')
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);
        return view('orders', compact('orders'));
    }

    /**
     * API endpoints for testing
     */
    public function apiStats()
    {
        return response()->json([
            'users' => User::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'order_items' => OrderItem::count(),
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit'),
            ]
        ]);
    }
}