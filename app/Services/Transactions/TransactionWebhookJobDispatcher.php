<?php

namespace App\Services\Transactions;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use App\Enums\TransactionsInternalStatusesEnum;
use App\Jobs\SendTransactionSyncWebhooks;
use App\Models\MerchantWebhookUrl;

/**
 * Prepare and dispatch job.
 */
class TransactionWebhookJobDispatcher
{
    /**
     * Data of notifications.
     *
     * @var Collection
     */
    protected $notificationList;

    /**
     * Webhooks urls of merchants.
     *
     * @var array
     */
    protected $merchantUrls;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notificationList = collect();
    }

    /**
     * Add data to notifications list.
     *
     * @param Transaction $transaction
     */
    public function addNotificationFor(Transaction $transaction): void
    {
        $notificationsList = $this->getNotificationsListOfMerchant($transaction->merchant_id);

        $notificationsList->push([
            'id' => $transaction->external_id,
        ]);
    }

    /**
     * Dispacth jobs.
     *
     * @return void
     */
    public function dispatch(): void
    {
        foreach ($this->notificationList as $merchantId => $transactionsData) {
            if ($hookUrl = $this->getMerchantHookUrl($merchantId)) {
                SendTransactionSyncWebhooks::dispatch($hookUrl, $transactionsData);
            }
        }
    }

    /**
     * Get list of notifications.
     *
     * @param  int    $merchantId
     *
     * @return Collection
     */
    protected function getNotificationsListOfMerchant(int $merchantId): Collection
    {
        if (!$this->notificationList->has($merchantId)) {
            $this->notificationList->put($merchantId, collect());
        }

        return $this->notificationList->get($merchantId);
    }

    /**
     * Return URL of a notification receiver.
     *
     * @param  int    $merchantId
     *
     * @return string
     */
    protected function getMerchantHookUrl(int $merchantId): string
    {
        if (!$this->merchantUrls) {
            $this->merchantUrls = MerchantWebhookUrl::pluck('url', 'merchant_id')->toArray();
        }

        return data_get($this->merchantUrls, $merchantId, '');
    }
}
