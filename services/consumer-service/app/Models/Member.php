<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'name',
        'email',
        'membership_id',
        'join_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'join_date' => 'date',
    ];
}
