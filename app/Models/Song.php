<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    // All fields except this will can be filled.
    protected $guarded = ['id'];

    // Let's cast some data to proper types
    protected $casts = [
        'duration' => 'integer',
        'total_duration' => 'integer',
    ];
}
