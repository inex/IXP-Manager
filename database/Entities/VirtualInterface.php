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
    protected $name = '';

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
     * @var bool $lag_framing
     */
    protected $lag_framing = false;

    /**
     * @var bool $fastlacp
     */
    protected $fastlacp = false;


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
     * @var \Entities\Customer
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
        if( $mtu === '' ) {
            $mtu = null;
        }
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
     * Get lag framing
     *
     * @return boolean
     */
    public function getLagFraming(): bool
    {
        return $this->lag_framing;
    }

    /**
     * Set lag framing
     *
     * @param boolean $lag_framing
     * @return VirtualInterface
     */
    public function setLagFraming(bool $lag_framing): VirtualInterface
    {
        $this->lag_framing = $lag_framing;

        return $this;
    }

    /**
     * Get lag framing - fastlacp
     *
     * @return boolean
     */
    public function getFastLACP(): bool
    {
        return $this->fastlacp;
    }

    /**
     * Set lag framing - fastlacp
     *
     * @param boolean $lag_framing
     * @return VirtualInterface
     */
    public function setFastLACP(bool $fastlacp): VirtualInterface
    {
        $this->fastlacp = $fastlacp;

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
     * Get the bundle name if name and channel group are set. Otherwise an empty string.
     * @return string
     */
    public function getBundleName(): string {
        if( $this->getName() && $this->getChannelgroup() ) {
            return $this->getName() . $this->getChannelgroup();
        } else {
            return '';
        }
    }

    /**
     * Add PhysicalInterfaces
     *
     * @param PhysicalInterface $physicalInterfaces
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
     * @param PhysicalInterface $physicalInterfaces
     */
    public function removePhysicalInterface(\Entities\PhysicalInterface $physicalInterfaces)
    {
        $this->PhysicalInterfaces->removeElement($physicalInterfaces);
    }

    /**
     * Get PhysicalInterfaces
     *
     * @return \Doctrine\Common\Collections\Collection|PhysicalInterface[]
     */
    public function getPhysicalInterfaces()
    {
        return $this->PhysicalInterfaces;
    }

    /**
     * Get peerring PhysicalInterfaces
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeeringPhysicalInterface()
    {
        $ppis = [];
        foreach( $this->getPhysicalInterfaces() as $ppi){
            if( $ppis[] = $ppi->getPeeringPhysicalInterface() );
        }
        return $ppis;
    }

    /**
     * Get fanout PhysicalInterfaces
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFanoutPhysicalInterface()
    {
        $ppis = [];
        foreach( $this->getPhysicalInterfaces() as $ppi){
            if( $ppis[] = $ppi->getFanoutPhysicalInterface() );

        }
        return $ppis;
    }

    /**
     * Add VlanInterfaces
     *
     * @param VlanInterface $vlanInterfaces
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
     * @param VlanInterface $vlanInterfaces
     */
    public function removeVlanInterface(\Entities\VlanInterface $vlanInterfaces)
    {
        $this->VlanInterfaces->removeElement($vlanInterfaces);
    }

    /**
     * Get VlanInterfaces
     *
     * @return \Doctrine\Common\Collection|VlanInterface[]
     */
    public function getVlanInterfaces()
    {
        return $this->VlanInterfaces;
    }

    /**
     * Add MACAddresses
     *
     * @param MACAddress $mACAddresses
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
     * @param MACAddress $mACAddresses
     */
    public function removeMACAddresses(\Entities\MACAddress $mACAddresses)
    {
        $this->MACAddresses->removeElement($mACAddresses);
    }

    /**
     * Get MACAddresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMACAddresses()
    {
        return $this->MACAddresses;
    }

    /**
     * Set Customer
     *
     * @param Customer $customer
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
     * @return Customer
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
     * Get a Switch Port of a virtual interface.
     *
     * @return string|bool The switch port or false if no switch port.
     */
    public function getSwitchPort()
    {
        if( count( $this->getPhysicalInterfaces() ) )
            return $this->getPhysicalInterfaces()[0]->getSwitchPort();
        else
            return false;
    }

    /**
     * Get an array of the switch port name(s) (`$pi->getSwitchPort()->getName()`)
     *
     * @return array
     */
    public function getSwitchPortNames(): array
    {
        $names = [];

        foreach( $this->getPhysicalInterfaces() as $pi ) {
            if( $pi->getSwitchPort() ) {
                $names[] = $pi->getSwitchPort()->getName();
            }
        }

        return $names;
    }

    /**
     * Get the cabinet of a virtual interface.
     *
     * @return Cabinet|null The location or false if no switch port.
     */
    public function getCabinet()
    {
        if( count( $this->getPhysicalInterfaces() ) ){
            return $this->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getCabinet();
        } else {
            return null;
        }

    }

    /**
     * Get the location of a virtual interface.
     *
     * @return Location|bool The location or false if no switch port.
     */
    public function getLocation()
    {
        if( count( $this->getPhysicalInterfaces() ) ){
            return $this->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getCabinet()->getLocation();
        } else {
            return false;
        }

    }

    /**
     * Is the type SwitchPort::TYPE_UNSET?
     *
     * @return bool
     */
    public function isTypeUnset(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_UNSET;
        }
        else{
            return false;
        }
    }

    /**
     * Is the type SwitchPort::TYPE_PEERING?
     *
     * @return bool
     */
    public function isTypePeering(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_PEERING;
        }
        else{
            return false;
        }
    }

    /**
     * Is the type SwitchPort::TYPE_MONITOR?
     *
     * @return bool
     */
    public function isTypeMonitor(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_MONITOR;
        }
        else{
            return false;
        }
    }

    /**
     * Is the type SwitchPort::TYPE_CORE?
     *
     * @return bool
     */
    public function isTypeCore(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_CORE;
        }
        else{
            return false;
        }
    }

    /**
     * Is the type SwitchPort::TYPE_OTHER?
     *
     * @return bool
     */
    public function isTypeOther(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_OTHER;
        }
        else{
            return false;
        }
    }

    /**
     * Is the type SwitchPort::TYPE_MANAGEMENT?
     *
     * @return bool
     */
    public function isTypeManagement(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_MANAGEMENT;
        }
        else{
            return false;
        }
    }

    /**
     * Is the type SwitchPort::TYPE_FANOUT?
     *
     * @return bool
     */
    public function isTypeFanout(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_FANOUT;
        }
        else{
            return false;
        }
    }

    /**
     * Is the type SwitchPort::TYPE_RESELLER?
     *
     * @return bool
     */
    public function isTypeReseller(): bool {
        if( $this->getType() ){
            return $this->getType() === SwitchPort::TYPE_RESELLER;
        }
        else{
            return false;
        }
    }


    /**
     * Turn the database integer representation of the type into text as
     * defined in the SwitchPort::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string {
        return SwitchPort::$TYPES[ $this->getType() ] ?? 'Unknown';
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

    /**
     * Add MACAddresses
     *
     * @param \Entities\MACAddress $mACAddresses
     * @return VirtualInterface
     */
    public function addMACAddress(\Entities\MACAddress $mACAddresses)
    {
        $this->MACAddresses[] = $mACAddresses;

        return $this;
    }

    /**
     * Remove MACAddresses
     *
     * @param \Entities\MACAddress $mACAddresses
     */
    public function removeMACAddress(\Entities\MACAddress $mACAddresses)
    {
        $this->MACAddresses->removeElement($mACAddresses);
    }


    /**
     * Add sflowReceiver
     *
     * @param \Entities\SflowReceiver $sflowReceiver
     *
     * @return VirtualInterface
     */
    public function addSflowReceiver( \Entities\SflowReceiver $sflowReceiver )
    {
        $this->SflowReceivers[] = $sflowReceiver;

        return $this;
    }

    /**
     * Remove sflowReceiver
     *
     * @param \Entities\SflowReceiver $sflowReceiver
     */
    public function removeSflowReceiver( \Entities\SflowReceiver $sflowReceiver )
    {
        $this->SflowReceivers->removeElement( $sflowReceiver );
    }


    /**
     * Get sflowReceivers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSflowReceivers()
    {
        return $this->SflowReceivers;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $SflowReceivers;


    /**
     * Get the speed of the LAG
     *
     * @param bool $connectedOnly Only consider physical interfaces with 'CONNECTED' state
     * @return int
     */
    public function speed( $connectedOnly = true ): int {
        $speed = 0;

        /** @var PhysicalInterface $pi */
        foreach( $this->getPhysicalInterfaces() as $pi ) {
            if( $connectedOnly && !$pi->statusIsConnected() ) {
                continue;
            }
            $speed += $pi->getSpeed();
        }

        return $speed;
    }

    /**
     * Return the core bundle associated to the virtual interface or false
     *
     * @return \Entities\CoreBundle
     */
    public function getCoreBundle() {
        /** @var PhysicalInterface $pi */
        foreach( $this->getPhysicalInterfaces() as $pi ) {
            if( $ci = $pi->getCoreInterface() ) {
                return $ci->getCoreLink()->getCoreBundle();
            }
        }
        return false;
    }

    /**
     * Check if the switch is the same for the physical interfaces of the virtual interface
     *
     * @return bool
     */
    public function sameSwitchForEachPI() {
        $lastSwitch = null;

        /** @var PhysicalInterface $pi */
        foreach( $this->getPhysicalInterfaces() as $pi ){
            if( $lastSwitch === null ) {
                $lastSwitch = $pi->getSwitchPort()->getSwitcher()->getId();
            } elseif( $lastSwitch !== $pi->getSwitchPort()->getSwitcher()->getId() ) {
                return false;
            }
        }

        return true;
    }


    /**
     * Is this LAG graphable?
     *
     * @return bool
     */
    public function isGraphable(): bool {
        foreach( $this->getPhysicalInterfaces() as $pi ) {
            if( $pi->isGraphable() ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convenience function to resolve the infrastructure of a virtual interface
     *
     * @return Infrastructure|null
     */
    public function getInfrastructure()
    {
        foreach( $this->getPhysicalInterfaces() as $pi ) {
            if( $sp = $pi->getSwitchPort() ) {
                return $sp->getSwitcher()->getInfrastructure();
            }
        }

        return null;
    }
}
