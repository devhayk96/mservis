<?php

namespace App\Services;

use App\Models\DailyCommission;
use App\Models\Transaction;
use App\Models\TransactionCommission;
use Exception;

/**
 * Handle merchant commissions.
 */
class MerchantTransactionCommission
{
    /**
     * Transaction.
     *
     * @var Transaction
     */
    protected $transaction;

    /**
     * Merchant.
     *
     * @var \App\Models\Merchant
     */
    protected $merchant;

    /**
     * Commission.
     *
     * @var \App\Models\DailyCommission
     */
    protected $dailyCommission;

    /**
     * Constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->merchant = $transaction->merchant;
        $this->setCommission();
    }

    /**
     * Show whether commission has not been applied to transaction.
     *
     * @return bool
     */
    public function commissionHasNotBeenApplied(): bool
    {
        return !$this->transaction->commission;
    }

    /**
     * Reduce balance of a merchant.
     *
     * @return void
     */
    public function reduceMerchantBalance(): void
    {
        if ($this->dailyCommission->id) {
            $commissionAbsoluteAmount = $this->dailyCommission->getCommissionForValue($this->transaction->amount);
            $writeOff = bcadd($this->transaction->amount, $commissionAbsoluteAmount, 2);

            $this->merchant->balance = bcsub($this->merchant->balance, $writeOff, 2);
            $this->merchant->save();

            TransactionCommission::create([
                'transaction_id' => $this->transaction->id,
                'daily_commission_id' => $this->dailyCommission->id,
                'amount' => $commissionAbsoluteAmount,
            ]);
        }
    }

    /**
     * Set daily commission.
     */
    protected function setCommission(): void
    {
        $this->dailyCommission = app('daily-commission')->getCommissionByMerchantId($this->merchant->id);
    }
}
