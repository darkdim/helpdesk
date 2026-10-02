<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'color' => '#' . str_pad(dechex(fake()->numberBetween(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
        ];
    }
}
