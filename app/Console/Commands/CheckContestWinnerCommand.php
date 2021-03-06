<?php

namespace App\Console\Commands;

use App\Events\ContestWinner;
use App\Models\Contest;
use App\Models\Juz;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckContestWinnerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contest:winner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check expiration date of contest and assign winner';

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
        $contests = Contest::opened()->where('notified', 0)->get();
        foreach ($contests as $contest) {
            if ($contest->closed_to_winner != 0 || ($contest->expired_at->diffInMinutes(\Carbon\Carbon::now()) == 0)) {
                if ($contest->closed_to_winner == 0) {
                    $contest->closed_to_winner = $contest->winner_id;
                }
                $contest->notified = 1;
                event(new ContestWinner($contest));
                $contest->save();
            }
        }
    }
}
