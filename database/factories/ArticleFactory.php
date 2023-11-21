<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'inventory_number' => fake()->unique()->numerify(),
            'catalog_number' => fake()->unique()->numerify(),
            'draft_number' => fake()->unique()->numerify(),
            'material' => fake()->colorName(),
            'description' => fake()->title(),
            'price' => fake()->randomFloat()
        ];
    }
}
