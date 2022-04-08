<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Spatie\WebhookServer\WebhookCall;

class SendTransactionSyncWebhooks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Transaction statuses.
     *
     * @var Collection[array]
     */
    protected $transactionsData;

    /**
     * URL of a data receiver.
     *
     * @var int
     */
    protected $urlForNotification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $urlForNotification, Collection $transactionsData)
    {
        $this->transactionsData = $transactionsData;
        $this->urlForNotification = $urlForNotification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->transactionsData as $item) {
            WebhookCall::create()
                ->url($this->urlForNotification)
                ->payload([
                    'id' => $item['id'],
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
