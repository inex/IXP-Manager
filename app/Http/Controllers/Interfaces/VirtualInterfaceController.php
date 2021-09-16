<?php

namespace IXP\Http\Controllers\Interfaces;

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

use DB;
use Exception;
use Former;

use Illuminate\View\View;

use IXP\Exceptions\GeneralException;
use JsonException;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\{Aggregators\VirtualInterfaceAggregator,
    Customer,
    PhysicalInterface,
    Switcher,
    SwitchPort,
    VirtualInterface,
    Vlan,
    VlanInterface};

use IXP\Http\Requests\{
    StoreVirtualInterface,
    StoreVirtualInterfaceWizard
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};
use Throwable;

/**
 * VirtualInterface Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Interfaces
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterfaceController extends Common
{
    /**
     * Display all the virtualInterfaces
     *
     * @return  View
     */
    public function list() : View
    {
        return view( 'interfaces/virtual/list' )->with([
            'resellerMode'      => $this->resellerMode(),
            'vis'               => VirtualInterface::selectRaw(
                'vi.id AS id, 
                            SUM( pi.speed ) AS speed,
                            SUM( pi.rate_limit ) AS rate_limit,
                            COUNT( pi.id ) AS nbpi,
                            c.id AS custid, c.name AS custname,
                            l.id as locationid, l.name AS locationname,
                            s.id AS switchid, s.name AS switchname,
                            GROUP_CONCAT( sp.name ) AS switchport,
                            GROUP_CONCAT( sp.type ) AS switchporttype,
                            GROUP_CONCAT( ppi.id ) AS peering,
                            GROUP_CONCAT( fpi.id ) AS fanout'
                        )->from( 'virtualinterface AS vi' )
                        ->leftJoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
                        ->leftJoin( 'physicalinterface AS ppi', 'ppi.fanout_physical_interface_id', 'pi.id' )
                        ->leftJoin( 'physicalinterface AS fpi', 'fpi.id', 'pi.fanout_physical_interface_id' )
                        ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
                        ->leftJoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
                        ->leftJoin( 'switch AS s', 's.id', 'sp.switchid' )
                        ->leftJoin( 'cabinet AS cab', 'cab.id', 's.cabinetid' )
                        ->leftJoin( 'location AS l', 'l.id', 'cab.locationid' )
                        ->groupBy( 'id' )
                        ->get()->toArray(),
        ]);
    }

    /**
     * Display the form to create a virtual interface
     *
     * @param  Customer  $cust  customer
     *
     * @return  View
     *
     * @throws
     */
    public function createWizardForCust( Customer $cust ) : View
    {
        return $this->wizard( $cust );
    }

    /**
     * Display the form to add a virtual interface with a selected customer
     *
     * @param Customer $cust the customer to preselect
     *
     * @return  View
     */
    public function createForCust( Customer $cust ) : View
    {
        return $this->create( request(), $cust );
    }

    /**
     * Display the form to create a virtual interface
     *
     * @param Request                   $r
     * @param Customer|null             $cust the customer to preselect
     *
     * @return View
     */
    public function create( Request $r, Customer $cust = null ): View
    {
        if( $cust ) {
            Former::populate( [
                'custid' => $r->old( 'cust', $cust->id ),
            ] );
        }

        return view( 'interfaces/virtual/add' )->with([
            'custs'             => Customer::groupBy( 'name' )->get(),
            'vlans'             => [],
            'vi'                => false,
            'cb'                => false,
            'selectedCust'      => $cust ?: false
        ]);
    }

    /**
     * Create a virtual interface
     *
     * @param   StoreVirtualInterface $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     */
    public function store( StoreVirtualInterface $r ): RedirectResponse
    {
        // we don't allow setting channel group or name until there's >= 1 physical interface / LAG framing:
        $r->merge( [ 'name' => '' , 'channelgroup' => null ] );
        $vi = VirtualInterface::make( $r->all() );

        $this->setBundleDetails( $vi );

        $vi->save();

        AlertContainer::push( 'Virtual interface created.', Alert::SUCCESS );
        return redirect( route( 'virtual-interface@edit', [ 'vi' => $vi->id ] ) );

    }

    /**
     * Display the form to edit a virtual interface
     *
     * @param Request           $r
     * @param VirtualInterface  $vi     the virtual interface
     *
     * @return View
     */
    public function edit( Request $r,  VirtualInterface $vi ): View
    {
        $name = $r->old( 'name', $vi->name );

        // Check if the last character of the Name is a white space,
        // if its the case we add Double quotes to keep the space at the end
        if( substr( $name, -1 ) === ' ' ) {
            $name = '"'. $name . '"';
        }

        // fill the form with Virtual interface data
        Former::populate([
            'custid'                => $r->old( 'custid',            $vi->custid        ),
            'trunk'                 => $r->old( 'trunk',             $vi->trunk         ),
            'lag_framing'           => $r->old( 'lag_framing',       $vi->lag_framing   ),
            'fastlacp'              => $r->old( 'fastlacp',          $vi->fastlacp      ),
            'description'           => $r->old( 'description',       $vi->description   ),
            'channelgroup'          => $r->old( 'channel-group',     $vi->channelgroup  ),
            'mtu'                   => $r->old( 'mtu',               $vi->mtu           ),
            'name'                  => $name,
        ]);

        return view( 'interfaces/virtual/add' )->with([
            'custs'             => Customer::groupBy( 'name' )->get(),
            'vlans'             => Vlan::orderBy( 'number' )->get(),
            'vi'                => $vi,
            'cb'                => $vi->getCoreBundle(),
            'selectedCust'      => false
        ]);
    }

    /**
     * Add or edit a virtual interface (set all the data needed)
     *
     * @param  StoreVirtualInterface  $r  instance of the current HTTP request
     * @param  VirtualInterface  $vi
     *
     * @return  RedirectResponse
     *
     * @throws GeneralException|Throwable
     */
    public function update( StoreVirtualInterface $r, VirtualInterface $vi ): RedirectResponse
    {
        $r->merge( [ 'name' => trim( $r->name , '"') ] );

        // we don't allow setting channel group or name until there's >= 1 physical interface / LAG framing:
        if( $vi->physicalInterfaces()->count() === 0 ) {
            $r->merge( [ 'name' => '' , 'channelgroup' => null ] );
        }

        DB::beginTransaction();
        $vi->fill( $r->all() );
        $this->setBundleDetails( $vi );
        $vi->save();

        if( $vi->physicalInterfaces()->count() > 0 ) {
            // We need to try and make naming of the virtual interface name automatic as well as choice
            // of the channel group number.

            // let's take group number first -> needs to be unique within a switch and > 0
            // (some devices may allow zero but programmatically it may be easier to avoid this due to legacy data)
            // if it's a number gt zero and it's changed (if we're editing)

            // ensure it's unique:
            if( !$r->lag_framing && $r->channelgroup === null && $vi->physicalInterfaces()->count() === 1 ) {
                // no op -> this allows a user to set a null channel group number on an interface with one PI and no lag framing.
            } else if( !VirtualInterfaceAggregator::validateChannelGroup( $vi ) ) {
                DB::rollback();
                AlertContainer::push( 'Channel group number is not unique within the switch.', Alert::DANGER );
                return redirect( route( 'virtual-interface@edit', [ 'vi' => $vi->id ] ) )->withInput()->exceptInput( 'channelgroup' );
            }
        }
        DB::commit();
        AlertContainer::push( 'Virtual Interface updated.', Alert::SUCCESS );
        return redirect( route( 'virtual-interface@edit', [ 'vi' => $vi->id ] ) );
    }

    /**
     * Display the wizard form to add a virtual interface
     *
     * @param  Customer|null  $cust  Id of the customer to preselect
     *
     * @return View
     *
     * @throws
     */
    public function wizard( Customer $cust = null ): View
    {
        if( $cust ) {
            Former::populate( [
                'custid' => $cust->id,
            ] );
        }

        return view( 'interfaces/virtual/wizard' )->with([
            'custs'                 => Customer::groupBy( 'name' )->get(),
            'vli'                   => false,
            'vlans'                 => Vlan::orderBy( 'number' )->get(),
            'pi_switches'           => Switcher::where( 'active', true )
                ->orderBy( 'name' )->get(),
            'resoldCusts'           => $this->resellerMode() ? json_encode( Customer::join('cust AS reseller', 'reseller.reseller', 'cust.id')
                ->orderBy('reseller.name')->get(), JSON_THROW_ON_ERROR) : json_encode([], JSON_THROW_ON_ERROR),
            'selectedCust'          => $cust ?: false
        ]);
    }

    /**
     * Create an interface wizard
     *
     * @param   StoreVirtualInterfaceWizard $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     */
    public function storeWizard( StoreVirtualInterfaceWizard $r ): RedirectResponse
    {
        $v  = Vlan::find( $r->vlanid );
        $vi = VirtualInterface::create( $r->all() );

        PhysicalInterface::create( array_merge( $r->all(), [
            'virtualinterfaceid' => $vi->id,
        ] ) );

        SwitchPort::find( $r->switchportid )->update( [ 'type' => SwitchPort::TYPE_PEERING ] );

        $vli = VlanInterface::make( array_merge( $r->all(),
            [
                'virtualinterfaceid' => $vi->id,
                'busyhost'           => false
            ]
        ) );

        if( !$this->setIp( $r, $v, $vli, false ) || !$this->setIp( $r, $v, $vli, true ) ) {
            return redirect(route( 'virtual-interface@wizard' ) )->withInput( $r->all() );
        }

        $vli->save();

        // add a warning if we're filtering on irrdb but have not configured one for the customer
        $this->warnIfIrrdbFilteringButNoIrrdbSourceSet( $vli );

        AlertContainer::push( "Virtual interface created.", Alert::SUCCESS );
        return redirect( route( 'virtual-interface@edit', [ 'vi' => $vi->id ] ) );
    }

    /**
     * Delete a Virtual Interface
     *
     * @param  Request  $r  instance of the current HTTP request
     * @param  VirtualInterface  $vi
     *
     * @return  RedirectResponse
     *
     * @throws Exception
     */
    public function delete( Request $r, VirtualInterface $vi ): RedirectResponse
    {
        if( $vi->getCoreBundle() ) {
            AlertContainer::push( 'The Virtual Interface is linked to a Core Bundle. Delete the Core Bundle first to be able to delete the Virtual Interface.', Alert::DANGER );
            return redirect( route( 'virtual-interface@edit' , [ 'vi' => $vi->id ] ) );
        }

        foreach( $vi->physicalInterfaces as $pi) {
            $this->deletePi( $r, $pi, false );
        }

        foreach( $vi->vlanInterfaces as $vli ) {
            $vli->layer2addresses()->delete();
            $vli->delete();
        }

        $vi->macAddresses()->delete();
        $vi->delete();

        AlertContainer::push( 'Virtual interface deleted.', Alert::SUCCESS );

        if( $r->user ) {
            return redirect( route( "customer@overview", [ 'cust' => $r->user, "tab" => "ports" ] ) );
        }
        return redirect( route( "virtual-interface@list" ) );
    }
}