<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleDrivePublicFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id'
    ];
}
