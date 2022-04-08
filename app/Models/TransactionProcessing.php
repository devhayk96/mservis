<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Records about transaction processing.
 */
class TransactionProcessing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions_processing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'is_processing',
        'processing_operator',
        'end_comment',
    ];

    /**
     * Get a transaction of a record.
     *
     * @return BelongsTo
     */
    public function processing(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Scope a query to only include records for a certain transaction.
     *
     * @param  Builder $query
     * @param  int     $transactionId
     *
     * @return Builder
     */
    public function scopeOfTransaction(Builder $query, int $transactionId): Builder
    {
        return $query->where('transaction_id', $transactionId);
    }
}
