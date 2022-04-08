<?php

namespace App\Services\Processing\Processors;

use View;
use DateTime;
use App\Models\Transaction;
use App\Jobs\Processing\PompayJob;

class PompayProcessor extends PaymentProcessor
{
    /**
     * Show if Pompay processor in test mode.
     *
     * @return bool
     */
    public static function isInTestMode(): bool
    {
        return (bool) app('database-config')->get('pompay.test_mode');
    }

    /**
     * Return job object.
     *
     * @param  int    $transactionId
     * @param  string $message
     * @param  int    $nextAttemptNumber
     *
     * @return PompayJob
     */
    public function getJobObject(int $transactionId, string $message, int $nextAttemptNumber = 1): PompayJob
    {
        return new PompayJob($transactionId, $message, $nextAttemptNumber);
    }

    /**
     * Return message of payment request.
     *
     * @param  Transaction $transaction
     *
     * @return string
     */
    public function getMessageForTransaction(Transaction $transaction): string
    {
        $cardNumber = self::isInTestMode()
            ? '0000000000000000'
            : $transaction->card_number;

        $otherMarkup = View::make('pompay/payment')->with([
            'id' => $transaction->id,
            'transactionAmount' => bcmul($transaction->amount, 100), // в копейках
            'service' => app('database-config')->get('pompay.service'),
            'cardNumber' => $cardNumber,
            'phone' => '+7' . $this->getRandomPhoneWithoutCountryCode(),
            'date' => $transaction->date->format(DateTime::ISO8601),
        ])->render();

        return $otherMarkup;
    }
}
