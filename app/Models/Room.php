<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'location',
    ];

    /**
     * Get the meetings scheduled in this room.
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }
}
