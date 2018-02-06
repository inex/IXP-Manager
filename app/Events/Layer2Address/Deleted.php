<?php

namespace IXP\Events\Layer2Address;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Deleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $mac;

    /**
     * @var Customer
     */
    public $auth;

    /**
     * @var VirtualInterface
     */
    public $vli;

    /**
     * Create a new event instance.
     *
     * @param string                $mac
     * @param VlanInterface         $vli
     * @param Customer              $auth
     *
     * @return void
     */
    public function __construct( $mac, $vli, $auth )
    {
        $this->action   = "delete";
        $this->mac      = $mac;
        $this->auth     = $auth;
        $this->vli      = $vli;
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
