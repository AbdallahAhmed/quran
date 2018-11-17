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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $users_ids = ContestMember::where('contest_id', $event->contest->id)->pluck('member_id')->toArray();
        $tokens = array();
        foreach ($users_ids as $user_id){
            $devices = UsersTokens::where('user_id', $user_id)->get();
            foreach ($devices as $device){
                $tokens[] = Token::find($device->token_id)->device_token;
            }
        }
        app()->setLocale('ar');
        $title = trans('app.winner');
        $body = str_replace(":name", "Abdallah", trans('app.contest_winner'));
        $body = str_replace(":contest", $event->contest->name, $body);
        $notification = new NotificationController($title, $body);
        $notification->sendGroup($tokens);
    }
}
