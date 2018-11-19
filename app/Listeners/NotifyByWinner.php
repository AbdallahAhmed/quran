<?php

namespace App\Listeners;

use App\Http\Controllers\NotificationController;
use App\Models\ContestMember;
use App\Models\Token;
use App\Models\UsersTokens;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyByWinner
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
        $users_ids = ContestMember::where('contest_id', $event->contest->id)->pluck('member_id')->toArray();
        foreach ($users_ids as $user_id) {
            $devices = Token::where('user_id', $user_id)->get();
            if (count($devices) > 0 && $event->contest->winner_id != 0) {
                $device = $devices[0];
                app()->setLocale($device->user->lang);
                $title = trans('app.winner');
                $body = str_replace(":name", $event->contest->winner->name, trans('app.contest_winner'));
                $body = str_replace(":contest", $event->contest->name, $body);
                $notification = new NotificationController($title, $body);
                $notification->send($device->device_token, array("type" => "contest_winner"));
            }

        }
    }
}
