<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\IPv6Address
 */
class IPv6Address
{
    /**
     * @var string $address
     */
    protected $address;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\VlanInterface
     */
    protected $VlanInterface;

    /**
     * @var Entities\Vlan
     */
    protected $Vlan;


    /**
     * Set address
     *
     * @param string $address
     * @return IPv6Address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
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
     * Set VlanInterface
     *
     * @param Entities\VlanInterface $vlanInterface
     * @return IPv6Address
     */
    public function setVlanInterface(\Entities\VlanInterface $vlanInterface = null)
    {
        $this->VlanInterface = $vlanInterface;
    
        return $this;
    }

    /**
     * Get VlanInterface
     *
     * @return Entities\VlanInterface 
     */
    public function getVlanInterface()
    {
        return $this->VlanInterface;
    }

    /**
     * Set Vlan
     *
     * @param Entities\Vlan $vlan
     * @return IPv6Address
     */
    public function setVlan(\Entities\Vlan $vlan = null)
    {
        $this->Vlan = $vlan;
    
        return $this;
    }

    /**
     * Get Vlan
     *
     * @return Entities\Vlan 
     */
    public function getVlan()
    {
        return $this->Vlan;
    }
}