<?php

namespace App\Listeners;

use App\Events\ContestCreated;
use App\Http\Controllers\NotificationController;
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
     * @param  object  $event
     * @return void
     */
    public function handle(ContestCreated $event)
    {
        $notification = new NotificationController(trans('app.new_contest'), trans('app.new_contest_content'));
        $users = User::whereHas('devices')->get();
        /*foreach ($users as $user){
            $notify = new \App\Not();
            $notify->user_id = $user->id;
            $notify->type = "new_contest_content";
            $notify->save();
        }*/
        $notification->sendAll();
    }
}
