<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->catchPhrase() . ' ' . $this->faker->randomElement(['Sale','Offer','Discount','Coupon']);
        $discountPct = $this->faker->numberBetween(10, 70);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numerify('###'),
            'code' => strtoupper($this->faker->bothify('SAVE##??')),
            'discount_text' => $discountPct . '% OFF',
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
