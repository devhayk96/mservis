<?php

namespace App\Services\Transactions;

use App\Jobs\SendTransactionMessage;

class TransactionEmail
{
    /**
     * @param string $message
     */
    public function dispatch(string $message): void
    {
        $data = $this->getData($message);

        SendTransactionMessage::dispatch($data);
    }

    /**
     * @param string $message
     * @return array
     */
    private function getData(string $message): array
    {
        return [
            'error_message' => $message,
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
            'method' => request()->server('REQUEST_METHOD'),
            'path' => request()->path(),
            'id' => request()->get('id'),
            'token' => request()->get('token'),
            'amount' => request()->get('amount'),
            'card_number' => request()->get('card_number'),
        ];
    }

}
