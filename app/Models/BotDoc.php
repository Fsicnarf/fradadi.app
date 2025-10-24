<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'tags',
        'path',
        'mime',
        'size',
        'content',
        'auto_titles'
    ];

    protected $casts = [
        'auto_titles' => 'array',
    ];
}
