<?php

namespace IXP\Http\Controllers\Api\V4\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Auth, Log;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use IXP\Http\Controllers\Interfaces\Common;
use IXP\Http\Requests\Api\V4\Provisioning\StoreConnection;

use IXP\Models\{
    Customer,
    PhysicalInterface,
    SwitchPort,
    VirtualInterface,
    Vlan,
    VlanInterface
};

use IXP\Support\Provisioning\IpAllocationException;
use IXP\Support\Provisioning\IpAllocator;

/**
 * Provisioning API - Connection Controller
 *
 * Creates and removes a member's connection to the exchange: the virtual interface, the
 * physical interface on a switch port, the VLAN interface, and the addresses on it.
 *
 * Extends Interfaces\Common rather than Api\V4\Controller, which is the point of the class:
 * setIp() and deletePi() are inherited, so the address assignment and the fanout-aware
 * teardown exist once in the codebase and keep working when upstream changes them. What is
 * reimplemented here is only the orchestration that VirtualInterfaceController::storeWizard()
 * wraps in redirects and flash messages.
 *
 * @see \IXP\Http\Controllers\Interfaces\VirtualInterfaceController::storeWizard()
 *
 * @author     KleyReX
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConnectionController extends Common
{
    /**
     * Create a connection for a member.
     *
     * Everything happens in one transaction, including the address allocation: the row lock
     * IpAllocator takes is only meaningful until commit, so two orders provisioned at the same
     * moment cannot be handed the same address.
     *
     * Differences from the web wizard, all deliberate:
     *
     *   - `ipv4address`/`ipv6address` may be "auto".
     *   - Addresses must already exist in the VLAN's pool. setIp() would happily create one on
     *     the fly; for an unattended caller that turns a typo into a stray address record.
     *     IpAllocator::claim() is therefore consulted first and rejects anything not in the
     *     pool, so by the time setIp() runs the address is known to exist.
     *   - LAG bookkeeping (setBundleDetails, and hence assignChannelGroup) is applied. The
     *     wizard omits it, which is harmless there because it creates single-port interfaces
     *     from a form that cannot request LAG framing.
     *
     * @param  StoreConnection  $r
     * @param  Customer         $cust
     *
     * @return JsonResponse
     */
    public function store( StoreConnection $r, Customer $cust ): JsonResponse
    {
        $vlan = Vlan::findOrFail( $r->input( 'vlanid' ) );

        // Reject a switch port which is taken, is not a member port, or sits on another
        // switch, before anything is written:
        if( $error = $this->rejectUnusableSwitchPort( $r ) ) {
            return response()->json( [ 'message' => $error ], 422 );
        }

        try {
            $vi = DB::transaction( function() use ( $r, $cust, $vlan ) {
                $vi = VirtualInterface::create( array_merge( $r->validated(), [ 'custid' => $cust->id ] ) );

                PhysicalInterface::create( array_merge( $r->validated(), [
                    'virtualinterfaceid' => $vi->id,
                ] ) );

                SwitchPort::findOrFail( $r->input( 'switchportid' ) )
                    ->update( [ 'type' => SwitchPort::TYPE_PEERING ] );

                // resolve "auto" and verify explicit addresses against the pool, then hand the
                // resolved values to the inherited setIp():
                $resolved = $r->all();

                foreach( [ false, true ] as $ipv6 ) {
                    $field = ( $ipv6 ? 'ipv6' : 'ipv4' ) . 'address';

                    $ip = ( new IpAllocator() )->claim( $vlan, $r->input( $field ), $ipv6 );

                    $resolved[ $field ] = $ip?->address;
                }

                $ipRequest = Request::create( '/', 'POST', $resolved );

                $vli = new VlanInterface( array_merge( $r->validated(), [
                    'virtualinterfaceid' => $vi->id,
                    'busyhost'           => $r->boolean( 'busyhost' ),
                ] ) );

                if( !$this->setIp( $ipRequest, $vlan, $vli, false ) || !$this->setIp( $ipRequest, $vlan, $vli, true ) ) {
                    // setIp() only fails when an address is in use on this VLAN; claim() has
                    // already ruled that out, so reaching here means someone took it in
                    // between. Rolling back is the right answer either way.
                    throw new IpAllocationException(
                        'An address became unavailable while the connection was being created. Please retry.'
                    );
                }

                $vli->save();

                $vi->refresh();
                $this->setBundleDetails( $vi );
                $vi->save();

                return $vi;
            } );
        } catch( IpAllocationException $e ) {
            return response()->json( [ 'message' => $e->getMessage() ], 422 );
        }

        $this->warnIfIrrdbFilteringButNoIrrdbSourceSet( $vi->vlanInterfaces()->first() );

        Log::notice( "Provisioning API: created connection {$vi->id} for {$cust->name} as "
            . Auth::user()->username );

        return response()->json( [ 'connection' => $this->connectionAsArray( $vi->fresh() ) ], 201 );
    }

    /**
     * List a member's connections.
     *
     * @param  Customer  $cust
     *
     * @return JsonResponse
     */
    public function index( Customer $cust ): JsonResponse
    {
        $connections = $cust->virtualInterfaces->map(
            fn( VirtualInterface $vi ) => $this->connectionAsArray( $vi )
        )->values()->all();

        return response()->json( [ 'connections' => $connections ] );
    }

    /**
     * Remove a connection.
     *
     * Uses the inherited deletePi(), which handles the fanout and reseller cases. That method
     * is not idempotent - it dereferences $pi->switchPort after having set switchportid to
     * null - so a physical interface already detached is skipped rather than passed to it.
     *
     * @param  VirtualInterface  $vi
     *
     * @return JsonResponse
     */
    public function destroy( VirtualInterface $vi ): JsonResponse
    {
        if( $vi->getCoreBundle() ) {
            return response()->json( [
                'message' => 'This connection belongs to a core bundle. Delete the core bundle first.',
            ], 409 );
        }

        $custName = $vi->customer?->name ?? 'unknown';
        $request  = Request::create( '/', 'POST' );

        DB::transaction( function() use ( $vi, $request ) {
            foreach( $vi->physicalInterfaces as $pi ) {
                if( $pi->switchportid === null ) {
                    // already detached: deletePi() would fail on a null switch port
                    $pi->delete();
                    continue;
                }

                $this->deletePi( $request, $pi, false );
            }

            foreach( $vi->vlanInterfaces as $vli ) {
                $vli->layer2addresses()->delete();
                $vli->delete();
            }

            $vi->macAddresses()->delete();
            $vi->delete();
        } );

        Log::notice( "Provisioning API: deleted connection {$vi->id} for {$custName} as "
            . Auth::user()->username );

        return response()->json( [ 'deleted' => true ] );
    }

    /**
     * Reject a switch port which cannot be given to a member.
     *
     * @param  StoreConnection  $r
     *
     * @return string|null  an error message, or null when the port is usable
     */
    private function rejectUnusableSwitchPort( StoreConnection $r ): ?string
    {
        $sp = SwitchPort::find( $r->input( 'switchportid' ) );

        if( !$sp ) {
            return 'The switch port does not exist.';
        }

        if( (int)$sp->switchid !== (int)$r->input( 'switch' ) ) {
            return "Switch port {$sp->name} does not belong to the switch given.";
        }

        if( $sp->physicalInterface ) {
            return "Switch port {$sp->name} is already assigned to another connection.";
        }

        if( !in_array( (int)$sp->type, [ SwitchPort::TYPE_UNSET, SwitchPort::TYPE_PEERING ], true ) ) {
            return "Switch port {$sp->name} is not a member port (type {$sp->type}).";
        }

        return null;
    }

    /**
     * Representation of a connection for API responses.
     *
     * @param  VirtualInterface  $vi
     *
     * @return array
     */
    private function connectionAsArray( VirtualInterface $vi ): array
    {
        return [
            'id'                    => $vi->id,
            'custid'                => $vi->custid,
            'name'                  => $vi->name,
            'trunk'                 => (bool)$vi->trunk,
            'lag_framing'           => (bool)$vi->lag_framing,
            'channelgroup'          => $vi->channelgroup,
            'physical_interfaces'   => $vi->physicalInterfaces->map( fn( PhysicalInterface $pi ) => [
                'id'            => $pi->id,
                'status'        => $pi->status,
                'speed'         => $pi->speed,
                'duplex'        => $pi->duplex,
                'rate_limit'    => $pi->rate_limit,
                'autoneg'       => (bool)$pi->autoneg,
                'switchport'    => $pi->switchportid,
                'switchport_name' => $pi->switchPort?->name,
                'switch'        => $pi->switchPort?->switchid,
                'switch_name'   => $pi->switchPort?->switcher?->name,
            ] )->values()->all(),
            'vlan_interfaces'       => $vi->vlanInterfaces->map( fn( VlanInterface $vli ) => [
                'id'            => $vli->id,
                'vlan'          => $vli->vlanid,
                'ipv4enabled'   => (bool)$vli->ipv4enabled,
                'ipv4address'   => $vli->ipv4address?->address,
                'ipv4hostname'  => $vli->ipv4hostname,
                'ipv6enabled'   => (bool)$vli->ipv6enabled,
                'ipv6address'   => $vli->ipv6address?->address,
                'ipv6hostname'  => $vli->ipv6hostname,
                'rsclient'      => (bool)$vli->rsclient,
                'irrdbfilter'   => (bool)$vli->irrdbfilter,
                'as112client'   => (bool)$vli->as112client,
                'maxbgpprefix'  => [ 'ipv4' => $vli->ipv4maxbgpprefix, 'ipv6' => $vli->ipv6maxbgpprefix ],
            ] )->values()->all(),
        ];
    }
}
