<?php

namespace Database\Factories;

use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => fake()->paragraph(),
            'is_internal' => false,
        ];
    }

    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_internal' => true,
        ]);
    }
}
