<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'name',
        'email',
        'phone',
        'password',
        'qr_code',
        'position',
        'organization_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($guest) {
            if (!$guest->qr_code) {
                $guest->qr_code = Str::uuid()->toString();
            }
        });
    }


    /**
     * Get the organization associated with the user.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the time slots associated with the user.
     */
    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    /**
     * Get the questions asked by this user.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'investor_id');
    }

    /**
     * Get meetings where this user participates as an issuer.
     */
    public function issuerMeetings()
    {
        return $this->hasMany(Meeting::class, 'issuer_id');
    }

    /**
     * Get meetings where this user participates as an investor.
     */
    public function investorMeetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_investors', 'investor_id', 'meeting_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get meetings created by this user.
     */
    public function createdMeetings()
    {
        return $this->hasMany(Meeting::class, 'created_by_id');
    }

    /**
     * Get meetings updated by this user.
     */
    public function updatedMeetings()
    {
        return $this->hasMany(Meeting::class, 'updated_by_id');
    }

    /**
     * Check if user is from an investor organization.
     */
    public function isInvestor(): bool
    {
        return $this->hasRole(UserRole::INVESTOR->value);
    }

    /**
     * Check if user is from an issuer organization.
     */
    public function isIssuer(): bool
    {
        return $this->hasRole(UserRole::ISSUER->value);
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN->value);
    }
    
    /**
     * Get all notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }
    
    /**
     * Get unread notifications for this user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
}
