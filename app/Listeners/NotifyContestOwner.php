<?php

namespace App\Listeners;

use App\Http\Controllers\NotificationController;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyContestOwner
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $name = $event->user->name;
        $join = str_replace(":name", $name, trans('app.new_member_join_contest'));
        $notification = new NotificationController(trans('app.new_join'), $join);
        $notification->sendUser(User::find($event->owner_id));
    }
}
