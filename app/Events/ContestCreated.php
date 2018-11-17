<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContestCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contest;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contest)
    {
        $this->contest = $contest;
    }
}
