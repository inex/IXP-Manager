<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Vlan
 */
class Vlan
{
	
	const PRIVATE_NO  = 0;
	const PRIVATE_YES = 1;
		
	public static $PRIVATE_YES_NO = array(
			self::PRIVATE_NO  => 'No',
			self::PRIVATE_YES => 'Yes'
	);
	
	
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var integer $number
     */
    private $number;

    /**
     * @var string $rcvrfname
     */
    private $rcvrfname;

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
    private $VlanInterfaces;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $IPv4Addresses;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $IPv6Addresses;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $NetworkInfo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VlanInterfaces = new \Doctrine\Common\Collections\ArrayCollection();
        $this->IPv4Addresses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->IPv6Addresses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->NetworkInfo = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Vlan
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
     * Set number
     *
     * @param integer $number
     * @return Vlan
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set rcvrfname
     *
     * @param string $rcvrfname
     * @return Vlan
     */
    public function setRcvrfname($rcvrfname)
    {
        $this->rcvrfname = $rcvrfname;
    
        return $this;
    }

    /**
     * Get rcvrfname
     *
     * @return string
     */
    public function getRcvrfname()
    {
        return $this->rcvrfname;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Vlan
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
     * Add VlanInterfaces
     *
     * @param Entities\VlanInterface $vlanInterfaces
     * @return Vlan
     */
    public function addVlanInterface(\Entities\VlanInterface $vlanInterfaces)
    {
        $this->VlanInterfaces[] = $vlanInterfaces;
    
        return $this;
    }

    /**
     * Remove VlanInterfaces
     *
     * @param Entities\VlanInterface $vlanInterfaces
     */
    public function removeVlanInterface(\Entities\VlanInterface $vlanInterfaces)
    {
        $this->VlanInterfaces->removeElement($vlanInterfaces);
    }

    /**
     * Get VlanInterfaces
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getVlanInterfaces()
    {
        return $this->VlanInterfaces;
    }

    /**
     * Add IPv4Addresses
     *
     * @param Entities\IPv4Address $iPv4Addresses
     * @return Vlan
     */
    public function addIPv4Addresse(\Entities\IPv4Address $iPv4Addresses)
    {
        $this->IPv4Addresses[] = $iPv4Addresses;
    
        return $this;
    }

    /**
     * Remove IPv4Addresses
     *
     * @param Entities\IPv4Address $iPv4Addresses
     */
    public function removeIPv4Addresse(\Entities\IPv4Address $iPv4Addresses)
    {
        $this->IPv4Addresses->removeElement($iPv4Addresses);
    }

    /**
     * Get IPv4Addresses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getIPv4Addresses()
    {
        return $this->IPv4Addresses;
    }

    /**
     * Add IPv6Addresses
     *
     * @param Entities\IPv6Address $iPv6Addresses
     * @return Vlan
     */
    public function addIPv6Addresse(\Entities\IPv6Address $iPv6Addresses)
    {
        $this->IPv6Addresses[] = $iPv6Addresses;
    
        return $this;
    }

    /**
     * Remove IPv6Addresses
     *
     * @param Entities\IPv6Address $iPv6Addresses
     */
    public function removeIPv6Addresse(\Entities\IPv6Address $iPv6Addresses)
    {
        $this->IPv6Addresses->removeElement($iPv6Addresses);
    }

    /**
     * Get IPv6Addresses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getIPv6Addresses()
    {
        return $this->IPv6Addresses;
    }

    /**
     * Add NetworkInfo
     *
     * @param Entities\NetworkInfo $networkInfo
     * @return Vlan
     */
    public function addNetworkInfo(\Entities\NetworkInfo $networkInfo)
    {
        $this->NetworkInfo[] = $networkInfo;
    
        return $this;
    }

    /**
     * Remove NetworkInfo
     *
     * @param Entities\NetworkInfo $networkInfo
     */
    public function removeNetworkInfo(\Entities\NetworkInfo $networkInfo)
    {
        $this->NetworkInfo->removeElement($networkInfo);
    }

    /**
     * Get NetworkInfo
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getNetworkInfo()
    {
        return $this->NetworkInfo;
    }
    /**
     * @var boolean
     */
    private $private;


    /**
     * Set private
     *
     * @param boolean $private
     * @return Vlan
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    
        return $this;
    }

    /**
     * Get private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }
}