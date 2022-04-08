<?php

namespace App\Services\Processing;

use App\Models\Transaction;
use App\Services\Processing\Processors\ProcessorFactory;
use Illuminate\Database\Eloquent\Collection;

/**
 * Send transactions to processing services.
 */
class PaymentProcessingService
{
    /**
     * Porcess transaction.
     *
     * @param  Transaction $transaction
     * @param  int         $processingOperatorId
     *
     * @return void
     */
    public function process(Transaction $transaction, int $processingOperatorId): void
    {
        $processor = ProcessorFactory::create($processingOperatorId);

        if ($processor) {
            $transaction->markAsInProcessing($processingOperatorId);
            $processor->processPayment($transaction);
        }
    }


    /**
     * Process many transactions.
     *
     * @param  Collection[Transaction] $transactions
     * @param  int                     $processingOperatorId
     *
     * @return void
     */
    public function processMany(Collection $transactions, int $processingOperatorId): void
    {
        foreach ($transactions as $transaction) {
            $this->process($transaction, $processingOperatorId);
        }
    }
}
