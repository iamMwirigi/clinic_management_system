<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'doctor_id',
        'specialty_name',
        'description',
        'consultation_fee',
        'is_active',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
