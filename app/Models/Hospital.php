<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'paybill_number',
        'subscription_status',
        'subscription_plan',
        'subscription_expires_at',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Get the users for the hospital.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get admins for the hospital.
     */
    public function admins()
    {
        return $this->users()->where('role', 'admin');
    }

    /**
     * Get doctors for the hospital.
     */
    public function doctors()
    {
        return $this->users()->where('role', 'doctor');
    }

    /**
     * Get attendants for the hospital.
     */
    public function attendants()
    {
        return $this->users()->where('role', 'attendant');
    }

    /**
     * Check if the hospital's subscription is active.
     */
    public function isSubscriptionActive(): bool
    {
        if ($this->subscription_status !== 'active') {
            return false;
        }

        if ($this->subscription_expires_at === null) {
            return true;
        }

        return $this->subscription_expires_at->isFuture();
    }
}
