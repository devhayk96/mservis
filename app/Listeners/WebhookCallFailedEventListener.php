<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Log;

class WebhookCallFailedEventListener
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
     * @param  WebhookCallFailedEvent  $event
     * @return void
     */
    public function handle(WebhookCallFailedEvent $event)
    {
        Log::channel('webhooks')->info('error', [
            'webhookUrl' => $event->webhookUrl,
            'payload' => $event->payload,
            'errorType' => $event->errorType,
            'errorMessage' => $event->errorMessage,
        ]);
    }
}
