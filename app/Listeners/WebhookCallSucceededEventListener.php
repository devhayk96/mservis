<?php

namespace App\Listeners;

use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class WebhookCallSucceededEventListener
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
     * @param  WebhookCallSucceededEvent  $event
     * @return void
     */
    public function handle(WebhookCallSucceededEvent $event)
    {
        Log::channel('webhooks')->info('success', [
            'webhookUrl' => $event->webhookUrl,
            'payload' => $event->payload,
            'response' => $event->response->getStatusCode(),
        ]);
    }
}
