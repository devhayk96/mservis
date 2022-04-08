<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Log;

class FinalWebhookCallFailedEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FinalWebhookCallFailedEvent  $event
     * @return void
     */
    public function handle(FinalWebhookCallFailedEvent $event)
    {
        Log::channel('webhooks')->info('final_error', [
            'webhookUrl' => $event->webhookUrl,
            'payload' => $event->payload,
            'errorType' => $event->errorType,
            'errorMessage' => $event->errorMessage,
        ]);
    }
}
