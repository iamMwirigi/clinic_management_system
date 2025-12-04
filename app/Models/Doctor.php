<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
        'date_of_birth',
        'license_number',
        'national_id',
        'specialization',
        'qualifications',
        'years_of_experience',
        'is_available',
        'consultation_fee',
        'work_start_time',
        'work_end_time',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'profile_photo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'is_available' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    /**
     * Get the hospital that the doctor belongs to.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
