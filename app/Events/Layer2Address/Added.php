<?php

namespace IXP\Events\Layer2Address;

use Entities\{
    Layer2Address   as Layer2AddressEntity,
    Customer        as CustomerEntity
};
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Added
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $action;

    /**
     * @var String
     */
    public $mac;

    /**
     * @var Customer
     */
    public $auth;

    /**
     * @var VlanInterface
     */
    public $vli;

    /**
     * Create a new event instance.
     *
     * @param Layer2AddressEntity     $l2a
     * @param CustomerEntity          $auth
     *
     * @return void
     */
    public function __construct(  $l2a,  $auth )
    {
        $this->action   = "add";
        $this->mac      = $l2a->getMac();
        $this->auth     = $auth;
        $this->vli      = $l2a->getVlanInterface();

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
