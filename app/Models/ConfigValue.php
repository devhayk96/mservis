<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for database stored config values.
 */
class ConfigValue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
