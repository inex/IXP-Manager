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

use Carbon\Carbon;
use DateTime, Log;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
};

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
};

use Illuminate\Support\Collection;

use IXP\Exceptions\Switches\RebootDiscoveryNotSupported;

use IXP\Traits\Observable;

use OSS_SNMP\SNMP;

use \OSS_SNMP\MIBS\Iface as SNMPIface;

/**
 * IXP\Models\Switcher
 *
 * @property int $id
 * @property int|null $cabinetid
 * @property int|null $vendorid
 * @property string|null $name
 * @property string|null $ipv4addr
 * @property string|null $ipv6addr
 * @property string|null $snmppasswd
 * @property int|null $infrastructure
 * @property string|null $model
 * @property bool|null $active
 * @property string|null $notes
 * @property string|null $hostname
 * @property string|null $os
 * @property string|null $osDate
 * @property string|null $osVersion
 * @property string|null $serialNumber
 * @property string|null $lastPolled
 * @property int|null $mauSupported
 * @property int|null $asn
 * @property string|null $loopback_ip
 * @property string|null $loopback_name
 * @property string|null $mgmt_mac_address
 * @property int|null $snmp_engine_time
 * @property int|null $snmp_system_uptime
 * @property int|null $snmp_engine_boots
 * @property int $poll
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServerConnection[] $consoleServerConnections
 * @property-read int|null $console_server_connections_count
 * @property-read \IXP\Models\Infrastructure|null $infrastructureModel
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\SwitchPort[] $switchPorts
 * @property-read int|null $switch_ports_count
 * @property-read \IXP\Models\Vendor|null $vendor
 * @method static Builder|Switcher newModelQuery()
 * @method static Builder|Switcher newQuery()
 * @method static Builder|Switcher query()
 * @method static Builder|Switcher whereActive($value)
 * @method static Builder|Switcher whereAsn($value)
 * @method static Builder|Switcher whereCabinetid($value)
 * @method static Builder|Switcher whereCreatedAt($value)
 * @method static Builder|Switcher whereHostname($value)
 * @method static Builder|Switcher whereId($value)
 * @method static Builder|Switcher whereInfrastructure($value)
 * @method static Builder|Switcher whereIpv4addr($value)
 * @method static Builder|Switcher whereIpv6addr($value)
 * @method static Builder|Switcher whereLastPolled($value)
 * @method static Builder|Switcher whereLoopbackIp($value)
 * @method static Builder|Switcher whereLoopbackName($value)
 * @method static Builder|Switcher whereMauSupported($value)
 * @method static Builder|Switcher whereMgmtMacAddress($value)
 * @method static Builder|Switcher whereModel($value)
 * @method static Builder|Switcher whereName($value)
 * @method static Builder|Switcher whereNotes($value)
 * @method static Builder|Switcher whereOs($value)
 * @method static Builder|Switcher whereOsDate($value)
 * @method static Builder|Switcher whereOsVersion($value)
 * @method static Builder|Switcher wherePoll($value)
 * @method static Builder|Switcher whereSerialNumber($value)
 * @method static Builder|Switcher whereSnmpEngineBoots($value)
 * @method static Builder|Switcher whereSnmpEngineTime($value)
 * @method static Builder|Switcher whereSnmpSystemUptime($value)
 * @method static Builder|Switcher whereSnmppasswd($value)
 * @method static Builder|Switcher whereUpdatedAt($value)
 * @method static Builder|Switcher whereVendorid($value)
 * @mixin \Eloquent
 */
class Switcher extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'switch';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cabinetid',
        'vendorid',
        'name',
        'ipv4addr',
        'ipv6addr',
        'snmppasswd',
        'infrastructure',
        'model',
        'active',
        'poll',
        'notes',
        'hostname',
        'os',
        'osDate',
        'osVersion',
        'serialNumber',
        'lastPolled',
        'mauSupported',
        'asn',
        'loopback_ip',
        'loopback_name',
        'mgmt_mac_address',
        'snmp_engine_time',
        'snmp_system_uptime',
        'snmp_engine_boots',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active'         => 'boolean',
    ];

    /**
     * Constants for the list mode dropdown in the swtiches list
     */
    public const VIEW_MODE_DEFAULT     = 'view_mode_default';
    public const VIEW_MODE_OS          = 'view_mode_os';
    public const VIEW_MODE_L3          = 'view_mode_l3';

    /**
     * @var array Textual representations of the list mode
     */
    public static $VIEW_MODES = [
        self::VIEW_MODE_DEFAULT     => 'Default',
        self::VIEW_MODE_OS          => 'OS View',
        self::VIEW_MODE_L3          => 'L3 View',
    ];

    /**
     * Elements for SNMP polling via the OSS_SNMP library
     *
     * These are used to build function names
     *
     * @see snmpPoll() below
     * @var array Elements for SNMP polling via the OSS_SNMP library
     */
    public static $SNMP_SWITCH_ELEMENTS = [
        'Model'         => [ 'fn' => 'model' ],
        'Os'            => [ 'fn' => 'os' ],
        'OsDate'        => [ 'fn' => 'osDate' ],
        'OsVersion'     => [ 'fn' => 'osVersion' ],
        'SerialNumber'  => [ 'fn' => 'serialNumber' ],
    ];

    /**
     * Get the infrastructure that own the switcher
     */
    public function infrastructureModel(): BelongsTo
    {
        return $this->belongsTo(Infrastructure::class, 'infrastructure' );
    }

    /**
     * Get the cabinet that own the switcher
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinetid' );
    }

    /**
     * Get the vendor that own the switcher
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendorid' );
    }

    /**
     * Get the switch ports for the switcher
     */
    public function switchPorts(): HasMany
    {
        return $this->hasMany(SwitchPort::class, 'switchid');
    }

    /**
     * Get the console server connections for the switcher
     */
    public function consoleServerConnections(): HasMany
    {
        return $this->hasMany(ConsoleServerConnection::class, 'switchid');
    }

    /**
     * Gets a listing of patch panel ports for a switch
     *
     * @return Collection
     */
    public function getPatchPanelPorts(): Collection
    {
        return self::select( 'ppp.*' )
            ->leftJoin( 'switchport AS sp', 'sp.switchid', 'switch.id' )
            ->leftJoin( 'patch_panel_port AS ppp', 'ppp.switch_port_id', 'sp.id' )
            ->where( 'switch.id', $this->id )
            ->where( 'ppp.id', '!=', NULL )
            ->groupBy( 'ppp.id' )
            ->get();
    }

    /**
     * Gets a listing of physical interfaces for a switch
     *
     * @return Collection
     */
    public function getPhysicalInterfaces(): Collection
    {
        return self::select( 'pi.*' )
            ->leftJoin( 'switchport AS sp', 'sp.switchid', 'switch.id' )
            ->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
            ->where( 'switch.id', $this->id )
            ->where( 'pi.id', '!=', NULL )
            ->get();
    }

    /**
     * Update switch's details using SNMP polling
     *
     * @see self::$SNMP_SWITCH_ELEMENTS
     *
     * @param SNMP  $host       An instance of \OSS_SNMP\SNMP for this switch
     * @param bool  $logger     An instance of the logger or false
     *
     * @return Switcher For fluent interfaces
     */
    public function snmpPoll( $host, bool $logger = false, bool $nosave = false ): Switcher
    {
        // utility to format dates
        $formatDate = function( $d ) {
            return $d instanceof DateTime ? $d->format( 'Y-m-d H:i:s' ) : 'Unknown';
        };

        foreach( self::$SNMP_SWITCH_ELEMENTS as $index => $p ) {
            $objfn = $p[ 'fn' ];
            $fn = "get{$index}";
            $n = $host->getPlatform()->$fn();

            if( $logger ) {
                switch( $index ) {
                    case 'OsDate':
                        if( $this->$objfn != $formatDate( $n ) )
                            Log::info( " [{$this->name}] Platform: Updating {$index} from " . $this->$objfn . " to " . $formatDate( $n ) );
                        else
                            Log::info( " [{$this->name}] Platform: Found {$index}: " . $formatDate( $n ) );
                        break;

                    default:
                        if( $logger && $this->$objfn != $n ){
                            Log::info( " [{$this->name}] Platform: Updating {$index} from {$this->$objfn} to {$n}" );
                        } else{
                            Log::info( " [{$this->name}] Platform: Found {$index}: {$n}" );
                        }
                        break;
                }
            }

            $this->$objfn = $n;
        }

        // does this switch support the IANA MAU MIB?
        try {
            $host->useMAU()->types();
            $this->mauSupported = true;
        } catch( \OSS_SNMP\Exception $e ) {
            $this->mauSupported = false;
        }

        // uptime data
        try {
            $this->snmp_system_uptime = $host->useSystem()->uptime();
        } catch( \OSS_SNMP\Exception $e ) {
            //
        }

        try {
            $this->snmp_engine_time = $host->useSNMP_Engine()->time();
            $this->snmp_engine_boots = $host->useSNMP_Engine()->boots();
        } catch( \OSS_SNMP\Exception $e ) {
            //
        }

        $this->lastPolled = now();

        if( !$nosave ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Update a switches ports using SNMP polling
     *
     * There is an optional ``$results`` array which can be passed by reference. If
     * so, it will be indexed by the SNMP port index (or a decreasing negative index
     * beginning -1 if the port only exists in the database). The contents of this
     * associative array is:
     *
     *     "port"   => \Models\SwitchPort object
     *     "bullet" =>
     *         - false for existing ports
     *         - "new" for newly found ports
     *         - "db" for ports that exist in the database only
     *
     * **Note:** It is assumed that the Doctrine2 Entity Manager is available in the
     * Zend registry as ``d2em`` in this function.
     *
     * @param SNMP $host An instance of \OSS_SNMP\SNMP for this switch
     * @param bool $logger An instance of the logger or false
     * @param bool $result
     * @param bool $nosave Do we need to save the object in DB ?
     *
     * @return Switcher For fluent interfaces
     *
     * @throws
     */
    public function snmpPollSwitchPorts( $host, $logger = false, bool|array &$result = false, bool $nosave = false ): Switcher
    {
        // clone the ports currently known to this switch as we'll be playing with this array
        $existingPorts = clone $this->switchPorts;

        // iterate over all the ports discovered on the switch:
        foreach( $host->useIface()->indexes() as $index ) {
            // Port types - see https://docs.ixpmanager.org/usage/switches/#snmp-and-port-types-iftype
            if( !in_array( $host->useIface()->types()[ $index ], config('ixp.snmp.allowed_interface_types') ) ) {
                continue;
            }

            // find the matching switch port that may already be in the database (or create a new one)
            $sp = false;
            foreach( $existingPorts as $ix => $ep ) {
                if( $ep->ifIndex === $index ) {
                    $sp = SwitchPort::findOrFail( $ep->id );
                    if( is_array( $result ) ){
                        $result[ $index ] = [ "port" => $sp, 'bullet' => false ];
                    }

                    if( $logger ) {
                        Log::info( " - {$this->name} - found pre-existing port for ifIndex {$index}" );
                    }

                    // remove this from the array so later we'll know what ports exist only in the database
                    unset( $existingPorts[ $ix ] );
                    break;
                }
            }

            if( !$sp ) {
                // none existing port in database so we have found a new port
                // Do we need to save the object ?
                if( !$nosave ){
                    $sp = SwitchPort::create( [
                        'switchid'  => $this->id,
                        'ifIndex'   => $index,
                        'active'    => true,
                        'type'      => SwitchPort::TYPE_UNSET,
                    ]);
                }

                if( is_array( $result ) ) {
                    $result[ $index ] = [ "port" => $sp, 'bullet' => "new" ];
                }

                if( $logger ) {
                    Log::info( "Found new port for {$this->name} with index $index" );
                }
            }

            // update / set port details from SNMP
            $sp->snmpUpdate( $host, $logger, $nosave );
        }

        if( $existingPorts->count() ) {
            $i = -1;
            foreach( $existingPorts as $ep ) {
                if( is_array( $result ) ) {
                    $result[ $i-- ] = [ "port" => $ep, 'bullet' => "db" ];
                }
                if( $logger ) {
                    Log::warning( "{$this->name} - port found in database with no matching port on the switch:  [{$ep->id}] {$ep->name}" );
                }
            }
        }

        return $this;
    }

    /**
     * Return an array of core bundles
     * @return CoreBundle[]
     */
    public function getCoreBundles(): array
    {
        $cbs = $cbids = [];

        foreach( $this->switchPorts as $sp ) {
            if( $sp->physicalInterface && $sp->physicalInterface->coreinterface ) {
                if( $sp->physicalInterface->coreinterface->corelinksidea ) {
                    if( !in_array( $sp->physicalInterface->coreinterface->corelinksidea->corebundle->id, $cbids, true ) ) {
                        $sideA = $sp->physicalInterface->coreinterface->corelinksidea;
                        $cbids[] = $sideA->corebundle->id;
                        $cbs[]   = $sideA->corebundle;
                    }
                } elseif( $sp->physicalInterface->coreinterface->corelinksideb ) {
                    if( !in_array( $sp->physicalInterface->coreinterface->corelinksideb->corebundle->id, $cbids, true ) ) {
                        $sideB = $sp->physicalInterface->coreinterface->corelinksideb;
                        $cbids[] = $sideB->corebundle->id;
                        $cbs[]   = $sideB->corebundle;
                    }
                }
            }
        }

        return $cbs;
    }

    /**
     * Evaluate the switches status.
     *
     * Checks for recent reboots and missed snmp polling.
     *
     * @return array
     */
    public function status(): array
    {
        // assume we're okay
        $okay = true;
        $msgs = [];

        if( !$this->active ) {
            return [
                'name' => $this->name,
                'status' => true,
                'msgs' => [ 'Switch is inactive. Status tests skipped.' ],
            ];
        }

        // last polled:
        if( $this->lastPolled ) {
            $lastPolled = Carbon::parse( $this->lastPolled );
            if( $lastPolled->diffInMinutes() > 10 ) {
                $okay = false;
                $msgs[] = 'WARNING: last polled ' . $lastPolled->diffForHumans() . '.';
            } else {
                $msgs[] = 'Last polled ' . $lastPolled->diffForHumans() . '.';;
            }
        } else {
            $okay = false;
            $msgs[] = 'Switch has never been polled via SNMP.';
        }

        try {
            if( $this->recentlyRebooted() ) {
                $okay = false;
                $msgs[] = 'CRITICAL: rebooted within the last hour (probably).';
            }
        } catch( RebootDiscoveryNotSupported $e ) {
            $msgs[] = 'Switch does not support reboot checks.';
        }

        return [
            'name' => $this->name,
            'status' => $okay,
            'msgs' => $msgs,
        ];
    }

    /**
     * Indicate if the switch (probably) recently rebooted.
     *
     * If this returns true, switch has /most likely/ rebooted.
     *
     * @param int $window Window in minutes for 'recently'. Defaults to 60.
     *
     * @return bool
     *
     * @throws RebootDiscoveryNotSupported
     */
    public function recentlyRebooted( int $window = 60 ): bool
    {
        if( $this->snmp_engine_time === null && $this->snmp_system_uptime === null ) {
            throw new RebootDiscoveryNotSupported;
        }

        // convert window to seconds
        $window *= 60;

        // assume that the switch probably hasn't rebooted
        $probably = false;

        if( ( $this->snmp_system_uptime / 100 ) < $window ) {
            // Either sysuptime has rolled over or switch has rebooted.

            // try to identify rollover from snmp engine uptime.
            // we're ignore engine.boots here because it's not clear what causes that to increment.
            if( $this->snmp_engine_time !== null ) {
                if( $this->snmp_engine_time < $window ) {
                    $probably = true;
                }
            } else {
                $probably = true;
            }
        }

        if( $probably === true && Carbon::parse( $this->lastPolled )->diffInMinutes() < 60 ) {
            // one additional check is that interface last change must be less than the sysuptime for a reboot
            // to have taken place. We'll add a margin to the window here also.
            $cutoff = time() - $window - 60; // 60 for some margin

            foreach( $this->switchPorts as $sp ) {
                if( $sp->ifLastChange < $cutoff && $sp->ifOperStatus === SNMPIface::IF_ADMIN_STATUS_UP && $sp->physicalInterface && $sp->active ) {
                    $probably = false;
                    break;
                }
            }
        }

        return $probably;
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Switcher [id:%d] '%s'",
            $model->id,
            $model->name,
        );
    }
}