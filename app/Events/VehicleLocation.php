<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleLocation implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lat;
    public $lng;
    public $vehicle_id;
    public $speed;
    public $head;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($lat, $lng, $vehicle_id,$speed,$head)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->vehicle_id = $vehicle_id;
        $this->speed = $speed;
        $this->head = $head;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('notifications-schoolapp.'.$this->vehicle_id);
    }

    public function broadcastWith()
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
            'vehicle_id' => $this->vehicle_id,
            'head' => $this->head,
            'speed' => $this->speed
        ];
    }
}
