<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationController;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ContestReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contest:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind user to read contest every period of time according to the contest due date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //require_once app_path('helper.php');

        $users = User::whereHas('contest')->get();
        foreach ($users as $user) {
            if (count($user->devices) > 0) {
                app()->setLocale($user->lang);
                $contest = $user->contest[0];
                $contest_pages = array();
                $expired_at = $contest->expired_at;
                $contest_remaining = \Carbon\Carbon::now()->diffInMinutes($expired_at);
                $contest_all = $expired_at->diffInMinutes($contest->start_at);
                $contest_precentage = (int)(($contest_remaining / $contest_all) * 100);
                if (
                    ($contest_precentage == 15 && $contest->reminder == 2) or
                    ($contest_precentage == 50 && $contest->reminder == 1) or
                    ($contest_precentage == 75 && $contest->reminder == 0))
                {
                    switch ($contest_precentage){
                        case 75:
                            $contest->reminder = 1;
                            $contest->save();
                            break;
                        case 50:
                            $contest->reminder = 2;
                            $contest->save();
                            break;
                        case 15:
                            $contest->reminder = 3;
                            $contest->save();
                    }
                    if ($contest->type == "juz")
                        $contest_pages = get_contest_pages($contest->juz_from, $contest->juz_to);
                    else {
                        $contest_pages = array_values(array_unique($contest->pages));
                    }
                    $user_pages = json_decode($contest->pivot->pages ? $contest->pivot->pages : '[]');
                    if (count($user_pages) > 0) {
                        $read_percentage = count($user_pages) > 0 ? (int)((count($user_pages) / count($contest_pages) * 100)) : 0;
                        $this->notify($user, $contest, $read_percentage, $contest_remaining);
                    } else {
                        $title = trans("app.contest_unread_reminder_title");
                        $body = str_replace(":remaining", remaining_time_human($contest_remaining), trans("app.contest_unread_reminder"));
                        $body = str_replace(":contest", $contest->name, $body);
                        $notification = new NotificationController($title, $body);
                        $array = array(
                            "type" => "contest_unread_reminder",
                            "contest_id" => $contest->id
                        );
                        $notification->send($user->devices[0]->device_token, $array);
                    }

                }
            }
        }
    }

    public function notify($user, $contest, $read, $time_remaining)
    {
        $title = trans("app.contest_reminder_title");
        $body = str_replace(":remaining", remaining_time_human($time_remaining), trans("app.contest_reminder"));
        $body = str_replace(":contest", $contest->name, $body);
        $body = str_replace(":percentage", $read . '%', $body);
        $notification = new NotificationController($title, $body);
        $array = array(
            "type" => "contest_reminder",
            "contest_id" => $contest->id
        );
        $notification->send($user->devices[0]->device_token, $array);
    }
}
