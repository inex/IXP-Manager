<?php

namespace IXP\Models;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
*/

use Log;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo,
    Relations\HasOne
};

use OSS_SNMP\MIBS\{
    Extreme\Port,
    Iface,
    MAU as MauMib
};

use OSS_SNMP\SNMP;

/**
 * IXP\Models\SwitchPort
 *
 * @property int $id
 * @property int|null $switchid
 * @property int|null $type
 * @property string|null $name
 * @property string|null $ifName
 * @property string|null $ifAlias
 * @property int|null $ifHighSpeed
 * @property int|null $ifMtu
 * @property string|null $ifPhysAddress
 * @property int|null $ifAdminStatus
 * @property int|null $ifOperStatus
 * @property int|null $ifLastChange
 * @property string|null $lastSnmpPoll
 * @property int|null $ifIndex
 * @property int $active
 * @property int|null $lagIfIndex
 * @property string|null $mauType
 * @property string|null $mauState
 * @property string|null $mauAvailability
 * @property string|null $mauJacktype
 * @property int|null $mauAutoNegSupported
 * @property int|null $mauAutoNegAdminState
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\PatchPanelPort|null $patchPanelPort
 * @property-read \IXP\Models\PhysicalInterface|null $physicalInterface
 * @property-read \IXP\Models\Switcher|null $switcher
 * @method static Builder|SwitchPort newModelQuery()
 * @method static Builder|SwitchPort newQuery()
 * @method static Builder|SwitchPort query()
 * @method static Builder|SwitchPort whereActive($value)
 * @method static Builder|SwitchPort whereCreatedAt($value)
 * @method static Builder|SwitchPort whereId($value)
 * @method static Builder|SwitchPort whereIfAdminStatus($value)
 * @method static Builder|SwitchPort whereIfAlias($value)
 * @method static Builder|SwitchPort whereIfHighSpeed($value)
 * @method static Builder|SwitchPort whereIfIndex($value)
 * @method static Builder|SwitchPort whereIfLastChange($value)
 * @method static Builder|SwitchPort whereIfMtu($value)
 * @method static Builder|SwitchPort whereIfName($value)
 * @method static Builder|SwitchPort whereIfOperStatus($value)
 * @method static Builder|SwitchPort whereIfPhysAddress($value)
 * @method static Builder|SwitchPort whereLagIfIndex($value)
 * @method static Builder|SwitchPort whereLastSnmpPoll($value)
 * @method static Builder|SwitchPort whereMauAutoNegAdminState($value)
 * @method static Builder|SwitchPort whereMauAutoNegSupported($value)
 * @method static Builder|SwitchPort whereMauAvailability($value)
 * @method static Builder|SwitchPort whereMauJacktype($value)
 * @method static Builder|SwitchPort whereMauState($value)
 * @method static Builder|SwitchPort whereMauType($value)
 * @method static Builder|SwitchPort whereName($value)
 * @method static Builder|SwitchPort whereSwitchid($value)
 * @method static Builder|SwitchPort whereType($value)
 * @method static Builder|SwitchPort whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SwitchPort extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'switchport';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'switchid',
        'type',
        'name',
        'ifName',
        'ifAlias',
        'ifHighSpeed',
        'ifMtu',
        'ifPhysAddress',
        'ifAdminStatus',
        'ifOperStatus',
        'ifLastChange',
        'lastSnmpPoll',
        'ifIndex',
        'active',
        'lagIfIndex',
        'mauType',
        'mauState',
        'mauAvailability',
        'mauJacktype',
        'mauAutoNegSupported',
        'mauAutoNegAdminState',
    ];

    public const TYPE_UNSET          = 0;
    public const TYPE_PEERING        = 1;
    public const TYPE_MONITOR        = 2;
    public const TYPE_CORE           = 3;
    public const TYPE_OTHER          = 4;
    public const TYPE_MANAGEMENT     = 5;

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
    public const TYPE_FANOUT         = 6;

    /**
     * For resellers, we need an uplink port(s) through which they deliver reseller
     * connections.
     *
     * @var int
     */
    public const TYPE_RESELLER       = 7;

    public static $TYPES = [
        self::TYPE_UNSET      => 'Unset / Unknown',
        self::TYPE_PEERING    => 'Peering',
        self::TYPE_MONITOR    => 'Monitor',
        self::TYPE_CORE       => 'Core',
        self::TYPE_OTHER      => 'Other',
        self::TYPE_MANAGEMENT => 'Management',
        self::TYPE_FANOUT     => 'Fanout',
        self::TYPE_RESELLER   => 'Reseller'
    ];

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
     * Mappings for OSS_SNMP functions to SwitchPort members
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
     * Get the switcher that own the switch port
     */
    public function switcher(): BelongsTo
    {
        return $this->belongsTo(Switcher::class, 'switchid' );
    }

    /**
     * Get the patch panel ports for this switch port
     */
    public function physicalInterface(): HasOne
    {
        return $this->hasOne(PhysicalInterface::class, 'switchportid' );
    }

    /**
     * Get the patch panel ports for this switch port
     */
    public function patchPanelPort(): HasOne
    {
        return $this->hasOne(PatchPanelPort::class, 'switch_port_id' );
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     *
     * @return string
     */
    public function type(): string
    {
        return self::$TYPES[ $this->type ];
    }

    /**
     * Is this an unset port?
     *
     * @return boolean
     */
    public function typeUnset():bool
    {
        return $this->type === self::TYPE_UNSET;
    }

    /**
     * Is this a peering port?
     *
     * @return boolean
     */
    public function typePeering(): bool
    {
        return $this->type === self::TYPE_PEERING;
    }

    /**
     * Is this a reseller port?
     *
     * @return boolean
     */
    public function typeReseller(): bool
    {
        return $this->type === self::TYPE_RESELLER;
    }

    /**
     * Is this a core port?
     *
     * @return boolean
     */
    public function typeCore(): bool
    {
        return $this->type === self::TYPE_CORE;
    }

    /**
     * Is this a fanout port?
     *
     * @return boolean
     */
    public function typeFanout(): bool
    {
        return $this->type === self::TYPE_FANOUT;
    }

    /**
     * Get the appropriate OID for in octets
     *
     * @return string
     */
    public function oidInOctets(): string
    {
        return Iface::OID_IF_HC_IN_OCTETS;
    }

    /**
     * Get the appropriate OID for out octets
     *
     * @return string
     */
    public function oidOutOctets(): string
    {
        return Iface::OID_IF_HC_OUT_OCTETS;
    }

    /**
     * Get the appropriate OID for in unicast packets
     *
     * @return string
     */
    public function oidInUnicastPackets(): string
    {
        return Iface::OID_IF_HC_IN_UNICAST_PACKETS;
    }

    /**
     * Get the appropriate OID for out unicast packets
     * @return string
     */
    public function oidOutUnicastPackets(): string
    {
        return Iface::OID_IF_HC_OUT_UNICAST_PACKETS;
    }

    /**
     * Get the appropriate OID for in errors
     * @return string
     */
    public function oidInErrors(): string
    {
        return Iface::OID_IF_IN_ERRORS;
    }

    /**
     * Get the appropriate OID for out errors
     * @return string
     */
    public function oidOutErrors(): string
    {
        return Iface::OID_IF_OUT_ERRORS;
    }

    /**
     * Get the appropriate OID for in discards
     * @return string
     */
    public function oidInDiscards(): string
    {
        return Iface::OID_IF_IN_DISCARDS;
    }

    /**
     * Get the appropriate OID for out discards
     *
     * @return string
     */
    public function oidOutDiscards(): string
    {
        switch( $this->switcher->os ) {
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
     *
     * @return string
     */
    public function oidInBroadcasts(): string
    {
        return Iface::OID_IF_HC_IN_BROADCAST;
    }

    /**
     * Get the appropriate OID for out broadcasts
     *
     * @return string
     */
    public function oidOutBroadcasts(): string
    {
        return Iface::OID_IF_HC_OUT_BROADCAST;
    }

    public function ifnameToSNMPIdentifier()
    {
        # escape special characters in ifName as per
        # http://oss.oetiker.ch/mrtg/doc/mrtg-reference.en.html - "Interface by Name" section

        $ifname = preg_replace( '/:/', '\\:', $this->ifName );
        $ifname = preg_replace( '/&/', '\\&', $ifname );
        $ifname = preg_replace( '/@/', '\\@', $ifname );
        $ifname = preg_replace( '/\ /', '\\\ ', $ifname );

        return $ifname;
    }

    /**
     * Update switch port details from a SNMP poll of the device.
     *
     * Pass an instance of OSS_Logger if you want logging enabled.
     *
     * @link https://github.com/opensolutions/OSS_SNMP
     *
     *
     * @param SNMP $host An instance of the SNMP host object
     * @param bool $logger An instance of the logger or false
     *
     * @return SwitchPort For fluent interfaces
     *
     * @throws
     */
    public function snmpUpdate( SNMP $host, bool $logger = false, bool $nosave = false ): SwitchPort
    {
        foreach( self::$SNMP_MAP as $snmp => $attribute ) {
            $fn = $attribute;

            switch( $snmp ) {
                case 'lastChanges':
                    $n = $host->useIface()->$snmp( true )[ $this->ifIndex ];

                    // need to allow for small changes due to rounding errors
                    if( $logger !== false && $this->$fn !== $n && abs( $this->$fn - $n ) > 60 ) {
                        Log::info( "[{$this->switcher->name}]:{$this->name} [Index: {$this->ifIndex}] Updating {$attribute} from [{$this->$fn}] to [{$n}]" );
                    }
                    break;
                default:
                    $n = null;
                    if( !empty($host->useIface()->$snmp()[ $this->ifIndex ]) ) {
                        $n = $host->useIface()->$snmp()[ $this->ifIndex ];
                    }

                    if( $logger !== false && $this->$fn !== $n ) {
                        Log::info( "[{$this->switcher->name}]:{$this->name} [Index: {$this->ifIndex}] Updating {$attribute} from [{$this->$fn}] to [{$n}]" );
                    }
                    break;
            }

            $this->$fn = $n;
        }

        if( $this->switcher->mauSupported ) {
            foreach( self::$OSS_SNMP_MAU_MAP as $snmp => $attribute ) {
                $fn = $attribute['fn'];

                try {
                    if( isset( $attribute['xlate'] ) ) {
                        $n = $host->useMAU()->$snmp( $attribute['xlate'] );
                        $n = isset( $n[ $this->ifIndex ] ) ? $n[ $this->ifIndex ] : null;
                    } else {
                        $n = $host->useMAU()->$snmp();
                        $n = isset( $n[ $this->ifIndex ] ) ? $n[ $this->ifIndex ] : null;
                    }
                } catch( \Exception $e ) {
                    // looks like the switch supports MAU but not all of the MIBs
                    if( $logger !== false ) {
                        Log::debug( "[{$this->switcher->name}]:{$this->name} [Index: {$this->ifIndex}] MAU MIB for {$fn} not supported" );
                    }
                    $n = null;
                }

                if( $snmp === 'types' ) {
                    if( isset( MauMib::$TYPES[ $n ] ) ) {
                        $n = MauMib::$TYPES[ $n ];
                    } else if( $n === null || $n === '.0.0' ) {
                        $n = '(empty)';
                    } else {
                        $n = '(unknown type - oid: ' . $n . ')';
                    }
                }

                if( $this->$fn !== $n && $logger !== false ) {
                    Log::info( "[{$this->switcher->name}]:{$this->name} [Index: {$this->ifIndex}] Updating {$attribute['fn']} from [{$this->$fn}] to [{$n}]" );
                }

                $this->$fn = $n;
            }
        }

        try {
            // not all switches support this
            // FIXME is there a vendor agnostic way of doing this?

            // are we a LAG port?
            $isAggregatePorts = $host->useLAG()->isAggregatePorts();

            if( isset( $isAggregatePorts[ $this->ifIndex ] ) && $isAggregatePorts[ $this->ifIndex ] ){
                $this->lagIfIndex = $host->useLAG()->portAttachedIds()[ $this->ifIndex ];
            } else {
                $this->lagIfIndex = null;
            }

        } catch( \Exception $e ){}

        $this->lastSnmpPoll = now();

        if( !$nosave ) {
            $this->save();
        }

        return $this;
    }
}