<?php

namespace IXP\Http\Controllers\Interfaces;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM, Redirect, Former, Input;

use Illuminate\View\View;

use Illuminate\Http\{
    JsonResponse, RedirectResponse, Request
};

use Entities\{
    PhysicalInterface as PhysicalInterfaceEntity,
    Switcher as SwitcherEntity,
    SwitchPort as SwitchPortEntity,
    VirtualInterface as VirtualInterfaceEntity
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
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PhysicalInterfaceController extends Common
{

    /**
     * Display all the physical interfaces as a list
     *
     * @return  View
     */
    public function list(): View {
        return view( 'interfaces/physical/list' )->with([
            'pis'               => D2EM::getRepository( PhysicalInterfaceEntity::class )->getForList()
        ]);
    }

    /**
     * Display a physical interface
     *
     * @param int $id ID of the physical interface
     * @return  View
     */
    public function view( int $id ): View {
        if( !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        return view( 'interfaces/physical/view' )->with([
            'pi' => $pi
        ]);
    }

    /**
     * Display the form to edit a physical interface from the core bundle from
     *
     * @param   integer $id ID of the physical interface
     * @param   integer $cb ID of core bundle
     *
     * @return View
     */
    public function editFromCb( int $id , int $cb ){
        return $this->edit( $id , null, $cb );
    }

    /**
     * Display the form to edit a physical interface
     *
     * @param   int $id ID of the physical interface
     * @param   int $viid ID of the virtual interface
     * @param   int $cb id we come from the core bundle edit form
     *
     * @return View|RedirectResponse
     */
    public function edit( int $id, int $viid = null, int $cb = null ) {

        /** @var PhysicalInterfaceEntity $pi */
        /** @var VirtualInterfaceEntity $vi */
        $vi = $pi = false;

        if( $id && !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        if( $viid && !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $viid ) ) ) {
            AlertContainer::push( 'You need a containing virtual interface before you add a physical interface', Alert::DANGER );
            return Redirect::back();
        }

        $old = request()->old();
        $data = [];

        if( $pi ) {
            // ==== EDIT PI MODE

            // we never edit a fanout port:
            if( $pi->getSwitchPort()->isTypeFanout() ) {
                AlertContainer::push( 'Do not edit fanout ports directly. Edit the peering interface and the fanout port will be updated to match.', Alert::DANGER );
                return Redirect::back();
            }

            // fill the form with physical interface data
            $data = [
                'switch'                  => array_key_exists( 'switch',        $old    ) ? $old['switch']          : $pi->getSwitchPort()->getSwitcher()->getId(),
                'switch-port'             => array_key_exists( 'switch-port',   $old    ) ? $old['switch-port']     : $pi->getSwitchPort()->getId(),
                'status'                  => array_key_exists( 'status',        $old    ) ? $old['status']          : $pi->getStatus(),
                'speed'                   => array_key_exists( 'speed',         $old    ) ? $old['speed']           : $pi->getSpeed(),
                'duplex'                  => array_key_exists( 'duplex',        $old    ) ? $old['duplex']          : $pi->getDuplex(),
                'autoneg-label'           => array_key_exists( 'autoneg-label', $old    ) ? $old['autoneg-label']   : ( $pi->getAutoneg() ? 1 : 0 ),
            ];

            // get all the switch ports available and add the switch port associated to the physical interface in the list
            $switchports = array_merge(
                D2EM::getRepository( SwitcherEntity::class )->getAllPortsNotAssignedToPI( $pi->getSwitchPort()->getSwitcher()->getId(), [], null ),
                [ [ "name" => $pi->getSwitchPort()->getName(), "id" => $pi->getSwitchPort()->getId(), "typeid" => $pi->getSwitchPort()->getType(), "type" => $pi->getSwitchPort()->resolveType() ] ]
            );



            // ascending sort the array by ID
            usort( $switchports, function( $item1, $item2 ) {
                return $item1['id'] <=> $item2['id'];
            });

        }

        // get the fanout details or other side of the core link details as/if applicable
        $data = $this->mergeFanoutDetails(      $pi, $pi ? $pi->getVirtualInterface() : null, $data );
        $data = $this->mergeCoreLinkDetails(    $pi, $data );

        Former::populate( $data );

        return view( 'interfaces/physical/edit' )->with([
            'switches'                    => D2EM::getRepository( SwitcherEntity::class )->getNames( false ),
            'switchports'                 => isset( $switchports ) ? $switchports : [],
            'pi'                          => $pi,
            'otherPICoreLink'             => $pi ? $pi->getOtherPICoreLink() : false,
            'vi'                          => $vi,
            'cb'                          => $cb ? $cb : false,
            'enableFanout'                => $this->resellerMode() && $vi && $vi->getCustomer()->isResoldCustomer(),
            'spFanout'                    => $pi && isset( $data['fanout'] ) && $data['fanout'] && $pi->getFanoutPhysicalInterface() ? $pi->getFanoutPhysicalInterface()->getSwitchPort()->getId() : false,
            'notes'                       => $pi ? ( array_key_exists( 'notes',           $old ) ? $old['notes']           :  $pi->getNotes() ) : ( array_key_exists( 'notes',           $old ) ? ( $old['notes'] ?? '' )           : "" ),
            'notesb'                      => array_key_exists( 'notes-b',           $data ) ? $data['notes-b']           : ""
        ]);
    }

    /**
     * Utility function called by edit(). If this physical interface being edited is part of a core link,
     * this function adds the details of the PI on the other end of the core link to the `$data` array.
     *
     * @param PhysicalInterfaceEntity $pi
     * @param array $data
     *
     * @return array
     */
    private function mergeCoreLinkDetails( $pi, array $data ): array
    {
        if( !$pi || !( $piB = $pi->getOtherPICoreLink() ) ) {
            return $data;
        }

        $data['switch-b']        = $piB->getSwitchPort()->getSwitcher()->getId();
        $data['switch-port-b']   = $piB->getSwitchPort()->getId();
        $data['status-b']        = $piB->getStatus();
        $data['speed-b']         = $piB->getSpeed();
        $data['duplex-b']        = $piB->getDuplex();
        $data['autoneg-label-b'] = $piB->getAutoneg() ? 1 : 0;
        $data['notes-b']         = $piB->getNotes();

        return $data;
    }

    /**
     * Utility function called by edit(). If this physical interface being edited is for a resold customer,
     * this function adds the details of the fanout port to the `$data` array.
     *
     * @param PhysicalInterfaceEntity $pi
     * @param VirtualInterfaceEntity $vi
     * @param array $data
     *
     * @return array
     */
    private function mergeFanoutDetails( $pi, $vi, array $data ): array
    {

        if( !( $this->resellerMode() && $vi && $vi->getCustomer()->isResoldCustomer() ) ) {
            return $data;
        }

        if( $pi && $pi->getFanoutPhysicalInterface() ) {
            $data['fanout']                 = $pi->getFanoutPhysicalInterface() ? 1 : 0;
            $data['switch-fanout']          = $pi->getFanoutPhysicalInterface()->getSwitchPort()->getSwitcher()->getId();
            $data['switch-port-fanout']     = $pi->getFanoutPhysicalInterface()->getSwitchPort()->getId();

            // @yann: not sure why this is here as well as fanout above?
            $data['fanout-checked']         = $pi->getFanoutPhysicalInterface() ? 1 : 0;
        }

        return $data;
    }


    /**
     * Edit a physical interface (set all the data needed)
     *
     * @param   StorePhysicalInterface $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( StorePhysicalInterface $request ): RedirectResponse {
        /** @var PhysicalInterfaceEntity $pi */
        /** @var VirtualInterfaceEntity $vi */
        /** @var SwitchPortEntity $sp */

        if( $request->input( 'id', false ) ) {
            if( !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort( 404, 'Unknown physical interface' );
            }
        } else {
            $pi = new PhysicalInterfaceEntity;
            D2EM::persist( $pi );
        }

        $vi = D2EM::getRepository( VirtualInterfaceEntity::class    )->find( $request->input( 'viid'        ) );    /** @var VirtualInterfaceEntity     $vi   */
        $sp = D2EM::getRepository( SwitchPortEntity::class          )->find( $request->input( 'switch-port' ) );    /** @var SwitchPortEntity           $sp   */

        // when presenting the add PI form, we include peering and unknown port types; set the selected port as peering:
        $sp->setType( SwitchPortEntity::TYPE_PEERING );

        if( $pi->getOtherPICoreLink() ){
            // check if the user has changed the switch port
            if( $sp->getId() != $pi->getSwitchPort()->getId() ){
                $oldSp = $pi->getSwitchPort();
                $oldSp->setType( SwitchPortEntity::TYPE_UNSET );
            }

            // check if the user has selected the same switch port
            if( $pi->getOtherPICoreLink()->getSwitchPort()->getSwitcher()->getId() == $sp->getSwitcher()->getId() ){
                AlertContainer::push( 'The switch port selected for this physical interface is already used by the other physical interface of the core bundle. Please select another switch port', Alert::DANGER );
                return Redirect::back( )->withInput( $request->all() );
            }

            $sp->setType( SwitchPortEntity::TYPE_CORE );
        }

        $this->setBundleDetails( $vi );

        $pi->setSwitchPort(         $sp );
        $pi->setVirtualInterface(   $vi );
        $pi->setStatus(             $request->input( 'status'           ) );
        $pi->setSpeed(              $request->input( 'speed'            ) );
        $pi->setDuplex(             $request->input( 'duplex'           ) );
        $pi->setAutoneg(            $request->input( 'autoneg-label'    ) ? 1 : 0 );
        $pi->setNotes(              $request->input( 'notes'            ) );





        if( !$this->processFanoutPhysicalInterface( $request, $pi, $vi) ){
            return Redirect::back( )->withInput( Input::all() );
        }

        if( $related = $pi->getRelatedInterface() ) {
            /** @var PhysicalInterfaceEntity $related */
            $related->setSpeed(     $request->input( 'speed'    ) );
            $related->setStatus(    $request->input( 'status'   ) );
            $related->setDuplex(    $request->input( 'duplex'   ) );
        }


        D2EM::flush();

        AlertContainer::push( 'Physical Interface updated successfully.', Alert::SUCCESS );
        return Redirect::to( $request->input( 'cb' ) ? route( "core-bundle/edit", [ "id" => $request->input( 'cb' ) ] ) : route( "interfaces/virtual/edit", [ "id" => $pi->getVirtualInterface()->getId() ] ) );
    }


    /**
     * Delete a Physical Interface
     *
     * @param   Request $request instance of the current HTTP request
     * @param   int $id ID of the Physical Interface
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function delete( Request $request,  int $id ): JsonResponse {
        /** @var PhysicalInterfaceEntity $pi */
        if( !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        if( $pi->getCoreInterface() ){
            AlertContainer::push( 'You cannot delete this physical interface as there is a core bundle linked with it.', Alert::DANGER );
            return response()->json( [ 'success' => false ] );
        }

        if( $pi->getSwitchPort()->isTypePeering() && $pi->getFanoutPhysicalInterface() ) {
            $pi->getSwitchPort()->setPhysicalInterface( null );
            $pi->getFanoutPhysicalInterface()->getSwitchPort()->setType( SwitchPortEntity::TYPE_PEERING );
        }
        else if( $pi->getSwitchPort()->isTypeFanout() && $pi->getPeeringPhysicalInterface() ) {
            if( $request->input( 'related' ) ){
                $this->removeRelatedInterface( $pi );
            }

            $pi->getPeeringPhysicalInterface()->setFanoutPhysicalInterface( null );
        }

        if( $request->input( 'related' ) && $pi->getRelatedInterface() ) {
            $this->removeRelatedInterface( $pi );
            D2EM::flush();
        }

        $this->setBundleDetails( $pi->getVirtualInterface() );


        D2EM::remove( $pi );
        D2EM::flush();

        AlertContainer::push( 'The Physical Interface has been deleted successfully.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }
}