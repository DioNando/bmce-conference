<?php

namespace App\Models;

use App\Enums\OrganizationType;
use App\Enums\Origin;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'origin',
        'profil',
        'organization_type',
        'organization_type_other',
        'logo',
        'fiche_bkgr',
        'country_id',
        'description',
    ];

    protected $casts = [
        'origin' => Origin::class,
        'profil' => UserRole::class,
        'organization_type' => OrganizationType::class,
    ];

    /**
     * Get the users belonging to this organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the country that this organization belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
