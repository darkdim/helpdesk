<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Pending = 'pending';
    case Resolved = 'resolved';
    case Closed = 'closed';

    /**
     * Check whether a transition from this status to the given status is allowed.
     *
     * Per SPEC section 5:
     * open → pending | resolved | closed
     * pending → open | resolved | closed
     * resolved → open | closed
     * closed → nowhere
     */
    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Open => in_array($to, [self::Pending, self::Resolved, self::Closed]),
            self::Pending => in_array($to, [self::Open, self::Resolved, self::Closed]),
            self::Resolved => in_array($to, [self::Open, self::Closed]),
            self::Closed => false,
        };
    }

    /**
     * Check whether the ticket is in an active (non-final) state.
     */
    public function isActive(): bool
    {
        return match ($this) {
            self::Open, self::Pending, self::Resolved => true,
            self::Closed => false,
        };
    }
}
