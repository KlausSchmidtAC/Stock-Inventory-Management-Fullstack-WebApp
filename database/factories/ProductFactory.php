<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'isbn' => fake()->optional(0.8)->isbn13(),
            'count' => fake()->numberBetween(0, 100),
            'manufacturer' => fake()->optional(0.7)->company(),
            'supplier' => fake()->optional(0.7)->company(),
            'price' => fake()->randomFloat(2, 5, 500),
            'last_supplied_at' => fake()->optional(0.6)->dateTimeBetween('-1 year', 'now'),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'count' => 0,
        ]);
    }

    /**
     * Indicate that the product has low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'count' => fake()->numberBetween(1, 10),
        ]);
    }
}
