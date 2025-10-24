<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'key',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
