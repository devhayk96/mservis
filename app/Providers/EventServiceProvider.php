<?php

namespace App\Providers;

use App\Listeners\WebhookCallSucceededEventListener;
use App\Listeners\WebhookCallFailedEventListener;
use App\Listeners\FinalWebhookCallFailedEventListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        WebhookCallSucceededEvent::class => [
            WebhookCallSucceededEventListener::class
        ],
        WebhookCallFailedEvent::class => [
            WebhookCallFailedEventListener::class
        ],
        FinalWebhookCallFailedEvent::class => [
            FinalWebhookCallFailedEventListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
