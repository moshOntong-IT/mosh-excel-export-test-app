<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $status = fake()->randomElement($statuses);
        
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . fake()->unique()->numberBetween(100000, 999999),
            'status' => $status,
            'total_amount' => fake()->randomFloat(2, 20, 1000),
            'shipping_address' => json_encode([
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'zip' => fake()->postcode(),
                'country' => 'USA'
            ]),
            'billing_address' => json_encode([
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'zip' => fake()->postcode(),
                'country' => 'USA'
            ]),
            'notes' => fake()->optional()->sentence(),
            'shipped_at' => $status === 'shipped' || $status === 'delivered' 
                ? fake()->dateTimeBetween('-30 days', 'now') 
                : null,
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'shipped_at' => null,
        ]);
    }

    public function shipped()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shipped',
            'shipped_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function delivered()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'shipped_at' => fake()->dateTimeBetween('-30 days', '-3 days'),
        ]);
    }

    public function cancelled()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'shipped_at' => null,
        ]);
    }
}