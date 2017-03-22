<?php

namespace Entities;

/**
 * Layer2Address
 */
class Layer2Address
{
    /**
     * @var string
     */
    private $mac;

    /**
     * @var \DateTime
     */
    private $firstseen;

    /**
     * @var \DateTime
     */
    private $lastseen;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\VlanInterface
     */
    private $vlanInterface;


}

