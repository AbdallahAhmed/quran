<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ContestCreated' => [
            'App\Listeners\NewContestNotify',
        ],
        'App\Events\ContestJoin' => [
            'App\Listeners\NotifyContestOwner'
        ],
        'App\Events\ContestWinner' => [
            'App\Listeners\NotifyByWinner'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
