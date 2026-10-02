<?php

namespace Database\Factories;

use App\Models\TicketStatusLog;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketStatusLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_status' => TicketStatus::Open,
            'to_status' => TicketStatus::Pending,
            'created_at' => now(),
        ];
    }
}
