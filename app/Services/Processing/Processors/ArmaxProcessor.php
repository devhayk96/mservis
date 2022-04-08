<?php

namespace App\Services\Processing\Processors;

use View;
use App\Models\Transaction;
use App\Jobs\Processing\ArmaxJob;

class ArmaxProcessor extends PaymentProcessor
{
    /**
     * Show if Armax processor in test mode.
     *
     * In test mode the payments aren't created.
     *
     * @return bool
     */
    public static function isInTestMode(): bool
    {
        return (bool) app('database-config')->get('armax.test_mode');
    }

    /**
     * Return job object.
     *
     * @param  int    $transactionId
     * @param  string $message
     * @param  int    $nextAttemptNumber
     *
     * @return ArmaxJob
     */
    public function getJobObject($transactionId, $message, int $nextAttemptNumber = 1): ArmaxJob
    {
        return new ArmaxJob($transactionId, $message, $nextAttemptNumber);
    }
    /**
     * Get message for Armax.
     *
     * @param  Transaction $transaction
     *
     * @return string
     */
    public function getMessageForTransaction(Transaction $transaction): string
    {
        $viewName = self::isInTestMode()
            ? 'armax/payment-test'
            : 'armax/payment';

        $otherMarkup = View::make($viewName)->with([
            'dealer' => app('database-config')->get('armax.dealer'),
            'login' => app('database-config')->get('armax.login'),
            'password' => app('database-config')->get('armax.password'),
            'terminal' => app('database-config')->get('armax.terminal'),
            'provider' => app('database-config')->get('armax.provider'),
            'date' => $transaction->date->toIso8601ZuluString(),
            'transactionId' => $transaction->id,
            'commission' => number_format($this->getCommission(), 2, '.', ''),
            'transactionAmount' => number_format($this->getTransactionAmountWithCommission($transaction), 2, '.', ''),
            'cardNumber' => $transaction->card_number,
            'phone' => $this->getRandomPhoneWithoutCountryCode()
        ])->render();

        return "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" . $otherMarkup;
    }

    /**
     * Return commission.
     *
     * @return float
     */
    protected function getCommission(): float
    {
        return 300.0;
    }

    /**
     * Return transaction amount with commission.
     *
     * @param  Transaction $transaction
     *
     * @return float
     */
    protected function getTransactionAmountWithCommission(Transaction $transaction): float
    {
        return (float) bcadd($transaction->amount, $this->getCommission(), 2);
    }
}
