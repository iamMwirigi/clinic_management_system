<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Attendant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'hospital_id',
        'email',
        'phone',
        'password_hash',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
