<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory(),
            'attendee_name' => fake()->name(),
            'attendee_email' => fake()->unique()->safeEmail(),
            'status' => 'confirmed',
        ];
    }
}