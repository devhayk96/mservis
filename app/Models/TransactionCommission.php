<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionCommission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'daily_commission_id',
        'amount',
    ];

    public function dailyCommission(): BelongsTo
    {
        return $this->belongsTo(DailyCommission::class);
    }
}
