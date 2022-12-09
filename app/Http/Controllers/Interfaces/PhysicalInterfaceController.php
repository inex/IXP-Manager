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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Redirect, Former;

use Illuminate\Http\{
    RedirectResponse, Request
};

use Illuminate\View\View;

use IXP\Models\{
    Aggregators\SwitcherAggregator,
    CoreBundle,
    PhysicalInterface,
    Switcher,
    SwitchPort,
    VirtualInterface
};

use IXP\Http\Requests\{
    StorePhysicalInterface
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * PhysicalInterface Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Interfaces
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PhysicalInterfaceController extends Common
{
    /**
     * Display all the physical interfaces as a list
     *
     * @return  View
     */
    public function list(): View
    {
        return view( 'interfaces/physical/list' )->with([
            'pis'   => PhysicalInterface::selectRaw(
                'pi.id AS id, pi.speed AS speed, pi.duplex AS duplex, pi.status AS status,
                    pi.notes AS notes, pi.autoneg AS autoneg, pi.rate_limit as rate_limit,
                    c.name AS customer, c.id AS custid,
                    s.name AS switch, s.id AS switchid,
                    vi.id AS vintid,
                    sp.type as type, ppi.id as ppid, fpi.id as fpid,
                    sp.name AS port, l.id AS locid, l.name AS location'
            )->from( 'physicalinterface AS pi' )
            ->leftJoin( 'physicalinterface AS ppi', 'ppi.fanout_physical_interface_id', 'pi.id' )
            ->leftJoin( 'physicalinterface AS fpi', 'fpi.id', 'pi.fanout_physical_interface_id' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
            ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
            ->leftJoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftJoin( 'switch AS s', 's.id', 'sp.switchid' )
            ->leftJoin( 'cabinet AS cab', 'cab.id', 's.cabinetid' )
            ->leftJoin( 'location AS l', 'l.id', 'cab.locationid' )
            ->get()->toArray()
        ]);
    }

    /**
     * Display a physical interface
     *
     * @param PhysicalInterface $pi ID of the physical interface
     *
     * @return  View
     */
    public function view( PhysicalInterface $pi ): View
    {
        return view( 'interfaces/physical/view' )->with([
            'pi'    => $pi
        ]);
    }

    /**
     * Display the form to edit a physical interface
     *
     * @param VirtualInterface      $vi   ID of the virtual interface
     *
     * @return View|RedirectResponse
     */
    public function create( VirtualInterface $vi )
    {
        return view( 'interfaces/physical/edit' )->with([
            'switches'                    => Switcher::orderBy( 'name' )->get(),
            'switchports'                 => [],
            'pi'                          => false,
            'otherPICoreLink'             => false,
            'vi'                          => $vi,
            'cb'                          => false,
            'enableFanout'                => $this->resellerMode() && $vi && $vi->customer->reseller,
            'spFanout'                    => false,
        ]);
    }

    /**
     * Store a physical interface (set all the data needed)
     *
     * @param   StorePhysicalInterface $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( StorePhysicalInterface $r ): RedirectResponse
    {
        $vi = VirtualInterface::find( $r->virtualinterfaceid );

        // when presenting the add PI form, we include peering and unknown port types; set the selected port as peering:
        SwitchPort::find( $r->switchportid )
            ->update( [ 'type' => SwitchPort::TYPE_PEERING ] );

        $this->setBundleDetails( $vi );
        $vi->save();

        $pi = PhysicalInterface::create( $r->all() );

        if( !$this->processFanoutPhysicalInterface( $r, $pi, $vi) ){
            return Redirect::back()->withInput( $r->all() );
        }

        if( $related = $pi->relatedInterface() ) {
            $related->speed  = $r->speed;
            $related->status = $r->status;
            $related->duplex = $r->duplex;
            $related->save();
        }

        AlertContainer::push( 'Physical Interface created.', Alert::SUCCESS );
        return Redirect::to( $r->cb ? route( "core-bundle@edit", [ "cb" => $r->cb ] ) : route( "virtual-interface@edit", [ "vi" => $pi->virtualinterfaceid ] ) );
    }

    /**
     * Display the form to edit a physical interface from the core bundle
     *
     * @param  Request  $r
     * @param  PhysicalInterface  $pi  the physical interface
     * @param  CoreBundle  $cb  core bundle
     *
     * @return RedirectResponse|View
     */
    public function editFromCb( Request $r, PhysicalInterface $pi , CoreBundle $cb ): View|RedirectResponse
    {
        return $this->edit( $r, $pi , null, $cb );
    }

    /**
     * Display the form to edit a physical interface
     *
     * @param Request                   $r
     * @param PhysicalInterface         $pi     the physical interface
     * @param VirtualInterface|null     $vi     the virtual interface
     * @param CoreBundle|null           $cb     we come from the core bundle edit form
     *
     * @return View|RedirectResponse
     */
    public function edit( Request $r,  PhysicalInterface $pi, VirtualInterface $vi = null, CoreBundle $cb = null )
    {
        // we never edit a fanout port:
        if( $pi->switchPort->typeFanout() ) {
            AlertContainer::push( 'Do not edit fanout ports directly. Edit the peering interface and the fanout port will be updated to match.', Alert::DANGER );
            return redirect( route( 'virtual-interface@edit', [ 'vi' => $pi->virtualinterfaceid ] ) );
        }

        if( $vi && $pi->virtualinterfaceid !== $vi->id ) {
            AlertContainer::push( 'The physical interface does not belong to this virtual interface.', Alert::DANGER );
            return redirect( route( 'virtual-interface@edit', [ 'vi' => $pi->virtualinterfaceid ] ) );
        }

        // fill the form with physical interface data
        $data = [
            'switch'        => $r->old( 'switch',        $pi->switchPort->switchid  ),
            'switchportid'  => $r->old( 'switchportid',  $pi->switchportid          ),
            'status'        => $r->old( 'status',        $pi->status                ),
            'speed'         => $r->old( 'speed',         $pi->speed                 ),
            'duplex'        => $r->old( 'duplex',        $pi->duplex                ),
            'rate_limit'    => $r->old( 'rate_limit',    $pi->rate_limit            ),
            'autoneg'       => $r->old( 'autoneg',       $pi->autoneg               ),
            'notes'         => $r->old( 'notes',         $pi->notes                 ),
        ];

        // get all the switch ports available and add the switch port associated to the physical interface in the list
        $switchports = SwitcherAggregator::allPorts( $pi->switchPort->switchid, [], [], true, true ) +
                        [ $pi->switchportid =>
                              [ "name" => $pi->switchPort->name,
                                "id" => $pi->switchportid,
                                "type" => $pi->switchPort->type,
                                "porttype" => $pi->switchPort->type ]
                        ];

        ksort($switchports);

        // get the fanout details or other side of the core link details as/if applicable
        $data = $this->mergeFanoutDetails(      $pi, $pi->virtualInterface , $data );
        $data = $this->mergeCoreLinkDetails(    $pi, $data );

        Former::populate( $data );

        return view( 'interfaces/physical/edit' )->with([
            'switches'                    => Switcher::orderBy( 'name' )->get(),
            'switchports'                 => $switchports ?? [],
            'pi'                          => $pi,
            'otherPICoreLink'             => $pi->otherPICoreLink(),
            'vi'                          => $vi ?: false,
            'cb'                          => $cb ?: false,
            'enableFanout'                => $this->resellerMode() && $vi && $vi->customer->reseller,
            'spFanout'                    => isset( $data['fanout'] ) && $data['fanout'] && $pi->fanoutPhysicalInterface()->exists() ? $pi->fanoutPhysicalInterface->switchPort->id : false,
            'notesb'                      => array_key_exists( 'notes-b', $data ) ? $data['notes-b'] : ''
        ]);
    }

    /**
     * Update a physical interface (set all the data needed)
     *
     * @param   StorePhysicalInterface  $r  instance of the current HTTP request
     * @param   PhysicalInterface       $pi
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function update( StorePhysicalInterface $r, PhysicalInterface $pi ): RedirectResponse
    {
        $vi = VirtualInterface::find( $r->virtualinterfaceid );
        // when presenting the add PI form, we include peering and unknown port types; set the selected port as peering:
        $sp = SwitchPort::find( $r->switchportid );
        $sp->update( [ 'type' => SwitchPort::TYPE_PEERING ] );

        if( $pi->otherPICoreLink() ){
            // check if the user has changed the switch port
            if( $sp->id !== $pi->switchportid ){
                $oldSp = $pi->switchPort;
                $oldSp->update( [ 'type' =>SwitchPort::TYPE_UNSET ] );
            }

            // check if the user has selected the same switch port
            if( $pi->otherPICoreLink()->switchPort->switchid === $sp->switchid ){
                AlertContainer::push( 'The switch port selected for this physical interface is already used by the other physical interface of the core bundle. Please select another switch port', Alert::DANGER );
                return Redirect::back( )->withInput( $r->all() );
            }

            $sp->update( [ 'type' => SwitchPort::TYPE_CORE ] );
        }

        $this->setBundleDetails( $vi );
        $vi->save();

        $pi->update( $r->all() );

        if( !$this->processFanoutPhysicalInterface( $r, $pi, $vi) ){
            return Redirect::back( )->withInput( $r->all() );
        }

        if( $related = $pi->relatedInterface() ) {
            $related->speed =    $r->speed;
            $related->status =   $r->status;
            $related->duplex =   $r->duplex;
            $related->save();
        }

        AlertContainer::push( 'Physical Interface updated.', Alert::SUCCESS );
        return Redirect::to( $r->cb ? route( "core-bundle@edit", [ "cb" => $r->cb ] ) : route( "virtual-interface@edit", [ "vi" => $pi->virtualinterfaceid ] ) );
    }

    /**
     * Delete a Physical Interface
     *
     * @param Request           $r instance of the current HTTP request
     * @param PhysicalInterface $pi
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function delete( Request $r, PhysicalInterface $pi): RedirectResponse
    {
        if( $_SERVER[ "HTTP_REFERER" ] === route( "physical-interface@list" ) ){
            $redirect = route( "physical-interface@list" );
        } else {
            $redirect = route( "virtual-interface@edit", [ "vi" => $pi->virtualinterfaceid ] );
        }

        if( $pi->coreInterface ) {
            AlertContainer::push( 'You cannot delete this physical interface as there is a core bundle linked with it.', Alert::DANGER );
            return Redirect::to( $redirect );
        }

        $this->deletePi( $r, $pi, true );
        AlertContainer::push( 'Physical Interface deleted.', Alert::SUCCESS );
        return Redirect::to( $redirect );
    }

    /**
     * Utility function called by edit(). If this physical interface being edited is part of a core link,
     * this function adds the details of the PI on the other end of the core link to the `$data` array.
     *
     * @param PhysicalInterface|null $pi
     * @param array             $data
     *
     * @return array
     */
    private function mergeCoreLinkDetails( ?PhysicalInterface $pi, array $data ): array
    {
        if( !$pi || !( $piB = $pi->otherPICoreLink() ) ) {
            return $data;
        }

        /** @var $piB PhysicalInterface */
        $data['switch-b']        = $piB->switchPort->switchid;
        $data['switch-port-b']   = $piB->switchportid;
        $data['status-b']        = $piB->status;
        $data['speed-b']         = $piB->speed;
        $data['duplex-b']        = $piB->duplex;
        $data['autoneg-label-b'] = $piB->autoneg ? 1 : 0;
        $data['notes-b']         = $piB->notes ?? '';

        return $data;
    }

    /**
     * Utility function called by edit(). If this physical interface being edited is for a resold customer,
     * this function adds the details of the fanout port to the `$data` array.
     *
     * @param PhysicalInterface|null     $pi
     * @param VirtualInterface|null      $vi
     * @param array                     $data
     *
     * @return array
     */
    private function mergeFanoutDetails( ?PhysicalInterface $pi, ?VirtualInterface $vi, array $data ): array
    {
        if( !( $this->resellerMode() && $vi && $vi->customer->reseller ) ) {
            return $data;
        }

        if( $pi && $pi->fanoutPhysicalInterface()->exists() ) {
            $data['fanout']                 = $pi->fanoutPhysicalInterface ? 1 : 0;
            $data['switch-fanout']          = $pi->fanoutPhysicalInterface->switchPort->switcher->id;
            $data['switch-port-fanout']     = $pi->fanoutPhysicalInterface->switchPort->id;
        }

        return $data;
    }
}