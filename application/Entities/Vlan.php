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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $NetInfo;

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
    


    /**
     * Add NetInfo
     *
     * @param \Entities\NetInfo $netInfo
     * @return Vlan
     */
    public function addNetInfo(\Entities\NetInfo $netInfo)
    {
        $this->NetInfo[] = $netInfo;
    
        return $this;
    }

    /**
     * Remove NetInfo
     *
     * @param \Entities\NetInfo $netInfo
     */
    public function removeNetInfo(\Entities\NetInfo $netInfo)
    {
        $this->NetInfo->removeElement($netInfo);
    }

    /**
     * Get NetInfo
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNetInfos()
    {
        return $this->NetInfo;
    }

    /**
     * Return the entity object of NetInfo
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol Sets one of \Entities\NetInfo Protocols
     * @param int    $index    Ddefault 0 If an indexed preference, get a specific index (default: 0)
     * @return \Entities\NetInfo|bool Returns net info object or false
     * @throws Exception Unknown protocol.
     */
    public function loadNetInfo( $property, $protocol, $index = 0 )
    {
        if( !isset( \Entities\NetInfo::$PROTOCOLS[ $protocol ] ) )
            throw new Exception( "Unknown protocol" );

        foreach( $this->getNetInfos() as $pref )
        {
            if( $pref->getProperty() == $property && $pref->getIx() == $index && $pref->getProtocol() == $protocol )
            {
                return $pref;
            }
        }

        return false;
    }

    /**
     * Set (or update) a NetInfo
     *
     * @param string $property The preference name
     * @param string $value    The value to assign to the preference
     * @param int    $protocol Sets one of \Entities\NetInfo Protocols
     * @param int    $index    default 0 If an indexed preference, set a specific index number. Default 0.
     * @return \Entities\NetInfo|bool Returns net info object or false
     * @throws Exception Unknown protocol.
     */
    public function setNetInfo( $property, $value, $protocol, $index = 0 )
    {
        if( !isset( \Entities\NetInfo::$PROTOCOLS[ $protocol ] ) )
            throw new Exception( "Unknown protocol" );

        $pref = $this->loadNetInfo( $property, $protocol, $index );

        if( $pref )
        {
            $pref->setValue( $value );
            $pref->setProtocol( $protocol );
            $pref->setIx( $index );

            return $this;
        }

        $pref = $this->_createNetInfoEntity( $this );
        $pref->setProperty( $property );
        $pref->setProtocol( $protocol );
        $pref->setValue( $value );
        $pref->setIx( $index );

        $em = \Zend_Registry::get( 'd2em' )[ 'default' ];
        $em->persist( $pref );
        return $this;
    }

    /**
     * Add an indexed NetInfo
     *
     * @param string $property The preference name
     * @param string $value The value to assign to the preference
     * @param int    $protocol Sets one of \Entities\NetInfo Protocols
     * @return \Entities\NetInfo|bool Returns net info object or false
     * @throws Exception Unknown protocol
     */
    public function addIndexedNetInfo( $property, $value, $protocol )
    {
        if( !isset( \Entities\NetInfo::$PROTOCOLS[ $protocol ] ) )
            throw new Exception( "Unknown protocol" );

        // what's the current highest index and how many is there?
        $highest = -1;

        foreach( $this->getNetInfos() as $pref )
        {
            if( $pref->getProperty() == $property && $pref->getProtocol() == $protocol )
            {
                if( $pref->getIx() > $highest )
                    $highest = $pref->getIx();
            }
        }

        $em = \Zend_Registry::get( 'd2em' )[ 'default' ];
        if( is_array( $value ) )
        {
            foreach( $value as $v )
            {
                $pref = $this->_createNetInfoEntity( $this );
                $pref->setProperty( $property );
                $pref->setProtocol( $protocol );
                $pref->setValue( $v );
                $pref->setIx( ++$highest );
                
                $em->persist( $pref );
            }
        }
        else
        {
            $pref = $this->_createNetInfoEntity( $this );
            $pref->setProperty( $property );
            $pref->setProtocol( $protocol );
            $pref->setValue( $value );
            $pref->setIx( ++$highest );

            $em->persist( $pref );
        }

        return $this;
    }

    /**
     * Creates \Entities\NetInfo object.
     *
     * @return \Entities\NetInfo
     */
    private function _createNetInfoEntity( $owner = null )
    {
        $pref = new \Entities\NetInfo();

        if( $owner != null )
        {
            $pref->setVlan( $owner );
            $owner->addNetInfo( $pref );
        }

        return $pref;
    }
}