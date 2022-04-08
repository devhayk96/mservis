<?php

namespace App\Services\Processing\Processors;

use App\Models\Transaction;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Carbon;

abstract class PaymentProcessor implements ProcessorInterface
{
    /**
     * Process payment.
     *
     * @param  Transaction $transaction
     *
     * @return void
     */
    public function processPayment(Transaction $transaction): void
    {
        $message = $this->getMessageForTransaction($transaction);
        $job = $this->getJobObject($transaction->id, $message);
        app(Dispatcher::class)->dispatch($job);
    }

    /**
     * Process payment.
     *
     * @param  Transaction $transaction
     * @param  int         $nextAttemptNumber
     * @param  string      $paymentDelay
     *
     * @return void
     */
    public function checkPayment(Transaction $transaction, int $nextAttemptNumber, string $paymentDelay): void
    {
        $message = $this->getMessageForTransaction($transaction);

        $job = $this->getJobObject($transaction->id, $message, $nextAttemptNumber);
        $job->delay($this->getDelayForCheckRequest($nextAttemptNumber, $paymentDelay));
        app(Dispatcher::class)->dispatch($job);
    }

    /**
     * Return delay before next request.
     *
     * @param  int $nextAttemptNumber
     * @param  string $paymentDelay
     *
     * @return Carbon
     */
    protected function getDelayForCheckRequest(int $nextAttemptNumber, string $paymentDelay): Carbon
    {
        $delay = app('subsequent-requests')
            ->getDelay($paymentDelay, $nextAttemptNumber - 1);

        return now()->addMinutes($delay);
    }

    /**
     * Mark transaction as not in processing.
     *
     * @param  int    $transactionId
     *
     * @return void
     */
    public function markTransactionAsNotInProcessing(int $transactionId): void
    {
        $transaction = Transaction::find($transactionId);

        if ($transaction) {
            $transaction->markAsNotInProcessing();
            $transaction->save();
        }
    }

    /**
     * Return random phone number without country code.
     *
     * @return string
     */
    protected function getRandomPhoneWithoutCountryCode(): string
    {
        return '9' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
    }
}
