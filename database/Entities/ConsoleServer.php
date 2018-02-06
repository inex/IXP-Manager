<?php

namespace Entities;

/**
 * ConsoleServer
 */
class ConsoleServer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $serial_number;

    /**
     * @var boolean
     */
    private $active = '1';

    /**
     * @var string
     */
    private $notes;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $consoleServerConnections;

    /**
     * @var \Entities\Vendor
     */
    private $vendor;

    /**
     * @var \Entities\Cabinet
     */
    private $cabinet;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->consoleServerConnections = new \Doctrine\Common\Collections\ArrayCollection();
    }

}

