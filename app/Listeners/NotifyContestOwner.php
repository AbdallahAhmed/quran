<?php

namespace App\Listeners;

use App\Http\Controllers\NotificationController;
use App\Models\Notificate;
use App\Models\Token;
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
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $name = $event->user->name;
        $join = str_replace(":name", $name, trans('app.new_member_join_contest'));
        $notify = new Notificate();
        $notify->user_id = $event->owner_id;
        $notify->type = "new_member_join_contest";
        $notify->from_id = fauth()->user()->id;
        app()->setLocale(User::find($event->owner_id)->lang);
        if (count(Token::where('user_id', $event->owner_id)->get()) > 0) {
            $notification = new NotificationController(trans('app.new_join'), $join);
            $notification->sendUser(
                User::find($event->owner_id),
                array(
                    'type' => 'new_member_join_contest',
                    'owner_id' => $event->owner_id
                )
            );
        }
    }
}
