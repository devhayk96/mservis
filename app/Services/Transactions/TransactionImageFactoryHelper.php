<?php

namespace App\Services\Transactions;

use App\Models\Merchant;
use Exception;
use Carbon\Carbon;
use App\Models\Bank;

/**
 * Help functions for TransactionImage factory.
 */
class TransactionImageFactoryHelper
{
    /**
     * Cache for Merchant models.
     *
     * @var array
     */
    private $merchantsCache = [];

    /**
     * Parse string representation of a date.
     *
     * @param  string $value
     *
     * @return Carbon|null
     */
    public function parseDate(string $value): ?Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Normalize card number.
     *
     * @param  string $value
     *
     * @return string
     */
    public function normalizeCardNumber(string $value): string
    {
        return trim(str_replace(' ', '', $value));
    }

    /**
     * Normalize amount of a transaction.
     *
     * @param  mixed $value
     *
     * @return float
     */
    public function normalizeAmount($value): float
    {
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }

    /**
     * Return Bank model by card number.
     *
     * @param  string $cardNumber
     *
     * @return Bank|null
     */
    public function getBankByCardNumber(string $cardNumber): ?Bank
    {
        return Bank::where('bin', substr($cardNumber, 0, 6))->first();
    }

    /**
     * Return Merchant model by a given name.
     *
     * @param  string $name
     *
     * @return Merchant|null
     */
    public function getMerchantByName(string $name): ?Merchant
    {
        if (!array_key_exists($name, $this->merchantsCache)) {
            $this->merchantsCache[$name] = Merchant::where('name', $name)->first();
        }

        return $this->merchantsCache[$name];
    }
}
