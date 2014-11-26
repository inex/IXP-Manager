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
     * Elements for SNMP polling via the OSS_SNMP library
     *
     * These are used to build function names
     *
     * @see snmpPoll() below
     * @var array Elements for SNMP polling via the OSS_SNMP library
     */
    public static $OSS_SNMP_SWITCH_ELEMENTS = [
        'Model',
        'Os',
        'OsDate',
        'OsVersion',
        'SerialNumber'
    ];

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $ipv4addr
     */
    protected $ipv4addr;

    /**
     * @var string $ipv6addr
     */
    protected $ipv6addr;

    /**
     * @var string $snmppasswd
     */
    protected $snmppasswd;

    /**
     * @var \Entities\Infrastructure
     */
    protected $Infrastructure;

    /**
     * @var integer $switchtype
     */
    protected $switchtype;

    /**
     * @var string $model
     */
    protected $model;

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
    protected $Ports;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $ConsoleServerConnections;

    /**
     * @var Entities\Cabinet
     */
    protected $Cabinet;

    /**
     * @var Entities\Vendor
     */
    protected $Vendor;

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
     * @param \Entities\Infrastructure $infrastructure
     * @return Switcher
     */
    public function setInfrastructure($infrastructure)
    {
        $this->Infrastructure = $infrastructure;

        return $this;
    }

    /**
     * Get infrastructure
     *
     * @return \Entities\Infrastructure
     */
    public function getInfrastructure()
    {
        return $this->Infrastructure;
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
    protected $SecEvents;


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
    protected $active;


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
    protected $hostname;


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
    protected $os;

    /**
     * @var \DateTime
     */
    protected $osDate;

    /**
     * @var string
     */
    protected $osVersion;

    /**
     * @var \DateTime
     */
    protected $lastPolled;


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



    /**
     * Update switch's details using SNMP polling
     *
     * @throws \OSS_SNMP\Exception
     * @see self::$OSS_SNMP_SWITCH_ELEMENTS
     *
     * @param \OSS_SNMP\SNMP $host An instance of \OSS_SNMP\SNMP for this switch
     * @param \OSS_Logger $logger An instance of the logger or false
     * @return \Entities\Switcher For fluent interfaces
     */
    public function snmpPoll( $host, $logger = false )
    {
        // utility to format dates
        $formatDate = function( $d ) {
            return $d instanceof \DateTime ? $d->format( 'Y-m-d H:i:s' ) : 'Unknown';
        };

        foreach( self::$OSS_SNMP_SWITCH_ELEMENTS as $p )
        {
            $fn = "get{$p}";
            $n = $host->getPlatform()->$fn();

            if( $logger )
            {
                switch( $p )
                {
                    case 'OsDate':
                        if( $formatDate( $this->$fn() ) != $formatDate( $n ) )
                            $logger->info( " [{$this->getName()}] Platform: Updating {$p} from " . $formatDate( $this->$fn() ) . " to " . $formatDate( $n ) );
                        else
                            $logger->info( " [{$this->getName()}] Platform: Found {$p}: " . $formatDate( $n ) );
                        break;

                    default:
                        if( $logger && $this->$fn() != $n )
                            $logger->info( " [{$this->getName()}] Platform: Updating {$p} from {$this->$fn()} to {$n}" );
                        else
                            $logger->info( " [{$this->getName()}] Platform: Found {$p}: {$n}" );
                        break;
                }
            }

            $fn = "set{$p}";
            $this->$fn( $n );
        }

        $this->setLastPolled( new \DateTime() );
        return $this;
    }



    /**
     * Update a switches ports using SNMP polling
     *
     * There is an optional ``$results`` array which can be passed by reference. If
     * so, it will be indexed by the SNMP port index (or a decresing nagative index
     * beginning -1 if the port only exists in the database). The contents of this
     * associative array is:
     *
     *     "port"   => \Entities\SwitchPort object
     *     "bullet" =>
     *         - false for existing ports
     *         - "new" for newly found ports
     *         - "db" for ports that exist in the database only
     *
     * **Note:** It is assumed that the Doctrine2 Entity Manager is available in the
     * Zend registry as ``d2em`` in this function.
     *
     * @throws \OSS_SNMP\Exception
     *
     * @param \OSS_SNMP\SNMP $host An instance of \OSS_SNMP\SNMP for this switch
     * @param \OSS_Logger $logger An instance of the logger or false
     * @param array Call by reference to an array in which to store results as outlined above
     * @return \Entities\Switcher For fluent interfaces
     */
    public function snmpPollSwitchPorts( $host, $logger = false, &$result = false )
    {
        // clone the ports currently known to this switch as we'll be playing with this array
        $existingPorts = clone $this->getPorts();

        // iterate over all the ports discovered on the switch:
        foreach( $host->useIface()->indexes() as $index )
        {
            // we're only interested in Ethernet ports here (right?)
            if( $host->useIface()->types()[ $index ] != \OSS_SNMP\MIBS\Iface::IF_TYPE_ETHERNETCSMACD )
                continue;

            // find the matching switchport that may already be in the database (or create a new one)
            $switchport = false;

            foreach( $existingPorts as $ix => $ep )
            {
                if( $ep->getIfIndex() == $index )
                {
                    $switchport = $ep;
                    if( is_array( $result ) ) $result[ $index ] = [ "port" => $switchport, 'bullet' => false ];
                    if( $logger ) { $logger->info( " - {$this->getName()} - found pre-existing port for ifIndex {$index}" ); };

                    // remove this from the array so later we'll know what ports exist only in the database
                    unset( $existingPorts[ $ix ] );
                    break;
                }
            }

            $new = false;
            if( !$switchport )
            {
                // no existing port in database so we have found a new port
                $switchport = new \Entities\SwitchPort();

                $switchport->setSwitcher( $this );
                $this->addPort( $switchport );

                $switchport->setType( \Entities\SwitchPort::TYPE_UNSET );
                $switchport->setIfIndex( $index );
                $switchport->setActive( true );

                \Zend_Registry::get( 'd2em' )['default']->persist( $switchport );

                if( is_array( $result ) ) $result[ $index ] = [ "port" => $switchport, 'bullet' => "new" ];
                $new = true;

                if( $logger ) { $logger->info( "Found new port for {$this->getName()} with index $index" ); };
            }

            // update / set port details from SNMP
            $switchport->snmpUpdate( $host, $logger );
        }

        if( count( $existingPorts ) )
        {
            $i = -1;
            foreach( $existingPorts as $ep )
            {
                if( is_array( $result ) ) $result[ $i-- ] = [ "port" => $ep, 'bullet' => "db" ];
                if( $logger ) { $logger->warn( "{$this->getName()} - port found in database with no matching port on the switch: "
                        . " [{$ep->getId()}] {$ep->getName()}" ); };
            }
        }

        return $this;
    }

    /**
     * @var string
     */
    private $serialNumber;


    /**
     * Set serialNumber
     *
     * @param string $serialNumber
     * @return Switcher
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }
}