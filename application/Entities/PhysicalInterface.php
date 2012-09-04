<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\PhysicalInterface
 */
class PhysicalInterface
{
    /**
     * @var integer $status
     */
    private $status;

    /**
     * @var integer $speed
     */
    private $speed;

    /**
     * @var string $duplex
     */
    private $duplex;

    /**
     * @var integer $monitorindex
     */
    private $monitorindex;

    /**
     * @var string $notes
     */
    private $notes;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Entities\SwitchPort
     */
    private $SwitchPort;

    /**
     * @var Entities\VirtualInterface
     */
    private $VirtualInterface;


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
}