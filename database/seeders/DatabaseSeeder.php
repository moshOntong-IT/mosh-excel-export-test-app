<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedType = $this->command->choice(
            'Which type of seed data would you like to create?',
            [
                'demo' => 'Demo Data (Quick - ~250 records for testing)',
                'large' => 'Large Dataset (Memory Testing - ~85,000+ records)',
                'both' => 'Both (Demo + Large Dataset)'
            ],
            'demo'
        );

        switch ($seedType) {
            case 'demo':
                $this->call(DemoDataSeeder::class);
                break;
            case 'large':
                $this->call(LargeDatasetSeeder::class);
                break;
            case 'both':
                $this->call(DemoDataSeeder::class);
                $this->call(LargeDatasetSeeder::class);
                break;
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->line('');
        $this->command->info('🚀 You can now test the Excel Export Streamer plugin:');
        $this->command->line('   • Start server: php artisan serve');
        $this->command->line('   • Visit test routes to export data');
    }
}
