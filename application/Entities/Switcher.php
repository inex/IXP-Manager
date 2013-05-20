<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Switcher
 */
class Switcher
{
    const TYPE_SWITCH        = 1;
    const TYPE_CONSOLESERVER = 2;
    
    
    public static $TYPES = [
        self::TYPE_SWITCH        => 'Switch',
        self::TYPE_CONSOLESERVER => 'Console Server'
    ];
    
    
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $ipv4addr
     */
    private $ipv4addr;

    /**
     * @var string $ipv6addr
     */
    private $ipv6addr;

    /**
     * @var string $snmppasswd
     */
    private $snmppasswd;

    /**
     * @var integer $infrastructure
     */
    private $infrastructure;

    /**
     * @var integer $switchtype
     */
    private $switchtype;

    /**
     * @var string $model
     */
    private $model;

    /**
     * @var string $notes
     */
    private $notes;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Ports;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $ConsoleServerConnections;

    /**
     * @var Entities\Cabinet
     */
    private $Cabinet;

    /**
     * @var Entities\Vendor
     */
    private $Vendor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Ports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ConsoleServerConnections = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Switcher
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ipv4addr
     *
     * @param string $ipv4addr
     * @return Switcher
     */
    public function setIpv4addr($ipv4addr)
    {
        $this->ipv4addr = $ipv4addr;
    
        return $this;
    }

    /**
     * Get ipv4addr
     *
     * @return string
     */
    public function getIpv4addr()
    {
        return $this->ipv4addr;
    }

    /**
     * Set ipv6addr
     *
     * @param string $ipv6addr
     * @return Switcher
     */
    public function setIpv6addr($ipv6addr)
    {
        $this->ipv6addr = $ipv6addr;
    
        return $this;
    }

    /**
     * Get ipv6addr
     *
     * @return string
     */
    public function getIpv6addr()
    {
        return $this->ipv6addr;
    }

    /**
     * Set snmppasswd
     *
     * @param string $snmppasswd
     * @return Switcher
     */
    public function setSnmppasswd($snmppasswd)
    {
        $this->snmppasswd = $snmppasswd;
    
        return $this;
    }

    /**
     * Get snmppasswd
     *
     * @return string
     */
    public function getSnmppasswd()
    {
        return $this->snmppasswd;
    }

    /**
     * Set infrastructure
     *
     * @param integer $infrastructure
     * @return Switcher
     */
    public function setInfrastructure($infrastructure)
    {
        $this->infrastructure = $infrastructure;
    
        return $this;
    }

    /**
     * Get infrastructure
     *
     * @return integer
     */
    public function getInfrastructure()
    {
        return $this->infrastructure;
    }

    /**
     * Set switchtype
     *
     * @param integer $switchtype
     * @return Switcher
     */
    public function setSwitchtype($switchtype)
    {
        $this->switchtype = $switchtype;
    
        return $this;
    }

    /**
     * Get switchtype
     *
     * @return integer
     */
    public function getSwitchtype()
    {
        return $this->switchtype;
    }

    /**
     * Set model
     *
     * @param string $model
     * @return Switcher
     */
    public function setModel($model)
    {
        $this->model = $model;
    
        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Switcher
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
     * Add Ports
     *
     * @param Entities\SwitchPort $ports
     * @return Switcher
     */
    public function addPort(\Entities\SwitchPort $ports)
    {
        $this->Ports[] = $ports;
    
        return $this;
    }

    /**
     * Remove Ports
     *
     * @param Entities\SwitchPort $ports
     */
    public function removePort(\Entities\SwitchPort $ports)
    {
        $this->Ports->removeElement($ports);
    }

    /**
     * Get Ports
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPorts()
    {
        return $this->Ports;
    }

    /**
     * Add ConsoleServerConnections
     *
     * @param Entities\ConsoleServerConnection $consoleServerConnections
     * @return Switcher
     */
    public function addConsoleServerConnection(\Entities\ConsoleServerConnection $consoleServerConnections)
    {
        $this->ConsoleServerConnections[] = $consoleServerConnections;
    
        return $this;
    }

    /**
     * Remove ConsoleServerConnections
     *
     * @param Entities\ConsoleServerConnection $consoleServerConnections
     */
    public function removeConsoleServerConnection(\Entities\ConsoleServerConnection $consoleServerConnections)
    {
        $this->ConsoleServerConnections->removeElement($consoleServerConnections);
    }

    /**
     * Get ConsoleServerConnections
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getConsoleServerConnections()
    {
        return $this->ConsoleServerConnections;
    }

    /**
     * Set Cabinet
     *
     * @param Entities\Cabinet $cabinet
     * @return Switcher
     */
    public function setCabinet(\Entities\Cabinet $cabinet = null)
    {
        $this->Cabinet = $cabinet;
    
        return $this;
    }

    /**
     * Get Cabinet
     *
     * @return Entities\Cabinet
     */
    public function getCabinet()
    {
        return $this->Cabinet;
    }

    /**
     * Set Vendor
     *
     * @param Entities\Vendor $vendor
     * @return Switcher
     */
    public function setVendor(\Entities\Vendor $vendor = null)
    {
        $this->Vendor = $vendor;
    
        return $this;
    }

    /**
     * Get Vendor
     *
     * @return Entities\Vendor
     */
    public function getVendor()
    {
        return $this->Vendor;
    }
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $SecEvents;


    /**
     * Add SecEvents
     *
     * @param Entities\SecEvent $secEvents
     * @return Switcher
     */
    public function addSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->SecEvents[] = $secEvents;
    
        return $this;
    }

    /**
     * Remove SecEvents
     *
     * @param Entities\SecEvent $secEvents
     */
    public function removeSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->SecEvents->removeElement($secEvents);
    }

    /**
     * Get SecEvents
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSecEvents()
    {
        return $this->SecEvents;
    }
    /**
     * @var boolean $active
     */
    private $active;


    /**
     * Set active
     *
     * @param boolean $active
     * @return Switcher
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    /**
     * @var string
     */
    private $hostname;


    /**
     * Set hostname
     *
     * @param string $hostname
     * @return Switcher
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    
        return $this;
    }

    /**
     * Get hostname
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }
    /**
     * @var string
     */
    private $os;

    /**
     * @var \DateTime
     */
    private $osDate;

    /**
     * @var string
     */
    private $osVersion;

    /**
     * @var \DateTime
     */
    private $lastPolled;


    /**
     * Set os
     *
     * @param string $os
     * @return Switcher
     */
    public function setOs($os)
    {
        $this->os = $os;
    
        return $this;
    }

    /**
     * Get os
     *
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * Set osDate
     *
     * @param \DateTime $osDate
     * @return Switcher
     */
    public function setOsDate($osDate)
    {
        $this->osDate = $osDate;
    
        return $this;
    }

    /**
     * Get osDate
     *
     * @return \DateTime
     */
    public function getOsDate()
    {
        return $this->osDate;
    }

    /**
     * Set osVersion
     *
     * @param string $osVersion
     * @return Switcher
     */
    public function setOsVersion($osVersion)
    {
        $this->osVersion = $osVersion;
    
        return $this;
    }

    /**
     * Get osVersion
     *
     * @return string
     */
    public function getOsVersion()
    {
        return $this->osVersion;
    }

    /**
     * Set lastPolled
     *
     * @param \DateTime $lastPolled
     * @return Switcher
     */
    public function setLastPolled($lastPolled)
    {
        $this->lastPolled = $lastPolled;
    
        return $this;
    }

    /**
     * Get lastPolled
     *
     * @return \DateTime
     */
    public function getLastPolled()
    {
        return $this->lastPolled;
    }
}