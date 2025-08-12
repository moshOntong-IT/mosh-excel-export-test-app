<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $categories = [
            'Electronics', 'Clothing', 'Books', 'Home & Garden', 
            'Sports', 'Toys', 'Automotive', 'Health & Beauty',
            'Groceries', 'Tools', 'Music', 'Movies'
        ];

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 5, 500),
            'category' => fake()->randomElement($categories),
            'sku' => strtoupper(fake()->bothify('??##??##')),
            'stock_quantity' => fake()->numberBetween(0, 1000),
            'is_active' => fake()->boolean(85), // 85% chance of being active
        ];
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'stock_quantity' => 0,
        ]);
    }

    public function electronics()
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Electronics',
            'price' => fake()->randomFloat(2, 50, 2000),
        ]);
    }

    public function books()
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Books',
            'price' => fake()->randomFloat(2, 10, 100),
            'name' => fake()->sentence(4),
        ]);
    }
}