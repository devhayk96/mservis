<?php

namespace App\Services;

use App\Models\Merchant;

class TokenDecodingService
{
    /**
     * Return merchant by a given token and transaction ID.
     *
     * @param  string $transactionId
     * @param  string $token
     *
     * @return Merchant|null
     */
    public function getMerchantOfTransaction(string $transactionId, string $token): ?Merchant
    {
        foreach (Merchant::all() as $merchant) {
            if ($token === md5($transactionId . $merchant->secret_key)) {
                return $merchant;
            }
        }

        return null;
    }
}
