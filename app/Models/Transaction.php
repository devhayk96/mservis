<?php

namespace App\Models;

use App\Enums\ProcessingOperatorsEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TransactionsInternalStatusesEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'amount',
        'card_number',
        'external_id',
        'merchant_id',
        'bank_id',
        'daily_commission_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'execution_date' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        $user = auth()->user();

        if ($user && !$user->hasRole('admin')) {
            static::addGlobalScope('hideOthersTransactions', function (Builder $builder) use ($user) {
                $builder->where('merchant_id', $user->merchant_id);
            });
        }
    }

    /**
     * Get the merchant of the transaction.
     *
     * @return BelongsTo
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the bank of the transaction.
     *
     * @return BelongsTo
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get processing records of the transaction.
     *
     * @return HasMany
     */
    public function processing(): HasMany
    {
        return $this->hasMany(TransactionProcessing::class);
    }

    /**
     * Get commission of the transaction.
     *
     * @return HasOne
     */
    public function commission(): HasOne
    {
        return $this->hasOne(TransactionCommission::class);
    }

    /**
     * Scope a query to only include transactions that are not in processing.
     *
     * @param  Builder $query
     *
     * @return Builder
     */
    public function scopeWhichNotInProcessing(Builder $query): Builder
    {
        return $query->whereDoesntHave('processing', function (Builder $processingQuery) {
            $processingQuery->where('is_processing', true);
        });
    }

    /**
     * Scope a query to only include transactions between certain dates.
     *
     * @param  Builder      $query
     * @param  string|null  $from
     * @param  string|null  $to
     *
     * @return Builder
     */
    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query->when($from, function (Builder $query) use ($from) {
            $query->where('date', '>=', $from);
        })
        ->when($to, function (Builder $query) use ($to) {
            $query->where('date', '<=', $to);
        });
    }

    /**
     * Return status of a transaction.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return data_get(TransactionsInternalStatusesEnum::all(), $this->status ?? 0, TransactionsInternalStatusesEnum::all()[0]);
    }

    /**
     * Set status of a transaction.
     *
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = (int) array_search($status, TransactionsInternalStatusesEnum::all());
    }

    /**
     * Mark transaction as success.
     *
     * @return void
     */
    public function setAsSuccess(): void
    {
        $this->status = TransactionsInternalStatusesEnum::STATUS_SUCCESS;
    }

    /**
     * Mark transaction as failed.
     *
     * @return void
     */
    public function setAsFail(): void
    {
        $this->status = TransactionsInternalStatusesEnum::STATUS_FAIL;
    }

    /**
     * Mark transaction as it is in processing.
     *
     * @param  int    $processingOperatorId
     *
     * @return void
     */
    public function markAsInProcessing(int $processingOperatorId): void
    {
        TransactionProcessing::create([
            'transaction_id' => $this->id,
            'processing_operator' => $processingOperatorId,
        ]);
    }

    /**
     * Mark transaction it is not in processing.
     *
     * @return void
     */
    public function markAsNotInProcessing(string $comment = ''): void
    {
        $processingRecord = $this->processing()
            ->orderBy('id', 'desc')
            ->first();

        if (!$processingRecord) {
            return;
        }

        $processingRecord->is_processing = false;
        $processingRecord->end_comment = $comment;
        $processingRecord->save();
    }

    /**
     * Return name of a processing operator.
     *
     * @return string
     */
    public function getProcessingOperator(): string
    {
        $lastProcessing = $this->getLastProcessing();

        return $lastProcessing
            ? ProcessingOperatorsEnum::getName($lastProcessing->processing_operator)
            : '';
    }

    /**
     * Show wheter transaction is in processing.
     *
     * @return bool
     */
    public function isProcessing(): bool
    {
        $lastProcessing = $this->getLastProcessing();

        return $lastProcessing
            ? (bool) $lastProcessing->is_processing
            : false;
    }

    /**
     * Return comment of a last processing.
     *
     * @return string
     */
    public function getProcessingComment(): string
    {
        return (string) optional($this->getLastProcessing())->end_comment;
    }

    /**
     * Return the last processing record.
     *
     * @return TransactionProcessing|null
     */
    public function getLastProcessing(): ?TransactionProcessing
    {
        return $this->processing
            ->sortByDesc('id')
            ->first();
    }
}
