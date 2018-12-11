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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
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
    Request,
    JsonResponse,
    RedirectResponse
};


use Entities\{
    VirtualInterface as VirtualInterfaceEntity,
    Customer as CustomerEntity,
    Vlan as VlanEntity,
    Switcher as SwitcherEntity,
    PhysicalInterface as PhysicalInterfaceEntity,
    SwitchPort as SwitchPortEntity,
    VlanInterface as VlanInterfaceEntity
};

use IXP\Http\Requests\{
    StoreVirtualInterface,
    StoreVirtualInterfaceWizard
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;


/**
 * VirtualInterface Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VirtualInterfaceController extends Common
{
    /**
     * Display all the virtualInterfaces
     *
     * @return  View
     */
    public function list() : View {
        return view( 'interfaces/virtual/list' )->with([
            'vis'               => D2EM::getRepository( VirtualInterfaceEntity::class )->getForList(),
            'resellerMode'      => $this->resellerMode()
        ]);
    }

    /**
     * Display the form to add a virtual interface
     *
     * @param int $custId ID of customer
     *
     * @return  View
     */
    public function addWizardCustId( int $custId = null ) : View {
        return $this->wizard( $custId );
    }


    /**
     * Display the form to add a virtual interface with a selected customer
     *
     * @param int $custId Id of the customer to preselect
     *
     * @return  View
     */
    public function addCustId( int $custId = null ) : View {
        return $this->add( null, $custId);
    }

    /**
     * Display the form to add a virtual interface
     *
     * @param int $id Id of the virtual interface
     * @param int $custId Id of the customer to preselect
     *
     * @return View
     */
    public function add( int $id = null, int $custId = null ): View {
        $vi = $cust = false;

        $old = request()->old();

        /** @var VirtualInterfaceEntity $vi */
        if( $id != null ) {

            if( (!$vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            // fill the form with Virtual interface data
            Former::populate([
                'cust'                  => array_key_exists( 'cust',         $old    ) ? $old['cust']              : $vi->getCustomer(),
                'trunk'                 => array_key_exists( 'trunk',        $old    ) ? $old['trunk']             : ( $vi->getTrunk()      ? 1 : 0 ),
                'lag_framing'           => array_key_exists( 'lag_framing',  $old    ) ? $old['lag_framing']       : ( $vi->getLagFraming() ? 1 : 0 ),
                'fastlacp'              => array_key_exists( 'fastlacp',     $old    ) ? $old['fastlacp']          : ( $vi->getFastLACP()   ? 1 : 0 ),
                'name'                  => array_key_exists( 'name',         $old    ) ? $old['name']              : $vi->getName(),
                'description'           => array_key_exists( 'description',  $old    ) ? $old['description']       : $vi->getDescription(),
                'channel-group'         => array_key_exists( 'channel-group',$old    ) ? $old['channel-group']     : $vi->getChannelgroup(),
                'mtu'                   => array_key_exists( 'mtu',          $old    ) ? $old['mtu']               : $vi->getMtu(),
            ]);

        }

        /** @var CustomerEntity $cust */
        if( $custId != null ) {
            if ( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $custId ) ) ) {
                abort( 404 );
            }

            Former::populate([
                'cust'                  => array_key_exists( 'cust',          $old    ) ? $old['cust']               : $cust->getId(),
            ]);
        }



        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'interfaces/virtual/add' )->with([
            'cust'              => D2EM::getRepository( CustomerEntity::class   )->getNames(),
            'vls'               => D2EM::getRepository( VlanEntity::class       )->getNames(),
            'vi'                => $vi ? $vi : false,
            'cb'                => $vi ? $vi->getCoreBundle() : false,
            'selectedCust'      => $cust
        ]);
    }

    /**
     * Add or edit a virtual interface (set all the data needed)
     *
     * @param   StoreVirtualInterface $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( StoreVirtualInterface $request ): RedirectResponse {
        /** @var VirtualInterfaceEntity $vi */
        if( $request->input( 'id' ) && $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$vi ) {
                abort(404, 'Unknown router');
            }
        } else {
            $vi = new VirtualInterfaceEntity;
            D2EM::persist($vi);
        }

        $inputCust = $request->input( 'cust' );
        if( $request->input( 'selectedCust' ) ){
            $inputCust = $request->input( 'selectedCust' );
        }


        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $inputCust ) ) ) {
            abort(404, 'Unknown customer');
        }

        // we don't allow setting channel group or name until there's >= 1 physical interface / LAG framing:
        if( count( $vi->getPhysicalInterfaces() ) == 0 ) {
            $request->merge( [ 'name' => '' , 'channel-group' => null ] );
        }


        $vi->setCustomer(               $cust );
        $vi->setTrunk(            $request->input( 'trunk' )          ? 1 : 0 );
        $vi->setLagFraming(  $request->input( 'lag_framing' )    ? 1 : 0 );
        $vi->setFastLACP(       $request->input( 'fastlacp' )       ? 1 : 0);
        $vi->setName(                   $request->input( 'name'             ) );
        $vi->setDescription(            $request->input( 'description'      ) );
        $vi->setChannelgroup(           $request->input( 'channel-group'    ) );
        $vi->setMtu(                    $request->input( 'mtu'              ) );

        $this->setBundleDetails( $vi );

        if( count( $vi->getPhysicalInterfaces() ) > 0 ) {
            // We need to try and make naming of the virtual interface name automatic as well as choice
            // of the channel group number.

            // let's take group number first -> needs to be unique within a switch and > 0
            // (some devices may allow zero but programmatically it may be easier to avoid this due to legacy data)
            // if it's a number gt zero and it's changed (if we're editing)

            // ensure it's unique:
            if( count( $vi->getPhysicalInterfaces() ) == 1 && !$request->input( 'lag_framing' ) && $request->input( 'channel-group' ) === null ) {
                // no op -> this allows a user to set a null channel group number on an interface with one PI and no lag framing.
            } else if( !D2EM::getRepository(VirtualInterfaceEntity::class )->validateChannelGroup( $vi ) ) {
                AlertContainer::push( 'Channel group number is not unique within the switch.', Alert::DANGER );
                return Redirect::to( $request->input( 'id' ) ? 'interfaces/virtual/edit/'.$vi->getId() : 'interfaces/virtual/add' )->withInput();
            }
        }

        D2EM::flush();

        AlertContainer::push( 'Virtual Interface added/updated successfully.', Alert::SUCCESS );

        return Redirect::to( 'interfaces/virtual/edit/'.$vi->getId());

    }

    /**
     * Display the wizard form to add a virtual interface
     *
     * @param int $custId Id of the customer to preselect
     *
     * @return View
     */
    public function wizard( int $custId = null ): View {

        $cust = false; /** @var CustomerEntity $cust */
        if( $custId ) {
            if( $cust = D2EM::getRepository( CustomerEntity::class )->find( $custId ) ) {
                // fill the form with Virtual interface data
                Former::populate( [
                    'cust' => $cust->getId(),
                ] );
            } else {
                abort(404);
            }
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'interfaces/virtual/wizard' )->with([
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getNames(),
            'vli'                   => false,
            'vlans'                 => D2EM::getRepository( VlanEntity::class )->getNames( false ),
            'pi_switches'           => D2EM::getRepository( SwitcherEntity::class )->getNames( true ),
            'resoldCusts'           => $this->resellerMode() ? json_encode( D2EM::getRepository( CustomerEntity::class )->getResoldCustomerNames() ) : json_encode([]) ,
            'selectedCust'          => $cust
        ]);
    }

    /**
     * Add or edit a interface wizard (set all the data needed)
     *
     * @param   StoreVirtualInterfaceWizard $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function storeWizard( StoreVirtualInterfaceWizard $request ): RedirectResponse {
        // all validation of ids is in the request object, App\Http\Requests\StoreVirtualInterfaceWizard
        $c   = D2EM::getRepository( CustomerEntity::class   )->find( $request->input( 'cust'        ) );    /** @var CustomerEntity   $c      */
        $v   = D2EM::getRepository( VlanEntity::class       )->find( $request->input( 'vlan'        ) );    /** @var VlanEntity       $v      */
        $sp  = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'switch-port' ) );    /** @var SwitchPortEntity $sp     */

        $vi = new VirtualInterfaceEntity;
        D2EM::persist($vi);

        $vi->setTrunk( $request->input( 'trunk', false ) );
        $vi->setCustomer( $c );


        $pi = new PhysicalInterfaceEntity;
        D2EM::persist($pi);
        $pi->setSpeed(  $request->input( 'speed'  ) );
        $pi->setStatus( $request->input( 'status' ) );
        $pi->setDuplex( $request->input( 'duplex' ) );

        $pi->setVirtualInterface(   $vi );
        $vi->addPhysicalInterface(  $pi);

        $sp->setType( SwitchPortEntity::TYPE_PEERING );
        $pi->setSwitchPort( $sp );

        $vli = new VlanInterfaceEntity();
        D2EM::persist($vli);

        $vli->setVirtualInterface(  $vi );
        $vli->setVlan(              $v  );
        $vli->setIrrdbfilter(       $request->input( 'irrdbfilter',     false ) );
        $vli->setRsMoreSpecifics(   $request->input( 'rsmorespecifics', false ) );
        $vli->setMcastenabled(      $request->input( 'mcastenabled',    false ) );
        $vli->setMaxbgpprefix(      $request->input( 'maxbgpprefix',    null  ) === "0" ? null : $request->input( 'maxbgpprefix', null ) );
        $vli->setRsclient(          $request->input( 'rsclient',        false ) );
        $vli->setAs112client(       $request->input( 'as112client',     false ) );
        $vli->setBusyhost(          false );

        if( !$this->setIp($request, $v, $vli, false ) || !$this->setIp($request, $v, $vli, true ) ) {
            return Redirect::to('virtualInterface/add-wizard' )->withInput( Input::all() );
        }

        D2EM::flush();

        // add a warning if we're filtering on irrdb but have not configured one for the customer
        $this->warnIfIrrdbFilteringButNoIrrdbSourceSet($vli);


        AlertContainer::push( "New interface created!", Alert::SUCCESS );

        return Redirect::route( 'interfaces/virtual/edit', [ 'id' => $vi->getId() ] );
    }

    /**
     * Delete a Virtual Interface
     *
     * @param   Request $request instance of the current HTTP request
     * @param   int $id ID of the VirtualInterface
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function delete( Request $request,  int $id ): JsonResponse {
        /** @var VirtualInterfaceEntity $vi */
        if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }


        if( $vi->getCoreBundle() ) {
            AlertContainer::push( 'The Virtual Interface is linked to a Core Bundle. Delete the Core Bundle first to be able to delete the Virtual Interface.', Alert::DANGER );

            return response()->json( [ 'success' => false ]);
        }

        foreach( $vi->getPhysicalInterfaces() as $pi) {
            /** @var PhysicalInterfaceEntity $pi */
            $vi->removePhysicalInterface( $pi );

            if( $pi->getSwitchPort()->isTypePeering() && $pi->getFanoutPhysicalInterface() ) {
                $pi->getSwitchPort()->setPhysicalInterface( null );
                $pi->getFanoutPhysicalInterface()->getSwitchPort()->setType(SwitchPortEntity::TYPE_PEERING );
            } else if( $pi->getSwitchPort()->isTypeFanout() && $pi->getPeeringPhysicalInterface() ) {
                if( $request->input( 'related' ) ){
                    $this->removeRelatedInterface( $pi );
                }

                $pi->getPeeringPhysicalInterface()->setFanoutPhysicalInterface( null );
            }

            D2EM::remove( $pi );

            if( $request->input( 'related' ) && $pi->getRelatedInterface() ){
                $this->removeRelatedInterface( $pi );
            }

        }

        foreach( $vi->getVlanInterfaces() as $vli ) {
            /** @var VlanInterfaceEntity $vli */
            foreach( $vli->getLayer2Addresses() as $l2a) {
                D2EM::remove( $l2a );
            }

            $vi->removeVlanInterface( $vli );
            D2EM::remove( $vli );
        }

        foreach( $vi->getMACAddresses() as $mac){
            D2EM::remove( $mac );
        }

        D2EM::remove( $vi );
        D2EM::flush();

        AlertContainer::push( 'The Virtual Interface has been deleted successfully.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ]);
    }

}
