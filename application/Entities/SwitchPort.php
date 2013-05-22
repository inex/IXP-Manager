<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\SwitchPort
 */
class SwitchPort
{
    
    const TYPE_UNSET          = 0;
    const TYPE_PEERING        = 1;
    const TYPE_MONITOR        = 2;
    const TYPE_CORE           = 3;
    const TYPE_OTHER          = 4;
    const TYPE_MANAGEMENT     = 5;
    
    public static $TYPES = array(
        self::TYPE_UNSET      => 'Unset / Unknown',
        self::TYPE_PEERING    => 'Peering',
        self::TYPE_MONITOR    => 'Monitor',
        self::TYPE_CORE       => 'Core',
        self::TYPE_OTHER      => 'Other',
        self::TYPE_MANAGEMENT => 'Management'
    );
    
    /**
     * @var integer $type
     */
    private $type;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Entities\PhysicalInterface
     */
    private $PhysicalInterface;

    /**
     * @var Entities\Switcher
     */
    private $Switcher;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $SecEvents;

    /**
     * @var string
     */
    private $ifName;

    /**
     * @var string
     */
    private $ifAlias;

    /**
     * @var integer
     */
    private $ifHighSpeed;

    /**
     * @var integer
     */
    private $ifMtu;

    /**
     * @var string
     */
    private $ifPhysAddress;

    /**
     * @var integer
     */
    private $ifAdminStatus;

    /**
     * @var integer
     */
    private $ifOperStatus;

    /**
     * @var integer
     */
    private $ifLastChange;

    /**
     * @var \DateTime
     */
    private $lastSnmpPoll;

    /**
     * @var integer
     */
    private $ifIndex;


    /**
     * @var boolean $active
     */
    private $active;

    /**
     * Set type
     *
     * @param integer $type
     * @return SwitchPort
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SwitchPort
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set PhysicalInterface
     *
     * @param Entities\PhysicalInterface $physicalInterface
     * @return SwitchPort
     */
    public function setPhysicalInterface(\Entities\PhysicalInterface $physicalInterface = null)
    {
        $this->PhysicalInterface = $physicalInterface;
    
        return $this;
    }

    /**
     * Get PhysicalInterface
     *
     * @return Entities\PhysicalInterface
     */
    public function getPhysicalInterface()
    {
        return $this->PhysicalInterface;
    }

    /**
     * Set Switcher
     *
     * @param Entities\Switcher $switcher
     * @return SwitchPort
     */
    public function setSwitcher(\Entities\Switcher $switcher = null)
    {
        $this->Switcher = $switcher;
    
        return $this;
    }

    /**
     * Get Switcher
     *
     * @return Entities\Switcher
     */
    public function getSwitcher()
    {
        return $this->Switcher;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->SecEvents = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add SecEvents
     *
     * @param Entities\SecEvent $secEvents
     * @return SwitchPort
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
     * Set ifName
     *
     * @param string $ifName
     * @return SwitchPort
     */
    public function setIfName($ifName)
    {
        $this->ifName = $ifName;
    
        return $this;
    }

    /**
     * Get ifName
     *
     * @return string 
     */
    public function getIfName()
    {
        return $this->ifName;
    }

    /**
     * Set ifAlias
     *
     * @param string $ifAlias
     * @return SwitchPort
     */
    public function setIfAlias($ifAlias)
    {
        $this->ifAlias = $ifAlias;
    
        return $this;
    }

    /**
     * Get ifAlias
     *
     * @return string 
     */
    public function getIfAlias()
    {
        return $this->ifAlias;
    }

    /**
     * Set ifHighSpeed
     *
     * @param integer $ifHighSpeed
     * @return SwitchPort
     */
    public function setIfHighSpeed($ifHighSpeed)
    {
        $this->ifHighSpeed = $ifHighSpeed;
    
        return $this;
    }

    /**
     * Get ifHighSpeed
     *
     * @return integer 
     */
    public function getIfHighSpeed()
    {
        return $this->ifHighSpeed;
    }

    /**
     * Set ifMtu
     *
     * @param integer $ifMtu
     * @return SwitchPort
     */
    public function setIfMtu($ifMtu)
    {
        $this->ifMtu = $ifMtu;
    
        return $this;
    }

    /**
     * Get ifMtu
     *
     * @return integer 
     */
    public function getIfMtu()
    {
        return $this->ifMtu;
    }

    /**
     * Set ifPhysAddress
     *
     * @param string $ifPhysAddress
     * @return SwitchPort
     */
    public function setIfPhysAddress($ifPhysAddress)
    {
        $this->ifPhysAddress = $ifPhysAddress;
    
        return $this;
    }

    /**
     * Get ifPhysAddress
     *
     * @return string 
     */
    public function getIfPhysAddress()
    {
        return $this->ifPhysAddress;
    }

    /**
     * Set ifAdminStatus
     *
     * @param integer $ifAdminStatus
     * @return SwitchPort
     */
    public function setIfAdminStatus($ifAdminStatus)
    {
        $this->ifAdminStatus = $ifAdminStatus;
    
        return $this;
    }

    /**
     * Get ifAdminStatus
     *
     * @return integer 
     */
    public function getIfAdminStatus()
    {
        return $this->ifAdminStatus;
    }

    /**
     * Set ifOperStatus
     *
     * @param integer $ifOperStatus
     * @return SwitchPort
     */
    public function setIfOperStatus($ifOperStatus)
    {
        $this->ifOperStatus = $ifOperStatus;
    
        return $this;
    }

    /**
     * Get ifOperStatus
     *
     * @return integer 
     */
    public function getIfOperStatus()
    {
        return $this->ifOperStatus;
    }

    /**
     * Set ifLastChange
     *
     * @param integer $ifLastChange
     * @return SwitchPort
     */
    public function setIfLastChange($ifLastChange)
    {
        $this->ifLastChange = $ifLastChange;
    
        return $this;
    }

    /**
     * Get ifLastChange
     *
     * @return integer 
     */
    public function getIfLastChange()
    {
        return $this->ifLastChange;
    }

    /**
     * Set lastSnmpPoll
     *
     * @param \DateTime $lastSnmpPoll
     * @return SwitchPort
     */
    public function setLastSnmpPoll($lastSnmpPoll)
    {
        $this->lastSnmpPoll = $lastSnmpPoll;
    
        return $this;
    }

    /**
     * Get lastSnmpPoll
     *
     * @return \DateTime 
     */
    public function getLastSnmpPoll()
    {
        return $this->lastSnmpPoll;
    }


    /**
     * Set ifIndex
     *
     * @param integer $ifIndex
     * @return SwitchPort
     */
    public function setIfIndex($ifIndex)
    {
        $this->ifIndex = $ifIndex;
    
        return $this;
    }

    /**
     * Get ifIndex
     *
     * @return integer 
     */
    public function getIfIndex()
    {
        return $this->ifIndex;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return SwitchPort
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
}