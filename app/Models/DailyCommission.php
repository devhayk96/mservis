<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyCommission extends Model
{
    /**
     * Type value for percent commission.
     */
    public const TYPE_PERCENT = 0;

    /**
     * Type value for absolute values commission.
     */
    public const TYPE_ABSOLUTE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'daily_commissions';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'amount',
        'type',
        'merchant_id',
    ];

    /**
     * Get transactions related to commission.
     *
     * @return BelongsTo
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Populate model with fallback values.
     *
     * @return void
     */
    public function populateFallbackCommission(): void
    {
        $this->amount = 0;
        $this->type = self::TYPE_PERCENT;
    }

    /**
     * Return commission for value.
     *
     * @param  float  $value
     *
     * @return float
     */
    public function getCommissionForValue(float $value): float
    {
        if ($this->type === self::TYPE_PERCENT) {
            return (float) bcdiv(bcmul($value, $this->amount, 2), 100, 2);
        } elseif ($this->type === self::TYPE_ABSOLUTE) {
            return $this->amount;
        }

        return 0.0;
    }
}
