<?php

namespace Tests\Unit;

use App\Enums\TicketStatus;
use Tests\TestCase;

class TicketStatusTest extends TestCase
{
    public function test_can_transition_open_to_pending(): void
    {
        $this->assertTrue(TicketStatus::Open->canTransitionTo(TicketStatus::Pending));
    }

    public function test_can_transition_open_to_resolved(): void
    {
        $this->assertTrue(TicketStatus::Open->canTransitionTo(TicketStatus::Resolved));
    }

    public function test_can_transition_open_to_closed(): void
    {
        $this->assertTrue(TicketStatus::Open->canTransitionTo(TicketStatus::Closed));
    }

    public function test_cannot_transition_open_to_open(): void
    {
        $this->assertFalse(TicketStatus::Open->canTransitionTo(TicketStatus::Open));
    }

    public function test_can_transition_pending_to_open(): void
    {
        $this->assertTrue(TicketStatus::Pending->canTransitionTo(TicketStatus::Open));
    }

    public function test_can_transition_pending_to_resolved(): void
    {
        $this->assertTrue(TicketStatus::Pending->canTransitionTo(TicketStatus::Resolved));
    }

    public function test_can_transition_pending_to_closed(): void
    {
        $this->assertTrue(TicketStatus::Pending->canTransitionTo(TicketStatus::Closed));
    }

    public function test_cannot_transition_pending_to_pending(): void
    {
        $this->assertFalse(TicketStatus::Pending->canTransitionTo(TicketStatus::Pending));
    }

    public function test_can_transition_resolved_to_open(): void
    {
        $this->assertTrue(TicketStatus::Resolved->canTransitionTo(TicketStatus::Open));
    }

    public function test_can_transition_resolved_to_closed(): void
    {
        $this->assertTrue(TicketStatus::Resolved->canTransitionTo(TicketStatus::Closed));
    }

    public function test_cannot_transition_resolved_to_pending(): void
    {
        $this->assertFalse(TicketStatus::Resolved->canTransitionTo(TicketStatus::Pending));
    }

    public function test_cannot_transition_resolved_to_resolved(): void
    {
        $this->assertFalse(TicketStatus::Resolved->canTransitionTo(TicketStatus::Resolved));
    }

    public function test_cannot_transition_closed_to_any(): void
    {
        foreach (TicketStatus::cases() as $to) {
            $this->assertFalse(
                TicketStatus::Closed->canTransitionTo($to),
                "closed should not transition to {$to->value}"
            );
        }
    }

    public function test_is_active_for_open(): void
    {
        $this->assertTrue(TicketStatus::Open->isActive());
    }

    public function test_is_active_for_pending(): void
    {
        $this->assertTrue(TicketStatus::Pending->isActive());
    }

    public function test_is_active_for_resolved(): void
    {
        $this->assertTrue(TicketStatus::Resolved->isActive());
    }

    public function test_is_not_active_for_closed(): void
    {
        $this->assertFalse(TicketStatus::Closed->isActive());
    }
}
