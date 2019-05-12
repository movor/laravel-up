<?php

namespace App\Providers;

use App\Listeners\MailDispatcher;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
    ];

    protected $subscribe = [
        MailDispatcher::class
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Sometimes we need to disable subscribers
        if (env('DISABLE_EVENT_SUBSCRIBERS') === true) {
            $this->subscribe = [];
        }

        parent::boot();

        //
    }
}