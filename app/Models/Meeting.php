<?php

namespace App\Models;

use App\Enums\MeetingStatus;
use App\Observers\MeetingObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([MeetingObserver::class])]
class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'time_slot_id',
        'issuer_id',
        'created_by_id',
        'updated_by_id',
        'status',
        'notes',
        'is_one_on_one',
    ];

    protected $casts = [
        'is_one_on_one' => 'boolean',
        'status' => MeetingStatus::class,
    ];

    /**
     * Get the room where the meeting takes place.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the time slot for this meeting.
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Get the issuer participating in this meeting.
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issuer_id');
    }

    /**
     * Get the investors participating in this meeting.
     */
    public function investors()
    {
        return $this->belongsToMany(User::class, 'meeting_investors', 'meeting_id', 'investor_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Get the meeting investors pivot records.
     */
    public function meetingInvestors(): HasMany
    {
        return $this->hasMany(MeetingInvestor::class);
    }

    /**
     * Get the questions related to this meeting.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the user who created this meeting.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the user who last updated this meeting.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
