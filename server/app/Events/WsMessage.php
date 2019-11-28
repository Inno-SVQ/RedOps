<?php


namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\Types\Integer;

class WsMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userRid;
    public $eventType;
    public $jsonContent;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userRid, $eventType, $jsonContent)
    {
        $this->userRid = $userRid;
        $this->eventType = $eventType;
        $this->jsonContent = $jsonContent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userRid);
    }

}