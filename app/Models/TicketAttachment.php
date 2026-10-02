<?php

namespace App\Models;

use Database\Factories\TicketAttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $ticket_message_id
 * @property string $path
 * @property string $original_name
 * @property string $mime
 * @property int $size
 * @property-read TicketMessage $message
 */
#[Fillable(['path', 'original_name', 'mime', 'size'])]
class TicketAttachment extends Model
{
    use HasFactory;

    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }
}
