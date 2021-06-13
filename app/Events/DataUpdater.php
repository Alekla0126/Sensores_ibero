<?php

namespace App\Events;

use App\Models\DeviceState;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DataUpdater implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DeviceState $device;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DeviceState $device)
    {
        $this->device = $device;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['my-channel'];
    }

    public function broadcastAs()
    {
        return 'my-event';
    }
}
