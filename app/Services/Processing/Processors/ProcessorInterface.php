<?php

namespace App\Services\Processing\Processors;

use App\Models\Transaction;

/**
 * Interface that every processor must implement.
 */
interface ProcessorInterface
{
    /**
     * Process payment.
     *
     * @param  Transaction $transaction
     *
     * @return void
     */
    public function processPayment(Transaction $transaction): void;

    /**
     * Mark transaction as not in processing.
     *
     * @param  int    $transactionId
     *
     * @return void
     */
    public function markTransactionAsNotInProcessing(int $transactionId): void;
}
