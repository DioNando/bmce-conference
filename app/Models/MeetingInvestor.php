<?php

namespace App\Models;

use App\Enums\InvestorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MeetingInvestor extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'investor_id',
        'status',
        'invitation_sent',
        'invitation_sent_at',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'status' => InvestorStatus::class,
        'invitation_sent' => 'boolean',
        'invitation_sent_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    /**
     * Get the meeting that this record belongs to.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Get the investor user for this meeting participation.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    /**
     * Get the user that checked in this investor.
     */
    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
