<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst($this->faker->words(3, true));
        $price = $this->faker->randomFloat(2, 10, 800);
        $discount = $this->faker->boolean(60) ? round($price * $this->faker->randomFloat(2, 0.5, 0.95), 2) : null;
        $discountPct = $discount ? intval(100 - ($discount / $price) * 100) : null;
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numerify('###'),
            'description' => $this->faker->paragraph(),
            'short_description' => $this->faker->sentence(12),
            'price' => $price,
            'discount_price' => $discount,
            'discount_percentage' => $discountPct,
            'is_featured' => $this->faker->boolean(15),
            'is_active' => true,
            'affiliate_url' => $this->faker->url(),
            'store_id' => \App\Models\Store::inRandomOrder()->value('id') ?? \App\Models\Store::factory(),
            'category_id' => \App\Models\Category::inRandomOrder()->value('id') ?? \App\Models\Category::factory(),
            'brand' => $this->faker->company(),
            'model' => strtoupper($this->faker->bothify('##??-###')),
            'specifications' => [
                'color' => $this->faker->safeColorName(),
                'weight' => $this->faker->randomFloat(2, 0.2, 5.0) . ' kg',
                'warranty' => $this->faker->randomElement(['6 months','1 year','2 years']),
            ],
            'rating' => $this->faker->randomFloat(1, 3.5, 5.0),
            'review_count' => $this->faker->numberBetween(5, 800),
            'views_count' => $this->faker->numberBetween(50, 5000),
            'clicks_count' => $this->faker->numberBetween(10, 2000),
        ];
    }
}
