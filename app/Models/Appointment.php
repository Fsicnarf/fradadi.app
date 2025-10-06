<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dni',
        'patient_name',
        'patient_age',
        'phone',
        'title',
        'start_at',
        'end_at',
        'appointment_type',
        'duration_min',
        'channel',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'patient_age' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
