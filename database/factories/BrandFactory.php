<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (! Storage::exists('images/brands')) {
            Storage::makeDirectory('images/brands');
        }

        return [
            'title' => $this->faker->company(),
            'thumbnail' => $this->faker->fixturesImage('brands', 'images/brands'),
            'on_home_page' => $this->faker->boolean(),
            'sorting' => $this->faker->numberBetween(1, 100),
        ];
    }
}
