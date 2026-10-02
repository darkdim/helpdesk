<?php

namespace Database\Factories;

use App\Models\TicketAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketAttachmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => 'attachments/' . fake()->uuid() . '.png',
            'original_name' => fake()->words(3, true) . '.png',
            'mime' => 'image/png',
            'size' => fake()->numberBetween(1000, 500000),
        ];
    }
}
