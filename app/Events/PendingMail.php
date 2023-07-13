<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reserve;

class PendingMail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $reserve;
    /**
     * Create a new event instance.
     *
     * @param Reserve $reserve
     * @return void
     */
    public function __construct(Reserve $reserve)
    {
        $this->reserve = $reserve;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
