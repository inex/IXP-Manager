<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\PhysicalInterface
 */
class PhysicalInterface
{
    const STATUS_CONNECTED       = 1;
    const STATUS_DISABLED        = 2;
    const STATUS_NOTCONNECTED    = 3;
    const STATUS_XCONNECT        = 4;
    const STATUS_QUARANTINE      = 5;

    public static $STATES = array(
        self::STATUS_CONNECTED    => 'Connected',
        self::STATUS_DISABLED     => 'Disabled',
        self::STATUS_NOTCONNECTED => 'Not Connected',
        self::STATUS_XCONNECT     => 'Awaiting X-Connect',
        self::STATUS_QUARANTINE   => 'Quarantine'
    );

    public static $SPEED = array(
        10    => '10 Mbps',
        100   => '100 Mbps',
        1000  => '1 Gbps',
        10000 => '10 Gbps'
    );

    public static $DUPLEX = array(
        'full'   => 'full',
        'half'   => 'half'
    );


    /**
     * @var integer $status
     */
    protected $status;

    /**
     * @var integer $speed
     */
    protected $speed;

    /**
     * @var string $duplex
     */
    protected $duplex;

    /**
     * @var integer $monitorindex
     */
    protected $monitorindex;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\SwitchPort
     */
    protected $SwitchPort;

    /**
     * @var Entities\VirtualInterface
     */
    protected $VirtualInterface;

    /**
     * @var \Entities\PhysicalInterface
     */
    protected $FanoutPhysicalInterface;

    /**
     * @var \Entities\PhysicalInterface
     */
    protected $PeeringPhysicalInterface;

    /**
     * Set status
     *
     * @param integer $status
     * @return PhysicalInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set speed
     *
     * @param integer $speed
     * @return PhysicalInterface
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return integer
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set duplex
     *
     * @param string $duplex
     * @return PhysicalInterface
     */
    public function setDuplex($duplex)
    {
        $this->duplex = $duplex;

        return $this;
    }

    /**
     * Get duplex
     *
     * @return string
     */
    public function getDuplex()
    {
        return $this->duplex;
    }

    /**
     * Set monitorindex
     *
     * @param integer $monitorindex
     * @return PhysicalInterface
     */
    public function setMonitorindex($monitorindex)
    {
        $this->monitorindex = $monitorindex;

        return $this;
    }

    /**
     * Get monitorindex
     *
     * @return integer
     */
    public function getMonitorindex()
    {
        return $this->monitorindex;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return PhysicalInterface
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set SwitchPort
     *
     * @param Entities\SwitchPort $switchPort
     * @return PhysicalInterface
     */
    public function setSwitchPort(\Entities\SwitchPort $switchPort = null)
    {
        $this->SwitchPort = $switchPort;

        return $this;
    }

    /**
     * Get SwitchPort
     *
     * @return Entities\SwitchPort
     */
    public function getSwitchPort()
    {
        return $this->SwitchPort;
    }

    /**
     * Set VirtualInterface
     *
     * @param Entities\VirtualInterface $virtualInterface
     * @return PhysicalInterface
     */
    public function setVirtualInterface(\Entities\VirtualInterface $virtualInterface = null)
    {
        $this->VirtualInterface = $virtualInterface;

        return $this;
    }

    /**
     * Get VirtualInterface
     *
     * @return Entities\VirtualInterface
     */
    public function getVirtualInterface()
    {
        return $this->VirtualInterface;
    }


    /**
     * Set FanoutPhysicalInterface
     *
     * @param \Entities\PhysicalInterface $fanoutPhysicalInterface
     * @return PhysicalInterface
     */
    public function setFanoutPhysicalInterface(\Entities\PhysicalInterface $fanoutPhysicalInterface = null)
    {
        $this->FanoutPhysicalInterface = $fanoutPhysicalInterface;

        return $this;
    }

    /**
     * Get FanoutPhysicalInterface
     *
     * @return \Entities\PhysicalInterface
     */
    public function getFanoutPhysicalInterface()
    {
        return $this->FanoutPhysicalInterface;
    }

    /**
     * Set PeeringPhysicalInterface
     *
     * @param \Entities\PhysicalInterface $peeringPhysicalInterface
     * @return PhysicalInterface
     */
    public function setPeeringPhysicalInterface(\Entities\PhysicalInterface $peeringPhysicalInterface = null)
    {
        $this->PeeringPhysicalInterface = $peeringPhysicalInterface;

        return $this;
    }

    /**
     * Get PeeringPhysicalInterface
     *
     * @return \Entities\PhysicalInterface
     */
    public function getPeeringPhysicalInterface()
    {
        return $this->PeeringPhysicalInterface;
    }

    /**
     * Gets the related peering / fanout port for the current fanout / peering port
     *
     * For reseller functionality, we have the option of having fanout ports connectted to
     * peering ports. In this case, this function will return the related peering or
     * fanout port as appropriate.
     *
     * @return \Entities\PhysicalInterface The related peering / fanout port (or false for none / n/a)
     */
    public function getRelatedInterface()
    {
        if( $this->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_FANOUT && $this->getPeeringPhysicalInterface() )
            return $this->getPeeringPhysicalInterface();
        else if( $this->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_PEERING && $this->getFanoutPhysicalInterface() )
            return $this->getFanoutPhysicalInterface();
        else
            return false;
    }


    /**
     * Determine if the port's status is set to CONNECTED
     * @return bool True if the port's status is CONNECTED
     */
    public function statusIsConnected()
    {
        return $this->getStatus() == self::STATUS_CONNECTED;
    }

    /**
     * Determine if the port's status is set to DISABLED
     * @return bool True if the port's status is DISABLED
     */
    public function statusIsDisabled()
    {
        return $this->getStatus() == self::STATUS_DISABLED;
    }

    /**
     * Determine if the port's status is set to NOTCONNECTED
     * @return bool True if the port's status is NOTCONNECTED
     */
    public function statusIsNotConnected()
    {
        return $this->getStatus() == self::STATUS_NOTCONNECTED;
    }

    /**
     * Determine if the port's status is set to XCONNECT
     * @return bool True if the port's status is XCONNECT
     */
    public function statusIsAwaitingXConnect()
    {
        return $this->getStatus() == self::STATUS_XCONNECT;
    }

    /**
     * Determine if the port's status is set to QUARANTINE
     * @return bool True if the port's status is QUARANTINE
     */
    public function statusIsQuarantine()
    {
        return $this->getStatus() == self::STATUS_QUARANTINE;
    }
}