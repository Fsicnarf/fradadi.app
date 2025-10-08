<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'key', // DNI o NAME:<nombre>
        'path',
        'title',
        'original_name',
        'mime',
        'size',
        'description',
        ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
