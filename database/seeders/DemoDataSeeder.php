<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating demo data...');

        // Create demo users
        $users = [
            User::factory()->create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'email_verified_at' => now(),
            ]),
            User::factory()->create([
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'email_verified_at' => now(),
            ]),
            User::factory()->create([
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'email_verified_at' => null, // Unverified user
            ]),
        ];

        // Create additional random users
        User::factory(47)->create(); // Total of 50 users

        // Create demo products
        $products = [
            Product::factory()->create([
                'name' => 'iPhone 15 Pro',
                'category' => 'Electronics',
                'price' => 999.99,
                'sku' => 'IPH15PRO',
                'stock_quantity' => 25,
            ]),
            Product::factory()->create([
                'name' => 'Samsung Galaxy S24',
                'category' => 'Electronics',
                'price' => 799.99,
                'sku' => 'SGS24',
                'stock_quantity' => 30,
            ]),
            Product::factory()->create([
                'name' => 'The Great Gatsby',
                'category' => 'Books',
                'price' => 12.99,
                'sku' => 'TGG001',
                'stock_quantity' => 100,
            ]),
            Product::factory()->create([
                'name' => 'Nike Air Max 90',
                'category' => 'Sports',
                'price' => 129.99,
                'sku' => 'NAM90',
                'stock_quantity' => 50,
            ]),
            Product::factory()->inactive()->create([
                'name' => 'Discontinued Product',
                'category' => 'Electronics',
                'price' => 199.99,
                'sku' => 'DISC001',
                'stock_quantity' => 0,
            ]),
        ];

        // Create additional random products
        Product::factory(95)->create(); // Total of 100 products

        $this->command->info('Creating orders and order items...');

        // Create orders for each user
        foreach (User::all() as $user) {
            $orderCount = rand(1, 5); // Each user has 1-5 orders
            
            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Add random items to each order
                $itemCount = rand(1, 4);
                $orderTotal = 0;
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $product = Product::inRandomOrder()->first();
                    $quantity = rand(1, 3);
                    $price = $product->price;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
                    
                    $orderTotal += ($price * $quantity);
                }
                
                // Update order total
                $order->update(['total_amount' => $orderTotal]);
            }
        }

        $this->command->info('Demo data created successfully!');
        $this->printStats();
    }

    private function printStats(): void
    {
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Users', User::count()],
                ['Products', Product::count()],
                ['Orders', Order::count()],
                ['Order Items', OrderItem::count()],
            ]
        );

        $this->command->info('Demo accounts:');
        $this->command->line('• john@example.com (verified)');
        $this->command->line('• jane@example.com (verified)');
        $this->command->line('• bob@example.com (unverified)');
    }
}