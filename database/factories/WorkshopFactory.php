<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WorkshopFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'scheduled_at' => fake()->dateTimeBetween('+2 days', '+2 months'),
            'capacity' => fake()->numberBetween(5, 30),
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}