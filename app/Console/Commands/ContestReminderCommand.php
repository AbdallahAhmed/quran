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
                $contest = $user->contest[0];
                $expired_at = $contest->expired_at;
                $contest_remaining = $expired_at->diffInMinutes(\Carbon\Carbon::now());
                $contest_all = $expired_at->diffInMinutes($contest->start_at);
                $contest_precentage = (int)(($contest_remaining / $contest_all) * 100);
                 if ($contest_precentage == 15 or $contest_precentage == 50 or $contest_precentage == 75) {
                $contest_pages = get_contest_pages($contest->juz_from, $contest->juz_to);
                $user_pages = json_decode($contest->pivot->pages ? $contest->pivot->pages : '[]');
                $read_percentage = count($user_pages) > 0 ? (int)((count($user_pages) / count($contest_pages) * 100)) : 0;
                $this->notify($user, $contest, $read_percentage, $contest_remaining);

                 }
            }
        }
    }

    public function notify($user, $contest, $read, $time_remaining)
    {
        app()->setLocale($user->lang);
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
