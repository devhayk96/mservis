<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    /**
     * The attributes for bank info.
     *
     * @var array
     */
    protected $fillable = [
        'bin',
        'bank',
        'system',
        'type',
        'level',
        'geo',
    ];
}
