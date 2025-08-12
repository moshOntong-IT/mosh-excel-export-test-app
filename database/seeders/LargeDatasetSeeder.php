<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating large dataset for memory testing...');
        
        // Disable query logging for performance
        DB::disableQueryLog();
        
        // Create users in chunks for memory efficiency
        $this->command->info('Creating 50,000 users...');
        $this->createUsersInChunks(50000, 1000);
        
        // Create products
        $this->command->info('Creating 10,000 products...');
        $this->createProductsInChunks(10000, 1000);
        
        // Create orders with items
        $this->command->info('Creating 25,000 orders with items...');
        $this->createOrdersWithItems(25000, 500);
        
        DB::enableQueryLog();
        
        $this->command->info('Large dataset created successfully!');
        $this->printDatasetStats();
    }

    private function createUsersInChunks(int $total, int $chunkSize): void
    {
        $chunks = ceil($total / $chunkSize);
        
        for ($i = 0; $i < $chunks; $i++) {
            $currentChunkSize = min($chunkSize, $total - ($i * $chunkSize));
            
            User::factory($currentChunkSize)->create();
            
            $this->command->getOutput()->write('.');
            
            // Force garbage collection to free memory
            if ($i % 10 === 0) {
                gc_collect_cycles();
            }
        }
        $this->command->line('');
    }

    private function createProductsInChunks(int $total, int $chunkSize): void
    {
        $chunks = ceil($total / $chunkSize);
        
        for ($i = 0; $i < $chunks; $i++) {
            $currentChunkSize = min($chunkSize, $total - ($i * $chunkSize));
            
            Product::factory($currentChunkSize)->create();
            
            $this->command->getOutput()->write('.');
            
            if ($i % 10 === 0) {
                gc_collect_cycles();
            }
        }
        $this->command->line('');
    }

    private function createOrdersWithItems(int $total, int $chunkSize): void
    {
        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();
        
        $chunks = ceil($total / $chunkSize);
        
        for ($i = 0; $i < $chunks; $i++) {
            $currentChunkSize = min($chunkSize, $total - ($i * $chunkSize));
            
            for ($j = 0; $j < $currentChunkSize; $j++) {
                $order = Order::factory()->create([
                    'user_id' => $userIds[array_rand($userIds)]
                ]);

                // Add 1-5 random items to each order
                $itemCount = rand(1, 5);
                for ($k = 0; $k < $itemCount; $k++) {
                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $productIds[array_rand($productIds)]
                    ]);
                }
            }
            
            $this->command->getOutput()->write('.');
            
            if ($i % 10 === 0) {
                gc_collect_cycles();
            }
        }
        $this->command->line('');
    }

    private function printDatasetStats(): void
    {
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Users', number_format(User::count())],
                ['Products', number_format(Product::count())],
                ['Orders', number_format(Order::count())],
                ['Order Items', number_format(OrderItem::count())],
                ['Total Records', number_format(
                    User::count() + Product::count() + Order::count() + OrderItem::count()
                )],
            ]
        );
    }
}