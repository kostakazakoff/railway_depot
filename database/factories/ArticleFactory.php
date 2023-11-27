<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'inventory_number' => fake()->unique()->numerify(),
            'catalog_number' => fake()->numerify(),
            'draft_number' => fake()->numerify(),
            'material' => fake()->colorName(),
            'description' => fake()->colorName(),
            'price' => fake()->numerify()
        ];
    }
}
