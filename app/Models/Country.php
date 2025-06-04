<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_en',
        'code',
    ];

    /**
     * Get the organizations for this country.
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
