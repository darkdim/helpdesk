<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => null,
            'subject' => fake()->sentence(),
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::Normal,
            'last_activity_at' => now(),
        ];
    }

    public function forCustomer(User $user): static
    {
        return $this->for($user, 'customer');
    }

    public function withAssignee(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assignee_id' => $user->id,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Pending,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Resolved,
            'resolved_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Closed,
            'closed_at' => now(),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Urgent,
        ]);
    }
}
