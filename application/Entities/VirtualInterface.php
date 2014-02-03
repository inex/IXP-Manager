<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\VirtualInterface
 */
class VirtualInterface
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var integer $mtu
     */
    protected $mtu;

    /**
     * @var boolean $trunk
     */
    protected $trunk;

    /**
     * @var integer $channelgroup
     */
    protected $channelgroup;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $PhysicalInterfaces;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $VlanInterfaces;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $MACAddresses;

    /**
     * @var Entities\Customer
     */
    protected $Customer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->PhysicalInterfaces = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VlanInterfaces = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MACAddresses = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return VirtualInterface
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
     * Set description
     *
     * @param string $description
     * @return VirtualInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set mtu
     *
     * @param integer $mtu
     * @return VirtualInterface
     */
    public function setMtu($mtu)
    {
        $this->mtu = $mtu;
    
        return $this;
    }

    /**
     * Get mtu
     *
     * @return integer
     */
    public function getMtu()
    {
        return $this->mtu;
    }

    /**
     * Set trunk
     *
     * @param boolean $trunk
     * @return VirtualInterface
     */
    public function setTrunk($trunk)
    {
        $this->trunk = $trunk;
    
        return $this;
    }

    /**
     * Get trunk
     *
     * @return boolean
     */
    public function getTrunk()
    {
        return $this->trunk;
    }

    /**
     * Set channelgroup
     *
     * @param integer $channelgroup
     * @return VirtualInterface
     */
    public function setChannelgroup($channelgroup)
    {
        $this->channelgroup = $channelgroup;
    
        return $this;
    }

    /**
     * Get channelgroup
     *
     * @return integer
     */
    public function getChannelgroup()
    {
        return $this->channelgroup;
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
     * Add PhysicalInterfaces
     *
     * @param Entities\PhysicalInterface $physicalInterfaces
     * @return VirtualInterface
     */
    public function addPhysicalInterface(\Entities\PhysicalInterface $physicalInterfaces)
    {
        $this->PhysicalInterfaces[] = $physicalInterfaces;
    
        return $this;
    }

    /**
     * Remove PhysicalInterfaces
     *
     * @param Entities\PhysicalInterface $physicalInterfaces
     */
    public function removePhysicalInterface(\Entities\PhysicalInterface $physicalInterfaces)
    {
        $this->PhysicalInterfaces->removeElement($physicalInterfaces);
    }

    /**
     * Get PhysicalInterfaces
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPhysicalInterfaces()
    {
        return $this->PhysicalInterfaces;
    }

    /**
     * Add VlanInterfaces
     *
     * @param Entities\VlanInterface $vlanInterfaces
     * @return VirtualInterface
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
     * Add MACAddresses
     *
     * @param Entities\MACAddress $mACAddresses
     * @return VirtualInterface
     */
    public function addMACAddresses(\Entities\MACAddress $mACAddresses)
    {
        $this->MACAddresses[] = $mACAddresses;
    
        return $this;
    }

    /**
     * Remove MACAddresses
     *
     * @param Entities\MACAddress $mACAddresses
     */
    public function removeMACAddresses(\Entities\MACAddress $mACAddresses)
    {
        $this->MACAddresses->removeElement($mACAddresses);
    }

    /**
     * Get MACAddresses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMACAddresses()
    {
        return $this->MACAddresses;
    }

    /**
     * Set Customer
     *
     * @param Entities\Customer $customer
     * @return VirtualInterface
     */
    public function setCustomer(\Entities\Customer $customer = null)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return Entities\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Get the *type* of virtual interface based on the switchport type.
     *
     * Actually returns type of the first physical interface's switchport. All
     * switchports in a virtual interface should be the same type so just
     * examining the first is sufficient to determine the *virtual interface type*.
     *
     * @see \Entities\SwitchPort::$TYPES
     *
     * @return string|bool The virtual interface type (`\Entities\SwitchPort::TYPE_XXX`) or false if no physical interfaces.
     */
    public function getType()
    {
        if( count( $this->getPhysicalInterfaces() ) )
            return $this->getPhysicalInterfaces()[0]->getSwitchPort()->getType();
        else
            return false;
    }

    /**
     * Add MACAddresses
     *
     * @param \Entities\MACAddress $mACAddresses
     * @return VirtualInterface
     */
    public function addMACAddresse(\Entities\MACAddress $mACAddresses)
    {
        $this->MACAddresses[] = $mACAddresses;
    
        return $this;
    }

    /**
     * Remove MACAddresses
     *
     * @param \Entities\MACAddress $mACAddresses
     */
    public function removeMACAddresse(\Entities\MACAddress $mACAddresses)
    {
        $this->MACAddresses->removeElement($mACAddresses);
    }
}