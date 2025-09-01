<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        $original = $this->faker->randomFloat(2, 10, 500);
        $deal = round($original * $this->faker->randomFloat(2, 0.3, 0.9), 2);
        $discountPct = min(90, max(5, intval(100 - ($deal / $original) * 100)));
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numerify('###'),
            'description' => $this->faker->sentence(12),
            'original_price' => $original,
            'deal_price' => $deal,
            'discount_percentage' => $discountPct,
            'starts_at' => now()->subDays($this->faker->numberBetween(0, 10)),
            'expires_at' => now()->addDays($this->faker->numberBetween(3, 30)),
            'is_featured' => $this->faker->boolean(20),
            'is_active' => true,
            'affiliate_url' => $this->faker->url(),
            'store_id' => \App\Models\Store::inRandomOrder()->value('id') ?? \App\Models\Store::factory(),
            'category_id' => \App\Models\Category::inRandomOrder()->value('id') ?? \App\Models\Category::factory(),
            'views_count' => $this->faker->numberBetween(50, 5000),
            'clicks_count' => $this->faker->numberBetween(10, 2000),
        ];
    }
}
