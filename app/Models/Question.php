<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'investor_id',
        'question',
        'response',
        'answered_at',
        'is_answered',
    ];

    protected $casts = [
        'is_answered' => 'boolean',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the meeting this question belongs to.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Get the investor who asked this question.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    /**
     * Alias for the investor relationship.
     * This provides compatibility with the naming used in views.
     */
    public function user(): BelongsTo
    {
        return $this->investor();
    }

    /**
     * Alias for backward compatibility with the old asked_by_id field name.
     * @deprecated Use investor() instead
     */
    // ! public function asked_by(): BelongsTo
    public function askedBy(): BelongsTo
    {
        return $this->investor();
    }
}
