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

    const PROTOCOL_IPv4 = 4;
    const PROTOCOL_IPv6 = 6;


    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var integer $number
     */
    protected $number;

    /**
     * @var string $rcvrfname
     */
    protected $rcvrfname;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $VlanInterfaces;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $IPv4Addresses;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $IPv6Addresses;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $NetworkInfo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $NetInfo;

    /**
     * @var \Entities\Infrastructure
     */
    private $Infrastructure;

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
    public function addIPv4Addresses(\Entities\IPv4Address $iPv4Addresses)
    {
        $this->IPv4Addresses[] = $iPv4Addresses;

        return $this;
    }

    /**
     * Remove IPv4Addresses
     *
     * @param Entities\IPv4Address $iPv4Addresses
     */
    public function removeIPv4Addresses(\Entities\IPv4Address $iPv4Addresses)
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
    public function addIPv6Addresses(\Entities\IPv6Address $iPv6Addresses)
    {
        $this->IPv6Addresses[] = $iPv6Addresses;

        return $this;
    }

    /**
     * Remove IPv6Addresses
     *
     * @param Entities\IPv6Address $iPv6Addresses
     */
    public function removeIPv6Addresses(\Entities\IPv6Address $iPv6Addresses)
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
    protected $private;

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
     * Get subent size for given protocol
     *
     * Returns string if subnet sizes was set and false otherwise
     *
     * @param int    $protocol The protocol to check for. One of \Entities\NetInfo::PROTOCOL_IPV constants.
     * @return string|bool
     */
    public function getSubnetSize( $protocol )
    {
        return $this->getNetInfo( 'masklen', $protocol );
    }

    /**
     * Get route servers array for given protocol
     *
     * Return array structure: [
     *    $ix1 => [ 'ipaddress' => $ip, 'type' => $type ],
     *    $ix2 => [ 'ipaddress' => $ip, 'type' => $type ],
     *    ... ]
     *
     * If no route servers are set returns empty array
     *
     * @param int    $protocol The protocol to check for. One of \Entities\NetInfo::PROTOCOL_IPV constants.
     * @return array
     */
    public function getRouteServers( $protocol )
    {
        return $this->getAssocNetInfo( 'routeserver', $protocol );
    }

    /**
     * Get dns file for given protocol
     *
     * Returns string if dns file was set and false otherwise
     *
     * @param int    $protocol The protocol to check for. One of \Entities\NetInfo::PROTOCOL_IPV constants.
     * @return string|bool
     */
    public function getDnsFile( $protocol )
    {
        return $this->getNetInfo( 'dnsfile', $protocol );
    }

    /**
     * Get array off as 112 servers for given protocol
     *
     * Returns array of servers adddesses or empty array if nothig was fonud.
     * Return array structure: [ $ix1 => $address1, $ix2 => $address2 ],
     *
     * @param int    $protocol The protocol to check for. One of \Entities\NetInfo::PROTOCOL_IPV constants.
     * @return array
     */
    public function getAS112Servers( $protocol )
    {
        return $this->getIndexedNetInfo( 'as112server', $protocol );
    }

    /**
     * Get array off route collectors for given protocol
     *
     * Returns array of route collectors or empty array if nothig was fonud.
     * Return array structure: [ $ix1 => $rcollector1, $ix2 => $rcollector2 ],
     *
     * @param int    $protocol The protocol to check for. One of \Entities\NetInfo::PROTOCOL_IPV constants.
     * @return array
     */
    public function getRouteCollectors( $protocol )
    {
        return $this->getIndexedNetInfo( 'routecollector', $protocol );
    }

    /**
     * Get ping beacons array for given protocol
     *
     * Return array structure: [
     *    $ix1 => [ 'ipaddress' => $ip, 'ratelimited' => $ratelimited ],
     *    $ix2 => [ 'ipaddress' => $ip, 'ratelimited' => $ratelimited ],
     *    ... ]
     *
     * If no route servers are set returns empty array
     *
     * @param int    $protocol The protocol to check for. One of \Entities\NetInfo::PROTOCOL_IPV constants.
     * @return array
     */
    public function getPingBeacons( $protocol )
    {
        return $this->getAssocNetInfo( 'pingbeacon', $protocol );
    }

    /**
     * Return the entity object of NetInfo
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol The protocol to check for
     * @param int    $index    Ddefault 0 If an indexed preference, get a specific index (default: 0)
     * @return \Entities\NetInfo|bool Returns net info object or false
     */
    public function loadNetInfo( $property, $protocol, $index = 0 )
    {
        foreach( $this->getNetInfos() as $pref )
        {
            if( $pref->getProperty() == $property && $pref->getIx() == $index && $pref->getProtocol() == $protocol )
                return $pref;
        }

        return false;
    }

    /**
     * Delete the named Net infos
     *
     * WARNING: You need to EntityManager#flush() if the return >0!
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol The protocol to check for
     * @param int    $index default null If an indexed preference then delete a specific index, if null then delete all
     * @return int The number of preferences deleted
     */
    public function deleteNetInfo( $property, $protocol, $index = null )
    {
        $count = 0;
        $em = \Zend_Registry::get( 'd2em' )[ 'default' ];
        foreach( $this->getNetInfos() as $pref )
        {
            if( $pref->getProperty() == $property && $pref->getProtocol() == $protocol )
            {
                if( $index === null || $pref->getIx() == $index )
                {
                    $count++;
                    $this->getNetInfos()->removeElement( $pref );
                    $em->remove( $pref );
                }
            }
        }

        return $count;
    }


    /**
     * Does the named net info exist or not?
     *
     * WARNING: Evaluate the return of this function using !== or === as a preference such as '0'
     * will evaluate as false otherwise.
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol The protocol to check for
     * @param int    $index default 0 If an indexed preference, get a specific index (default: 0)
     * @return boolean|string If the named preference is not defined or has expired, returns FALSE; otherwise it returns the preference
     * @see getNetInfo()
     */
    public function hasNetInfo( $property, $protocol, $index = 0 )
    {
        return $this->getNetInfo( $property, $protocol, $index );
    }

    /**
     * Get the named net info
     *
     * WARNING: Evaluate the return of this function using !== or === as a preference such as '0'
     * will evaluate as false otherwise.
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol The protocol to check for
     * @param int    $index default 0 If an indexed preference, get a specific index (default: 0)
     * @return boolean|string If the named preference is not defined or has expired, returns FALSE; otherwise it returns the preference
     */
    public function getNetInfo( $property, $protocol, $index = 0 )
    {
        foreach( $this->getNetInfos() as $pref )
        {
            if( $pref->getProperty() == $property && $pref->getIx() == $index && $pref->getProtocol() == $protocol )
                return $pref->getValue();
        }

        return false;
    }

    /**
     * Get indexed preferences as an array
     *
     * The standard response is an array of scalar values such as:
     *
     *     array( 'a', 'b', 'c' );
     *
     * If $withIndex is set to true, then it will be an array of associated arrays with the
     * index included:
     *
     *     array(
     *         array( 'p_index' => '0', 'p_value' => 'a' ),
     *         array( 'p_index' => '1', 'p_value' => 'b' ),
     *         array( 'p_index' => '2', 'p_value' => 'c' )
     *     );
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol The protocol to check for
     * @param boolean $withIndex default false Include index values. Default false.
     * @return array
     */
    public function getIndexedNetInfo( $property, $protocol, $withIndex = false )
    {
        $values = [];

        foreach( $this->getNetInfos() as $pref )
        {
            if( $pref->getProperty() == $property && $pref->getProtocol() == $protocol )
            {
                if( $withIndex )
                    $values[ $pref->getIx() ] = array( 'p_index' => $pref->getIx(), 'p_value' => $pref->getValue() );
                else
                    $values[ $pref->getIx() ] = $pref->getValue();
            }
        }

        ksort( $values, SORT_NUMERIC );
        return $values;
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

        // what's the current highest index and how many are there?
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
     * Get associative net infos as an array.
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol The protocol to check for
     * @param int    $index If an indexed preference, get a specific index, null means all indexes alowed (default: null)
     * @return boolean|array False if no such preference(s) exist, otherwise an array.
     */
    public function getAssocNetInfo( $property, $protocol, $index = null )
    {
        $values = [];

        foreach( $this->getNetInfos() as $pref )
        {
            if( strpos( $pref->getProperty(), $property ) === 0 && $pref->getProtocol() == $protocol )
            {
                if( $index == null || $pref->getIx() == $index )
                {
                    if( strpos( $pref->getProperty(), "." ) !== false )
                        $key = substr( $pref->getProperty(), strlen( $property ) + 1 );

                    if( $key )
                    {
                        $key = "{$pref->getIx()}.{$key}";
                        $values = $this->_processKey( $values, $key, $pref->getValue() );
                    }
                    else
                        $values[ $pref->getIx() ] = $pref->getValue();
                }
            }
        }

        return $values;
    }

    /**
     * Delete the named preference
     *
     * @param string $property The named attribute / preference to check for
     * @param int    $protocol The protocol to check for
     * @param int    $index default null If an indexed preference then delete a specific index, if null then delete all
     * @return int The number of preferences deleted
     */
    public function deleteAssocNetInfo( $property, $protocol, $index = null )
    {
        $cnt = 0;

        $em = \Zend_Registry::get( 'd2em' )[ 'default' ];
        foreach( $this->getNetInfos() as $pref )
        {
            if( strpos( $pref->getProperty(), $property ) === 0 && $pref->getProtocol() == $protocol )
            {
                if( $index == null || $pref->getIx() == $index )
                {
                    $this->getNetInfos()->removeElement( $pref );
                    $em->remove( $pref );
                    $cnt++;
                }
            }
        }

        return $cnt;
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

    /**
     * Assign the key's value to the property list. Handles the
     * nest separator for sub-properties.
     *
     * @param  array  $config
     * @param  string $key
     * @param  string $value
     * @return array
     * @throws Exception
     */
    private function _processKey($config, $key, $value)
    {
        if( strpos( $key, "." ) !== false)
        {
            $pieces = explode( ".", $key, 2 );
            if( strlen( $pieces[0] ) && strlen( $pieces[1] ) )
            {
                if( !isset( $config[ $pieces[0] ] ) )
                {
                    if( $pieces[0] === '0' && !empty( $config ) )
                        $config = [ $pieces[0] => $config ];
                    else
                        $config[ $pieces[0] ] = array();
                }
                elseif( !is_array( $config[$pieces[0]] ) )
                {
                    throw new Exception("Cannot create sub-key for '{$pieces[0]}' as key already exists");
                }
                $config[ $pieces[0] ] = $this->_processKey( $config[ $pieces[0] ], $pieces[1], $value );
            }
            else
            {
                throw new Exception( "Invalid key '$key'" );
            }
        }
        else
        {
            $config[$key] = $value;
        }
        return $config;
    }

    /**
     * Set Infrastructure
     *
     * @param \Entities\Infrastructure $infrastructure
     * @return Vlan
     */
    public function setInfrastructure(\Entities\Infrastructure $infrastructure = null)
    {
        $this->Infrastructure = $infrastructure;

        return $this;
    }

    /**
     * Get Infrastructure
     *
     * @return \Entities\Infrastructure
     */
    public function getInfrastructure()
    {
        return $this->Infrastructure;
    }

    /**
     * Add IPv4Addresses
     *
     * @param \Entities\IPv4Address $iPv4Addresses
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
     * @param \Entities\IPv4Address $iPv4Addresses
     */
    public function removeIPv4Addresse(\Entities\IPv4Address $iPv4Addresses)
    {
        $this->IPv4Addresses->removeElement($iPv4Addresses);
    }

    /**
     * Add IPv6Addresses
     *
     * @param \Entities\IPv6Address $iPv6Addresses
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
     * @param \Entities\IPv6Address $iPv6Addresses
     */
    public function removeIPv6Addresse(\Entities\IPv6Address $iPv6Addresses)
    {
        $this->IPv6Addresses->removeElement($iPv6Addresses);
    }
    /**
     * @var boolean
     */
    private $peering_matrix = 0;

    /**
     * @var boolean
     */
    private $peering_manager = 0;


    /**
     * Set peering_matrix
     *
     * @param boolean $peeringMatrix
     * @return Vlan
     */
    public function setPeeringMatrix($peeringMatrix)
    {
        $this->peering_matrix = $peeringMatrix;

        return $this;
    }

    /**
     * Get peering_matrix
     *
     * @return boolean
     */
    public function getPeeringMatrix()
    {
        return $this->peering_matrix;
    }

    /**
     * Set peering_manager
     *
     * @param boolean $peeringManager
     * @return Vlan
     */
    public function setPeeringManager($peeringManager)
    {
        $this->peering_manager = $peeringManager;

        return $this;
    }

    /**
     * Get peering_manager
     *
     * @return boolean
     */
    public function getPeeringManager()
    {
        return $this->peering_manager;
    }
}