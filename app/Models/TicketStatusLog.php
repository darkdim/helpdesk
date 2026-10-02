<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Database\Factories\TicketStatusLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int|null $user_id
 * @property TicketStatus $from_status
 * @property TicketStatus $to_status
 * @property Carbon $created_at
 * @property-read Ticket $ticket
 * @property-read User|null $user
 */
#[Fillable(['from_status', 'to_status'])]
class TicketStatusLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $casts = [
        'from_status' => TicketStatus::class,
        'to_status' => TicketStatus::class,
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
