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

use D2EM, Former, Input, Redirect;

use Entities\{
    CoreBundle as CoreBundleEntity,
    CoreLink as CoreLinkEntity,
    CoreInterface as CoreInterfaceEntity,
    Switcher as SwitcherEntity,
    SwitchPort as SwitchPortEntity,
    Customer as CustomerEntity,
    VirtualInterface as VirtualInterfaceEntity,
    PhysicalInterface as PhysicalInterfaceEntity
};

use Illuminate\Http\{
    JsonResponse,
    RedirectResponse,
    Request
};

use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\{
    StoreCoreBundle
};

use Illuminate\Support\Facades\View as FacadeView;
use Illuminate\View\View;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * Router Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundleController extends Controller
{
    /**
     * Display all the core bundles
     *
     * @return  View
     */
    public function list( int $id = null ): View {
        return view( 'interfaces/core-bundle/list' )->with([
            'cbs'       => D2EM::getRepository( CoreBundleEntity::class )->findAll( )
        ]);
    }

    /**
     * Display the form to edit a core bundle
     *
     * @return View
     */
    public function addWizard(): View {
        Former::open()->rules([
            'description'                   => 'required|string|max:255',
            'graph-title'                   => 'required|string|max:255',
            'cost'                          => 'integer',
            'mtu'                           => 'required|integer',
        ]);

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'interfaces/core-bundle/add-wizard' )->with([
            'switches'              => D2EM::getRepository( SwitcherEntity::class )->getNames(),
            'types'                 => CoreBundleEntity::$TYPES,
            'speed'                 => PhysicalInterfaceEntity::$SPEED,
            'duplex'                => PhysicalInterfaceEntity::$DUPLEX,
            'customers'             => D2EM::getRepository( CustomerEntity::class )->getAsArray( null, [CustomerEntity::TYPE_INTERNAL] )
        ]);
    }

    /**
     * Display the form to add core links to the bundle core form
     *
     * @param  Request    $request        instance of the current HTTP request
     * @return JsonResponse
     */
    public function addCoreLinkFrag( Request $request ) :JsonResponse {
        $nb = $request->input("nbCoreLink") + 1;

        $returnHTML = view('interfaces/core-bundle/core-link-frag')->with([
            'nbLink'                        => $nb,
            'enabled'                       => $request->input("enabled" ) ? true : false,
            'bundleType'                    => array_key_exists( $request->input("bundleType" ), CoreBundleEntity::$TYPES ) ? $request->input("bundleType" ) : CoreBundleEntity::TYPE_ECMP ,
        ])->render();

        return response()->json( ['success' => true, 'htmlFrag' => $returnHTML, 'nbCoreLinks' => $nb ] );
    }


    /**
     * Display the form to edit a core bundle
     *
     * @params  int $id ID of the Core bundle
     * @return  view
     */
    public function edit( int $id = null ): View {
        $cb = false;
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ){
            abort(404);
        }

        Former::open()->rules([
            'description'                   => 'required|string|max:255',
            'graph-title'                   => 'required|string|max:255',
            'cost'                          => 'integer',
            'mtu'                           => 'required|integer',
        ]);

        if( $cb ) {
            // fill the form with Virtual interface data
            Former::populate([
                'customer'                  => $cb->getCustomer(),
                'description'               => $cb->getDescription(),
                'graph-title'               => $cb->getGraphTitle(),
                'cost'                      => $cb->getCost(),
                'preference'                => $cb->getPreference(),
                'type'                      => $cb->getType(),
                'enabled'                   => $cb->getEnabled() ? 1 : 0,
                'bfd'                       => $cb->getBFD() ? 1 : 0,
                'subnet'                    => $cb->getIPv4Subnet() ,
            ]);
        }

        return view( 'interfaces/core-bundle/edit-wizard' )->with([
            'cb'                            => $cb,
            'types'                         => CoreBundleEntity::$TYPES,
            'speed'                         => PhysicalInterfaceEntity::$SPEED,
            'duplex'                        => PhysicalInterfaceEntity::$DUPLEX,
            'customers'                     => D2EM::getRepository( CustomerEntity::class )->getAsArray( null, [CustomerEntity::TYPE_INTERNAL] )
        ]);
    }

    /**
     * Add a core bundle/core links (set all the data needed)
     *
     * @param   StoreCoreBundle $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function storeWizard( StoreCoreBundle $request ): RedirectResponse {
        $edit = false;

        /** @var CoreBundleEntity $cb */
        if( $request->input( 'cb' ) ) {
            if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $request->input( 'cb' ) ) ) ) {
                abort('404', 'Unknown Core Bundle');
            }
            $edit = true;

            $vis = $cb->getVirtualInterfaces();
            $viSideA = $vis[ 'A' ];
            $viSideB = $vis[ 'B' ];
        }
        else{
            $cb = new CoreBundleEntity;
            D2EM::persist( $cb );

            /** @var VirtualInterfaceEntity $viSideA */
            $viSideA = new VirtualInterfaceEntity;
            D2EM::persist( $viSideA );

            /** @var VirtualInterfaceEntity $viSideB */
            $viSideB = new VirtualInterfaceEntity;
            D2EM::persist( $viSideB );
        }

        // set the value to the core bundle
        $cb->setDescription( $request->input( 'description' ) );
        $cb->setGraphTitle( $request->input( 'graph-title' ) );
        $cb->setCost( $request->input( 'cost' ) );
        $cb->setPreference( $request->input( 'preference' ) );
        $cb->setType( $request->input( 'type' ) );
        $cb->setEnabled( $request->input( 'enabled' ) ? $request->input( 'enabled' ) : false );
        $cb->setBFD( $request->input( 'bfd' ) ? $request->input( 'bfd' ) : false );
        $cb->setIPv4Subnet( $request->input( 'subnet' ) ? $request->input( 'subnet' ) : null );

        /** @var CustomerEntity $cust */
        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'customer' ) ) ) ) {
            abort('404', 'Unknown Customer');
        }

        // Set the customer to the Virtual interface of each side
        $viSideA->setCustomer( $cust );
        $viSideB->setCustomer( $cust );

        if( $edit ){
            D2EM::flush();
            AlertContainer::push( 'The core bundle has been updated successfully.', Alert::SUCCESS );
            return Redirect::to( 'interfaces/core-bundle/edit/'.$cb->getId() );
        }


        // Set value to the Virtual Interface side A
        $viSideA->setCustomer( $cust );
        $viSideA->setMtu( $request->input( 'mtu' ) );
        $viSideA->setName( $request->input( 'vi-name-a' ) );
        $viSideA->setChannelgroup( $request->input( 'vi-channel-number-a' ) );
        $viSideA->setTrunk( $request->input( 'framing' ) ? $request->input( 'framing' ) : false  );
        $viSideA->setFastLACP( $request->input( 'fast-lacp' ) ? $request->input( 'fast-lacp' ) : false  );

        // Set value to the Virtual Interface side B

        $viSideB->setMtu( $request->input( 'mtu' ) );
        $viSideB->setName( $request->input( 'vi-name-b' ) );
        $viSideB->setChannelgroup( $request->input( 'vi-channel-number-b' ) );
        $viSideB->setLagFraming( $request->input( 'framing' ) ? $request->input( 'framing' ) : false  );
        $viSideB->setFastLACP( $request->input( 'fast-lacp' ) ? $request->input( 'fast-lacp' ) : false  );

        if( $request->input( 'nb-core-links' ) == 0 || $request->input( 'nb-core-links' ) == null ){
            return Redirect::to( 'interfaces/core-bundle/add-wizard' )->withInput( Input::all() );
        }

        for( $i = 1; $i <= $request->input( 'nb-core-links' ); $i++ ){

            // Set value to the Core Bundle
            /** @var CoreLinkEntity $cl */
            $cl = new CoreLinkEntity;
            D2EM::persist( $cl );

            $cl->setCoreBundle( $cb );
            $cl->setEnabled( $request->input( 'enabled-cl-'.$i ) ? $request->input( 'enabled-cl-'.$i ) : false );

            $bfd = ($request->input( 'bfd-'.$i ) ? $request->input( 'bfd-'.$i ) : false );

            $cl->setBFD( ( $request->input( 'type' ) == CoreBundleEntity::TYPE_ECMP ) ? $bfd : false );
            $cl->setIPv4Subnet( $request->input( 'subnet-'.$i ) );


            // Side A
            /** @var SwitchPortEntity $spA */
            if( !( $spA = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'hidden-sp-a-'.$i ) ) ) ) {
                return Redirect::to( 'interfaces/core-bundle/add-wizard' )->withInput( Input::all() );
            }

            $spA->setType( SwitchPortEntity::TYPE_CORE );

            /** @var PhysicalInterfaceEntity $piSideA */
            $piSideA = new PhysicalInterfaceEntity;
            D2EM::persist( $piSideA );

            $piSideA->setSwitchPort( $spA );
            $piSideA->setVirtualInterface( $viSideA );
            $piSideA->setSpeed( $request->input( 'speed' ) );
            $piSideA->setDuplex( $request->input( 'duplex' ) );
            $piSideA->setAutoneg( $request->input( 'auto-neg' ) ? $request->input( 'auto-neg' ) : false );
            $piSideA->setStatus( PhysicalInterfaceEntity::STATUS_CONNECTED );


            /** @var CoreInterfaceEntity $ciSideA */
            $ciSideA = new CoreInterfaceEntity;
            D2EM::persist( $ciSideA );
            $ciSideA->setPhysicalInterface( $piSideA );



            // Side B
            /** @var SwitchPortEntity $spB */
            if( !( $spB = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'hidden-sp-b-'.$i ) ) ) ) {
                return Redirect::to( 'interfaces/core-bundle/add-wizard' )->withInput( Input::all() );
            }

            $spB->setType( SwitchPortEntity::TYPE_CORE );

            /** @var PhysicalInterfaceEntity $piSideB */
            $piSideB = new PhysicalInterfaceEntity;
            D2EM::persist( $piSideB );

            $piSideB->setSwitchPort( $spB );
            $piSideB->setVirtualInterface( $viSideB );
            $piSideB->setSpeed( $request->input( 'speed' ) );
            $piSideB->setDuplex( $request->input( 'duplex' ) );
            $piSideB->setAutoneg( $request->input( 'auto-neg' ) ? $request->input( 'auto-neg' ) : false );
            $piSideB->setStatus( PhysicalInterfaceEntity::STATUS_CONNECTED );

            /** @var CoreInterfaceEntity $ciSideB */
            $ciSideB = new CoreInterfaceEntity;
            D2EM::persist( $ciSideB );
            $ciSideB->setPhysicalInterface( $piSideB );


            $cl->setCoreInterfaceSideA( $ciSideA );
            $cl->setCoreInterfaceSideB( $ciSideB );

            $viSideA->addPhysicalInterface( $piSideA );
            $viSideB->addPhysicalInterface( $piSideB );
        }

        D2EM::flush();

        AlertContainer::push( 'The core bundle has been added successfully.', Alert::SUCCESS );

        return Redirect::to( 'interfaces/core-bundle/list' );
    }



    /**
     * Edit the core links associated to a ore bundle
     *
     * @param   Request $request instance of the current HTTP request
     * @param   int $id ID of the core bundle
     * @return  RedirectResponse
     */
    public function storeCoreLinks( Request $request, int $id ): RedirectResponse {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ) {
            abort('404', 'Unknown Core bundle');
        }


        foreach( $cb->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            $cl->setEnabled( $request->input( 'enabled-'.$cl->getId() ) ? $request->input( 'enabled-'.$cl->getId() ) : false );

            if( $cb->isECMP() ){
                $cl->setBFD( $request->input( 'bfd-'.$cl->getId() ) ? $request->input( 'bfd-'.$cl->getId() ) : false  );
                $cl->setIPv4Subnet( $request->input( 'subnet-'.$cl->getId() ) );
            }

        }

        D2EM::flush();

        AlertContainer::push( 'The core links have been edited with success.', Alert::SUCCESS );

        return Redirect::to( 'interfaces/core-bundle/edit/'.$cb->getId() );

    }


    /**
     * Delete the core bundle and everything associated with
     *
     ** Delete the core links
     ** Delete the core interfaces
     ** Delete the physical interfaces
     ** Delete the core Virtual Interfaces
     ** Change the status of the switch ports to UNSET
     *
     * @param   int $id ID of the core bundle
     * @return  JsonResponse
     */
    public function deleteCoreBundle( int $id ): JsonResponse {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ) {
            abort('404', 'Unknown Core bundle');
        }

        foreach( $cb->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */

            foreach( $cl->getCoreInterfaces() as $ci ){
                /** @var CoreInterfaceEntity $ci */

                $pi = $ci->getPhysicalInterface();

                $vi = $pi->getVirtualInterface();

                $sp = $vi->getSwitchPort();

                $sp->setType( SwitchPortEntity::TYPE_UNSET );

                D2EM::remove( $ci );
                D2EM::remove( $pi );
                D2EM::remove( $vi );
            }

            D2EM::remove( $cl );
        }

        D2EM::remove( $cb );

        D2EM::flush();

        AlertContainer::push( 'The core bundle has been deleted with success.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );

    }


    /**
     * Add a core link to a core bundle
     *
     * @param   Request $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function addCoreLink( Request $request ): RedirectResponse {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $request->input( 'core-bundle' ) ) ) ) {
            abort('404', 'Unknown Core Bundle');
        }

        if( $request->input( 'nb-core-links' ) == 0 || $request->input( 'nb-core-links' ) == null ){
            return Redirect::to( 'interfaces/core-bundle/edit/'.$cb->getId() )->withInput( Input::all() );
        }

        $virtualInterface = $cb->getVirtualInterfaces();

        // Set value to the Core Bundle
        /** @var CoreLinkEntity $cl */
        $cl = new CoreLinkEntity;
        D2EM::persist( $cl );

        $cl->setCoreBundle( $cb );
        $cl->setEnabled( $request->input( 'enabled-cl-1' ) ? $request->input( 'enabled-cl-1' ) : false );

        $bfd = ( $request->input( 'bfd-1' ) ? $request->input( 'bfd-1' ) : false );

        $cl->setBFD( ( $cb->getType() == CoreBundleEntity::TYPE_ECMP ) ? $bfd : false );
        $cl->setIPv4Subnet( $request->input( 'subnet-1' ) );


        // Side A
        /** @var SwitchPortEntity $spA */
        if( !( $spA = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'sp-a-1' ) ) ) ) {
            return Redirect::to( 'interfaces/core-bundle/edit/'.$cb->getId() )->withInput( Input::all() );
        }

        $spA->setType( SwitchPortEntity::TYPE_CORE );



        /** @var PhysicalInterfaceEntity $piSideA */
        $piSideA = new PhysicalInterfaceEntity;
        D2EM::persist( $piSideA );

        $piSideA->setSwitchPort( $spA );
        $piSideA->setVirtualInterface( $virtualInterface[ 'A' ] );
        $piSideA->setSpeed( $cb->getSpeedPi() );
        $piSideA->setDuplex( $cb->getDuplexPi() );
        $piSideA->setAutoneg( $cb->getAutoNegPi() );
        $piSideA->setStatus( PhysicalInterfaceEntity::STATUS_CONNECTED );


        /** @var CoreInterfaceEntity $ciSideA */
        $ciSideA = new CoreInterfaceEntity;
        D2EM::persist( $ciSideA );
        $ciSideA->setPhysicalInterface( $piSideA );



        // Side B
        /** @var SwitchPortEntity $spB */
        if( !( $spB = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'sp-b-1' ) ) ) ) {
            return Redirect::to( 'interfaces/core-bundle/edit/'.$cb->getId() )->withInput( Input::all() );
        }

        $spB->setType( SwitchPortEntity::TYPE_CORE );

        /** @var PhysicalInterfaceEntity $piSideB */
        $piSideB = new PhysicalInterfaceEntity;
        D2EM::persist( $piSideB );

        $piSideB->setSwitchPort( $spB );
        $piSideB->setVirtualInterface( $virtualInterface[ 'B' ] );
        $piSideB->setSpeed( $cb->getSpeedPi() );
        $piSideB->setDuplex( $cb->getDuplexPi() );
        $piSideB->setAutoneg(  $cb->getAutoNegPi() );
        $piSideB->setStatus( PhysicalInterfaceEntity::STATUS_CONNECTED );

        /** @var CoreInterfaceEntity $ciSideB */
        $ciSideB = new CoreInterfaceEntity;
        D2EM::persist( $ciSideB );
        $ciSideB->setPhysicalInterface( $piSideB );


        $cl->setCoreInterfaceSideA( $ciSideA );
        $cl->setCoreInterfaceSideB( $ciSideB );

        $virtualInterface[ 'A' ]->addPhysicalInterface( $piSideA );
        $virtualInterface[ 'B' ]->addPhysicalInterface( $piSideB );

        D2EM::flush();

        AlertContainer::push( 'The core link has been added successfully.', Alert::SUCCESS );

        return Redirect::to( 'interfaces/core-bundle/edit/'.$cb->getId() );
    }

    /**
     * Delete a Core link
     *
     * Delete the associated core interface/ physical interface
     * Change the type of the switch ports to UNSET
     *
     * @param  int $id ID of the core link to delete
     * @return  JsonResponse
     */
    public function delete( int $id ) : JsonResponse {

        /** @var CoreLinkEntity $cl */
        if( !( $cl = D2EM::getRepository( CoreLinkEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        foreach( $cl->getCoreInterfaces() as $ci ){
            /** @var CoreInterfaceEntity $ci */
            $pi = $ci->getPhysicalInterface();
            $sp = $pi->getSwitchPort();

            $sp->setType( SwitchPortEntity::TYPE_UNSET );

            D2EM::remove( $pi );
            D2EM::remove( $ci );
        }
        D2EM::remove( $cl );
        D2EM::flush();

        AlertContainer::push( 'The core link has been deleted successfully.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }

}
