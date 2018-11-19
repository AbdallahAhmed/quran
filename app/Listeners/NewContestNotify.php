<?php

namespace App\Listeners;

use App\Events\ContestCreated;
use App\Http\Controllers\NotificationController;
use App\Models\Notificate;
use App\User;
use Illuminate\Queue\SerializesModels;

class NewContestNotify
{
    use SerializesModels;

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
    public function handle(ContestCreated $event)
    {
        $body = str_replace(":contest", $event->contest->name, trans('app.new_contest_content'));
        $notification = new NotificationController(trans('app.new_contest'), $body);
        $users = User::whereHas('devices')->get();
        foreach ($users as $user) {
            //if ($user->id != fauth()->user()->id) {
                $notify = new Notificate();
                $notify->user_id = $user->id;
                $notify->contest_id = $event->contest->id;
                $notify->type = "new_contest_content";
                $notify->save();
           // }
        }

        $notification->sendAll($users, array("type" => "contest_reminder", "contest_name" => $event->contest->name));
    }
}
