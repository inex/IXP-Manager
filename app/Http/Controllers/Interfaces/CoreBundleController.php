<?php

namespace IXP\Http\Controllers\Interfaces;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM, Former, Redirect;

use IXP\Utils\Former\Framework\TwitterBootstrap4;

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

use IXP\Http\Requests\{
    StoreCoreBundle
};

use Illuminate\View\View;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * CoreBundle Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundleController extends Common
{

    public function __construct() {

        if( !config( 'ixp_fe.frontend.beta.core_bundles', false ) ) {
            AlertContainer::push( 'The core bundle functionality is not ready for production use.', Alert::DANGER );
            Redirect::to('')->send();
        }

    }

    /**
     * Display the core bundles list
     *
     * @return  View
     */
    public function list(): View {
        AlertContainer::push( 'The core bundle functionality is not ready for production use.', Alert::DANGER );

        return view( 'interfaces/core-bundle/list' )->with([
            'cbs'       => D2EM::getRepository( CoreBundleEntity::class )->findAll( )
        ]);
    }

    /**
     * Display the form to add a core bundle wizard
     *
     * @return View
     */
    public function addWizard(): View {
        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'interfaces/core-bundle/add-wizard' )->with([
            'switches'                      => D2EM::getRepository( SwitcherEntity::class )->getNames(),
            'customers'                     => D2EM::getRepository( CustomerEntity::class )->getAsArray( null, [ CustomerEntity::TYPE_INTERNAL ] ),
        ]);
    }

    /**
     * Display the form to add core links to the bundle core form
     *
     * @param  Request    $request        instance of the current HTTP request
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function addCoreLinkFrag( Request $request ) :JsonResponse {
        $nb = $request->input("nbCoreLink") + 1;



        $returnHTML = view('interfaces/core-bundle/core-link-frag')->with([
            'nbLink'                        => $nb,
            'formerrrrrr'                   => 'dd',
            'enabled'                       => $request->input("enabled" ) ? true : false,
            'bundleType'                    => array_key_exists( $request->input("bundleType" ), CoreBundleEntity::$TYPES ) ? $request->input("bundleType" ) : CoreBundleEntity::TYPE_ECMP ,
        ])->render();

        return response()->json( ['success' => true, 'htmlFrag' => $returnHTML, 'nbCoreLinks' => $nb ] );
    }


    /**
     * Display the form to edit a core bundle
     *
     * @param  int $id ID of the Core bundle
     *
     * @return  View
     */
    public function edit( int $id = null ): View {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ){
            abort(404);
        }

        $old = request()->old();

        if( $cb ) {
            // fill the form with Virtual interface data
            Former::populate([
                'customer'                  => array_key_exists( 'customer',        $old    ) ? $old['customer']        : $cb->getCustomer(),
                'description'               => array_key_exists( 'description',     $old    ) ? $old['description']     : $cb->getDescription(),
                'graph-title'               => array_key_exists( 'graph-title',     $old    ) ? $old['graph-title']     : $cb->getGraphTitle(),
                'cost'                      => array_key_exists( 'cost',            $old    ) ? $old['cost']            : $cb->getCost(),
                'preference'                => array_key_exists( 'preference',      $old    ) ? $old['preference']      : $cb->getPreference(),
                'type'                      => array_key_exists( 'type',            $old    ) ? $old['type']            : $cb->getType(),
                'subnet'                    => array_key_exists( 'subnet',          $old    ) ? $old['subnet']          : $cb->getIPv4Subnet() ,
                'enabled'                   => array_key_exists( 'enabled',         $old    ) ? $old['enabled']         : ( $cb->getEnabled()    ? 1 : 0 ),
                'bfd'                       => array_key_exists( 'bfd',             $old    ) ? $old['bfd']             : ( $cb->getBFD()        ? 1 : 0 ),
                'stp'                       => array_key_exists( 'stp',             $old    ) ? $old['stp']             : ( $cb->getSTP()        ? 1 : 0 ),
            ]);
        }

        return view( 'interfaces/core-bundle/edit-wizard' )->with([
            'cb'                            => $cb,
            'customers'                     => D2EM::getRepository( CustomerEntity::class )->getAsArray( null, [CustomerEntity::TYPE_INTERNAL] )
        ]);
    }

    /**
     * Add a core bundle/core links (set all the data needed)
     *
     * @param   StoreCoreBundle $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
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
            $via = $vis[ 'A' ];
            $vib = $vis[ 'B' ];
        }
        else{
            $cb = new CoreBundleEntity;
            D2EM::persist( $cb );

            $via = new VirtualInterfaceEntity;
            D2EM::persist( $via );

            $vib = new VirtualInterfaceEntity;
            D2EM::persist( $vib );
        }

        // set the value to the core bundle
        $cb->setDescription(    $request->input( 'description'          ) );
        $cb->setGraphTitle(     $request->input( 'graph-title'          ) );
        $cb->setCost(           $request->input( 'cost'                 ) );
        $cb->setPreference(     $request->input( 'preference'           ) );
        $cb->setType(           $request->input( 'type'                 ) );
        $cb->setEnabled(        $request->input( 'enabled'           ) ?? false );
        $cb->setBFD(            $request->input( 'bfd'                  ) ?? false  );
        $cb->setIPv4Subnet(     $request->input( 'subnet'         ) ?? null  );
        $cb->setSTP(            $request->input( 'stp',false     ) ?? false );

        /** @var CustomerEntity $cust */
        $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'customer' ) )  ;

        // Set the customer to the Virtual interface for each side
        $via->setCustomer( $cust );
        $vib->setCustomer( $cust );

        // if we edit the core bundle we stop here
        if( $edit ){
            D2EM::flush();
            AlertContainer::push( 'Core bundle updated successfully.', Alert::SUCCESS );
            return Redirect::to( route( "core-bundle/edit", [ "id" => $cb->getId() ] ) );
        }

        foreach( [ 'a' => $via , 'b' => $vib ] as $side => $vi ){
            /** @var $vi VirtualInterfaceEntity */
            // Set value to the Virtual Interface side A and B
            $vi->setCustomer(       $cust );
            $vi->setMtu(            $request->input( "mtu"                      ) );
            $vi->setName(           $request->input( "vi-name-$side"            ) );
            $vi->setChannelgroup(   $request->input( "vi-channel-number-$side"  ) );
            $vi->setTrunk(          $request->input( 'framing'                  ) ?? false  );
            $vi->setFastLACP(       $request->input( 'fast-lacp'                ) ?? false  );

            if( $request->input( "type" ) == CoreBundleEntity::TYPE_L2_LAG ){
                $vi->setLagFraming( true );
            }

        }

        // CHeck if there is at least 1 core link created for the core bundle
        if( $request->input( 'nb-core-links' ) == 0 || $request->input( 'nb-core-links' ) == null ){
            return Redirect::to( route( "core-bundle/add" ) )->withInput( $request->all() );
        }

        for( $i = 1; $i <= $request->input( 'nb-core-links' ); $i++ ){
            // Creating all the element linked to the core bundle ( core link, core interface , physical interface)
            $this->buildCorelink( $cb, $request, [ 'a' => $via , 'b' => $vib], $i , false  );
        }

        D2EM::flush();

        AlertContainer::push( 'The core bundle has been added successfully.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle/list" ) );
    }



    /**
     * Edit the core links associated to a core bundle
     *
     * @param   Request $request instance of the current HTTP request
     *
     * @param   int $id ID of the core bundle
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function storeCoreLinks( Request $request, int $id ): RedirectResponse {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ) {
            abort('404', 'Unknown Core bundle');
        }

        foreach( $cb->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            $cl->setEnabled( $request->input( 'enabled-'.$cl->getId() ) ?? false );

            if( $cb->isECMP() ){
                $cl->setBFD( $request->input( 'bfd-'.$cl->getId() ) ?? false  );
                $cl->setIPv4Subnet( $request->input( 'subnet-'.$cl->getId() ) );
            }
        }

        D2EM::flush();

        AlertContainer::push( 'The core links have been edited successfully.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle/edit", [ "id" => $cb->getId() ] ) );

    }


    /**
     * Add a core link to a core bundle only in EDIT MODE
     *
     * @param   Request $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function addCoreLink( Request $request ): RedirectResponse {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $request->input( 'core-bundle' ) ) ) ) {
            abort('404', 'Unknown Core Bundle');
        }

        if( $request->input( 'nb-core-links' ) == 0 || $request->input( 'nb-core-links' ) == null ){
            return Redirect::to( route( "core-bundle/edit", [ "id" => $cb->getId()] ) )->withInput( $request->all() );
        }

        /** @var VirtualInterfaceEntity $via */
        $via = $cb->getVirtualInterfaces()[ 'A' ];
        /** @var VirtualInterfaceEntity $vib */
        $vib = $cb->getVirtualInterfaces()[ 'B' ];

        $this->buildCorelink( $cb, $request, [ 'a' => $via , 'b' => $vib], 1 , true );

        D2EM::flush();

        AlertContainer::push( 'The core link has been added successfully.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle/edit" , [ "id" => $cb->getId() ] ) );
    }

    /**
     * Build all everything that a Core Bundle need (core link, core Interface etc)
     *
     * @param   CoreBundleEntity $cb Corebundle object
     * @param   Request $request instance of the current HTTP request
     * @param   array $vis array of the Virtual interfaces ( side A and B ) linked to the core bundle
     * @param   int $clNumber
     * @param   bool $edit Are we editing the core bundle ?
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    private function buildCorelink( $cb, $request, $vis, $clNumber, $edit ){
        // Set value to the Core Bundle
        /** @var CoreLinkEntity $cl */
        $cl = new CoreLinkEntity;
        D2EM::persist( $cl );

        $cl->setCoreBundle( $cb );
        $cl->setEnabled( $request->input( "enabled-cl-$clNumber" ) ?? false );

        $bfd = ( $request->input( "bfd-$clNumber") ?? false );

        $type = $edit ? $cb->getType() : $request->input( 'type' ) ;

        $cl->setBFD( ( $type == CoreBundleEntity::TYPE_ECMP ) ? $bfd : false );
        $cl->setIPv4Subnet( $request->input( "subnet-$clNumber" ) );

        foreach( $vis as $side => $vi ){
            /** @var SwitchPortEntity $spa */
            /** @var SwitchPortEntity $spb */
            if( !( ${ 'sp'.$side } = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( "hidden-sp-$side-$clNumber" ) ) ) ) {
                return Redirect::back()->withInput( $request->all() );
            }

            ${ 'sp'.$side }->setType( SwitchPortEntity::TYPE_CORE );

            /** @var PhysicalInterfaceEntity $pia */
            /** @var PhysicalInterfaceEntity $pib */
            ${ 'pi'.$side } = new PhysicalInterfaceEntity;
            D2EM::persist( ${ 'pi'.$side } );

            ${ 'pi'.$side }->setSwitchPort(        ${ 'sp'.$side } );
            ${ 'pi'.$side }->setVirtualInterface(  $vi );
            ${ 'pi'.$side }->setSpeed(             $edit ? $cb->getSpeedPi() : $request->input( 'speed' ) );
            ${ 'pi'.$side }->setDuplex(            $edit ? $cb->getDuplexPi() : $request->input( 'duplex'   )  );
            ${ 'pi'.$side }->setAutoneg(           $edit ? $cb->getAutoNegPi() : $request->input( 'auto-neg' ) ?? false );
            ${ 'pi'.$side }->setStatus(            PhysicalInterfaceEntity::STATUS_CONNECTED );

            /** @var CoreInterfaceEntity $cia */
            /** @var CoreInterfaceEntity $cib */
            ${ 'ci'.$side } = new CoreInterfaceEntity;
            D2EM::persist( ${ 'ci'.$side } );
            ${ 'ci'.$side }->setPhysicalInterface( ${ 'pi'.$side } );
        }

        $cl->setCoreInterfaceSideA( $cia );
        $cl->setCoreInterfaceSideB( $cib );

        $vis[ 'a' ]->addPhysicalInterface( $pia );
        $vis[ 'b' ]->addPhysicalInterface( $pib );
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
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function deleteCoreBundle( int $id ): JsonResponse {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ) {
            abort('404', 'Unknown Core bundle');
        }

        if( D2EM::getRepository( CoreBundleEntity::class )->delete( $cb ) ) {
            AlertContainer::push( "Core bundle deleted successfully.", Alert::SUCCESS );
        } else {
            AlertContainer::push( "Error: core bundle could not be deleted. Please open a GitHub bug report.", Alert::DANGER );
        }

        return response()->json( [ 'success' => true ] );

    }

    /**
     * Delete a Core link
     *
     * Delete the associated core interface/ physical interface
     * Change the type of the switch ports to UNSET
     *
     * @param  int $id ID of the core link to delete
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function deleteCoreLink( int $id ) : JsonResponse {

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