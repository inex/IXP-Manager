<?php

namespace Entities;

use Log;

use Entities\{
    PatchPanelPort      as PatchPanelPortEntity,
    PhysicalInterface   as PhysicalInterfaceEntity,
    Switcher            as SwitcherEntity
};

use OSS_SNMP\Exception;
use OSS_SNMP\MIBS\Extreme\Port;
use OSS_SNMP\MIBS\Iface;


use OSS_SNMP\MIBS\MAU as MauMib;

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

    /**
     * For resellers, we need to enforce the one port - one mac - one address rule
     * on the peering LAN. Depending on switch technology, this will be done using
     * a virtual ethernet port or a dedcaited fanout switch. A fanout port is a port
     * that sends a resold member's traffic to a peering port / switch.
     *
     * I.e. it is an output port to the exchange to connects to a standard peering
     * input port.
     *
     * @var int
     */
    const TYPE_FANOUT         = 6;

    /**
     * For resellers, we need an uplink port(s) through which they deliver reseller
     * connections.
     *
     * @var int
     */
    const TYPE_RESELLER       = 7;

    public static $TYPES = array(
        self::TYPE_UNSET      => 'Unset / Unknown',
        self::TYPE_PEERING    => 'Peering',
        self::TYPE_MONITOR    => 'Monitor',
        self::TYPE_CORE       => 'Core',
        self::TYPE_OTHER      => 'Other',
        self::TYPE_MANAGEMENT => 'Management',
        self::TYPE_FANOUT     => 'Fanout',
        self::TYPE_RESELLER   => 'Reseller'
    );

    // This array is for matching data from OSS_SNMP to the switchport database table.
    // See snmpUpdate() below
    public static $SNMP_MAP = [
        'descriptions'    => 'Name',
        'names'           => 'IfName',
        'aliases'         => 'IfAlias',
        'highSpeeds'      => 'IfHighspeed',
        'mtus'            => 'IfMtu',
        'physAddresses'   => 'IfPhysAddress',
        'adminStates'     => 'IfAdminStatus',
        'operationStates' => 'IfOperStatus',
        'lastChanges'     => 'IfLastChange'
    ];


    /**
     * Mappings for OSS_SNMP fucntions to SwitchPort members
     */
    public static $OSS_SNMP_MAU_MAP = [
        'types'             => [ 'fn' => 'MauType',         'xlate' => false ],
        'states'            => [ 'fn' => 'MauState',        'xlate' => true ],
        'mediaAvailable'    => [ 'fn' => 'MauAvailability', 'xlate' => true ],
        'jackTypes'         => [ 'fn' => 'MauJacktype',     'xlate' => true ],
        'autonegSupported'  => [ 'fn' => 'MauAutoNegSupported'  ],
        'autonegAdminState' => [ 'fn' => 'MauAutoNegAdminState' ]
    ];

    /**
     * @var integer $type
     */
    protected $type;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Entities\PhysicalInterface
     */
    protected $PhysicalInterface;

    /**
     * @var \Entities\Switcher
     */
    protected $Switcher;

    /**
     * @var string
     */
    protected $ifName;

    /**
     * @var string
     */
    protected $ifAlias;

    /**
     * @var integer
     */
    protected $ifHighSpeed;

    /**
     * @var integer
     */
    protected $ifMtu;

    /**
     * @var string
     */
    protected $ifPhysAddress;

    /**
     * @var integer
     */
    protected $ifAdminStatus;

    /**
     * @var integer
     */
    protected $ifOperStatus;

    /**
     * @var integer
     */
    protected $ifLastChange;

    /**
     * @var \DateTime
     */
    protected $lastSnmpPoll;

    /**
     * @var integer
     */
    protected $ifIndex;

    /**
     * @var boolean $active
     */
    protected $active;

    /**
     * @var \Entities\PatchPanelPort
     */
    private $patchPanelPort;

    /**
     * @var string
     */
    private $mauType;

    /**
     * @var string
     */
    private $mauState;

    /**
     * @var string
     */
    private $mauAvailability;

    /**
     * @var string
     */
    private $mauJacktype;

    /**
     * @var boolean
     */
    private $mauAutoNegSupported;

    /**
     * @var boolean
     */
    private $mauAutoNegAdminState;

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
     * @param PhysicalInterfaceEntity $physicalInterface
     * @return SwitchPort
     */
    public function setPhysicalInterface( PhysicalInterfaceEntity $physicalInterface = null)
    {
        $this->PhysicalInterface = $physicalInterface;

        return $this;
    }

    /**
     * Get PhysicalInterface
     *
     * @return PhysicalInterface
     */
    public function getPhysicalInterface() {
        return $this->PhysicalInterface;
    }

    /**
     * Set Switcher
     *
     * @param SwitcherEntity $switcher
     * @return SwitchPort
     */
    public function setSwitcher( SwitcherEntity $switcher = null)
    {
        $this->Switcher = $switcher;

        return $this;
    }

    /**
     * Get Switcher
     *
     * @return SwitcherEntity
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


    /**
     * Update switch port details from a SNMP poll of the device.
     *
     * Pass an instance of OSS_Logger if you want logging enabled.
     *
     * @link https://github.com/opensolutions/OSS_SNMP
     *
     *
     * @param \OSS_SNMP\SNMP $host An instance of the SNMP host object
     * @param bool $logger An instance of the logger or false
     *
     * @return \Entities\SwitchPort For fluent interfaces
     *
     * @throws
     */
    public function snmpUpdate( $host, $logger = false ){
        foreach( self::$SNMP_MAP as $snmp => $entity ) {
            $fn = "get{$entity}";

            switch( $snmp ) {
                case 'lastChanges':
                    $n = $host->useIface()->$snmp( true )[ $this->getIfIndex() ];

                    // need to allow for small changes due to rounding errors
                    if( $logger !== false && $this->$fn() != $n && abs( $this->$fn() - $n ) > 60 )
                        Log::info( "[{$this->getSwitcher()->getName()}]:{$this->getName()} [Index: {$this->getIfIndex()}] Updating {$entity} from [{$this->$fn()}] to [{$n}]" );
                    break;

                default:
                    $n = $host->useIface()->$snmp()[ $this->getIfIndex() ];

                    if( $logger !== false && $this->$fn() != $n )
                        Log::info( "[{$this->getSwitcher()->getName()}]:{$this->getName()} [Index: {$this->getIfIndex()}] Updating {$entity} from [{$this->$fn()}] to [{$n}]" );
                    break;
            }

            $fn = "set{$entity}";
            $this->$fn( $n );
        }


        if( $this->getSwitcher()->getMauSupported() ) {
            foreach( self::$OSS_SNMP_MAU_MAP as $snmp => $entity ) {
                $getfn = "get{$entity['fn']}";
                $setfn = "set{$entity['fn']}";

                try {
                    if( isset( $entity['xlate'] ) ) {
                        $n = $host->useMAU()->$snmp( $entity['xlate'] );
                        $n = isset( $n[ $this->getIfIndex() ] ) ? $n[ $this->getIfIndex() ] : null;
                    } else {
                        $n = $host->useMAU()->$snmp();
                        $n = isset( $n[ $this->getIfIndex() ] ) ? $n[ $this->getIfIndex() ] : null;
                    }

                } catch( Exception $e ) {
                    // looks like the switch supports MAU but not all of the MIBs
                    if( $logger !== false ) {
                        Log::debug( "[{$this->getSwitcher()->getName()}]:{$this->getName()} [Index: {$this->getIfIndex()}] MAU MIB for {$fn} not supported" );
                    }
                    $n = null;
                }

                if( $snmp == 'types' ) {
                    if( isset( MauMib::$TYPES[ $n ] ) ) {
                        $n = MauMib::$TYPES[ $n ];
                    } else if( $n === null || $n === '.0.0' ) {
                        $n = '(empty)';
                    } else {
                        $n = '(unknown type - oid: ' . $n . ')';
                    }
                }

                if( $this->$getfn() != $n ) {
                    Log::info( "[{$this->getSwitcher()->getName()}]:{$this->getName()} [Index: {$this->getIfIndex()}] Updating {$entity['fn']} from [{$this->$getfn()}] to [{$n}]" );
                }

                $this->$setfn( $n );
            }
        }

        try {
            // not all switches support this
            // FIXME is there a vendor agnostic way of doing this?

            // are we a LAG port?
            $isAggregatePorts = $host->useLAG()->isAggregatePorts();

            if( isset( $isAggregatePorts[ $this->getIfIndex() ] ) && $isAggregatePorts[ $this->getIfIndex() ] )
                $this->setLagIfIndex( $host->useLAG()->portAttachedIds()[ $this->getIfIndex() ] );
            else
                $this->setLagIfIndex( null );

        } catch( Exception $e ){}

        $this->setLastSnmpPoll( new \DateTime() );

        return $this;
    }



    public function ifnameToSNMPIdentifier()
    {
        # escape special characters in ifName as per
        # http://oss.oetiker.ch/mrtg/doc/mrtg-reference.en.html - "Interface by Name" section

        $ifname = preg_replace( '/:/', '\\:', $this->getIfName() );
        $ifname = preg_replace( '/&/', '\\&', $ifname );
        $ifname = preg_replace( '/@/', '\\@', $ifname );
        $ifname = preg_replace( '/\ /', '\\\ ', $ifname );

        return $ifname;
    }

    /**
     * @var integer
     */
    private $lagIfIndex;


    /**
     * Set lagIfIndex
     *
     * @param integer $lagIfIndex
     * @return SwitchPort
     */
    public function setLagIfIndex($lagIfIndex)
    {
        $this->lagIfIndex = $lagIfIndex;

        return $this;
    }

    /**
     * Get lagIfIndex
     *
     * @return integer
     */
    public function getLagIfIndex()
    {
        return $this->lagIfIndex;
    }



    /**
     * Set mauType
     *
     * @param string $mauType
     * @return SwitchPort
     */
    public function setMauType($mauType)
    {
        $this->mauType = $mauType;

        return $this;
    }

    /**
     * Get mauType
     *
     * @return string
     */
    public function getMauType()
    {
        return $this->mauType;
    }

    /**
     * Set mauState
     *
     * @param string $mauState
     * @return SwitchPort
     */
    public function setMauState($mauState)
    {
        $this->mauState = $mauState;

        return $this;
    }

    /**
     * Get mauState
     *
     * @return string
     */
    public function getMauState()
    {
        return $this->mauState;
    }

    /**
     * Set mauAvailability
     *
     * @param string $mauAvailability
     * @return SwitchPort
     */
    public function setMauAvailability($mauAvailability)
    {
        $this->mauAvailability = $mauAvailability;

        return $this;
    }

    /**
     * Get mauAvailability
     *
     * @return string
     */
    public function getMauAvailability()
    {
        return $this->mauAvailability;
    }

    /**
     * Set mauJacktype
     *
     * @param string $mauJacktype
     * @return SwitchPort
     */
    public function setMauJacktype($mauJacktype)
    {
        $this->mauJacktype = $mauJacktype;

        return $this;
    }

    /**
     * Get mauJacktype
     *
     * @return string
     */
    public function getMauJacktype()
    {
        return $this->mauJacktype;
    }

    /**
     * Set mauAutoNegSupported
     *
     * @param boolean $mauAutoNegSupported
     * @return SwitchPort
     */
    public function setMauAutoNegSupported($mauAutoNegSupported)
    {
        $this->mauAutoNegSupported = $mauAutoNegSupported;

        return $this;
    }

    /**
     * Get mauAutoNegSupported
     *
     * @return boolean
     */
    public function getMauAutoNegSupported()
    {
        return $this->mauAutoNegSupported;
    }

    /**
     * Set mauAutoNegAdminState
     *
     * @param boolean $mauAutoNegAdminState
     * @return SwitchPort
     */
    public function setMauAutoNegAdminState($mauAutoNegAdminState)
    {
        $this->mauAutoNegAdminState = $mauAutoNegAdminState;

        return $this;
    }

    /**
     * Get mauAutoNegAdminState
     *
     * @return boolean
     */
    public function getMauAutoNegAdminState()
    {
        return $this->mauAutoNegAdminState;
    }

    /**
     * Get the appropriate OID for in octets
     * @return string
     */
    public function oidInOctets(): string {
        return Iface::OID_IF_HC_IN_OCTETS;
    }

    /**
     * Get the appropriate OID for out octets
     * @return string
     */
    public function oidOutOctets(): string {
        return Iface::OID_IF_HC_OUT_OCTETS;
    }

    /**
     * Get the appropriate OID for in unicast packets
     * @return string
     */
    public function oidInUnicastPackets(): string {
        return Iface::OID_IF_HC_IN_UNICAST_PACKETS;
    }

    /**
     * Get the appropriate OID for out unicast packets
     * @return string
     */
    public function oidOutUnicastPackets(): string {
        return Iface::OID_IF_HC_OUT_UNICAST_PACKETS;
    }

    /**
     * Get the appropriate OID for in errors
     * @return string
     */
    public function oidInErrors(): string {
        return Iface::OID_IF_IN_ERRORS;
    }

    /**
     * Get the appropriate OID for out errors
     * @return string
     */
    public function oidOutErrors(): string {
        return Iface::OID_IF_OUT_ERRORS;
    }

    /**
     * Get the appropriate OID for in discards
     * @return string
     */
    public function oidInDiscards(): string {
        return Iface::OID_IF_IN_DISCARDS;
    }

    /**
     * Get the appropriate OID for out discards
     * @return string
     */
    public function oidOutDiscards(): string {
        switch( $this->getSwitcher()->getOs() ) {
            case 'ExtremeXOS':
                return Port::OID_PORT_CONG_DROP_PKTS;
                break;

            default:
                return Iface::OID_IF_OUT_DISCARDS;
                break;
        }

    }

    /**
     * Get the appropriate OID for in broadcasts
     * @return string
     */
    public function oidInBroadcasts(): string {
        return Iface::OID_IF_HC_IN_BROADCAST;
    }

    /**
     * Get the appropriate OID for out broadcasts
     * @return string
     */
    public function oidOutBroadcasts(): string {
        return Iface::OID_IF_HC_OUT_BROADCAST;
    }

    /**
     * Is this an unset port?
     * @return boolean
     */
    public function isTypeUnset() {
        return $this->getType() == self::TYPE_UNSET;
    }

    /**
     * Is this a peering port?
     * @return boolean
     */
    public function isTypePeering() {
        return $this->getType() == self::TYPE_PEERING;
    }

    /**
     * Is this a reseller port?
     * @return boolean
     */
    public function isTypeReseller() {
        return $this->getType() == self::TYPE_RESELLER;
    }

    /**
     * Is this a core port?
     * @return boolean
     */
    public function isTypeCore() {
        return $this->getType() == self::TYPE_CORE;
    }

    /**
     * Is this a fanout port?
     * @return boolean
     */
    public function isTypeFanout() {
        return $this->getType() == self::TYPE_FANOUT;
    }

    /**
     * Set patchPanelPort
     *
     * @param PatchPanelPortEntity $patchPanelPort
     *
     * @return SwitchPort
     */
    public function setPatchPanelPort( PatchPanelPortEntity $patchPanelPort = null)
    {
        $this->patchPanelPort = $patchPanelPort;
        return $this;
    }

    /**
     * Get patchPanelPort
     *
     * @return PatchPanelPortEntity
     */
    public function getPatchPanelPort()
    {
        return $this->patchPanelPort;
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string {
        return self::$TYPES[ $this->getType() ];
    }
}
