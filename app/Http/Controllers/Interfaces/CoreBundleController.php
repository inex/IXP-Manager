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

use D2EM, Former, Input, Log, Redirect;

use Entities\{
    CoreBundle as CoreBundleEntity,
    CoreLink as CoreLinkEntity,
    CoreInterface as CoreInterfaceEntity,
    Customer as CustomerEntity,
    Switcher as SwitcherEntity,
    VirtualInterface as VirtualInterfaceEntity,
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

    public function __construct()
    {
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
    public function list(): View
    {
        AlertContainer::push( 'The core bundle functionality is not ready for production use.', Alert::DANGER );

        return view( 'interfaces/core-bundle/list' )->with([
            'cbs'       => D2EM::getRepository( CoreBundleEntity::class )->findAll()
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
     * Display the form to edit a core bundle
     *
     * @param  Request  $request    Instance of the current HTTP request
     * @param  int      $id         ID of the Core bundle
     *
     * @return  View
     */
    public function edit( Request $request,  int $id = null ): View
    {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ){
            abort(404);
        }

        if( $cb ) {
            // fill the form with the core bundle data
            Former::populate([
                'customer'                  => $request->old('customer', $cb->getCustomer() ),
                'description'               => $request->old('description', $cb->getDescription() ),
                'graph-title'               => $request->old('graph-title', $cb->getGraphTitle() ),
                'cost'                      => $request->old('cost', $cb->getCost() ),
                'preference'                => $request->old('preference', $cb->getPreference() ),
                'type'                      => $request->old('type', $cb->getType() ),
                'subnet'                    => $request->old('subnet', $cb->getIPv4Subnet() ) ,
                'enabled'                   => $request->old('enabled', ( $cb->getEnabled()    ? 1 : 0 ) ),
                'bfd'                       => $request->old('bfd', ( $cb->getBFD()        ? 1 : 0 ) ),
                'stp'                       => $request->old('stp', ( $cb->getSTP()        ? 1 : 0 ) ),
            ]);
        }

        return view( 'interfaces/core-bundle/edit-wizard' )->with([
            'cb'                            => $cb,
            'customers'                     => D2EM::getRepository( CustomerEntity::class )->getAsArray( null, [ CustomerEntity::TYPE_INTERNAL ] )
        ]);
    }

    /**
     * Set all the data from the request to the core bundle object
     *
     * @param Request                   $request    instance of the current HTTP request
     * @param CoreBundleEntity          $cb         Core bundle object receiving the datas
     *
     * @return bool
     */
    private function setDataToCB( Request $request, CoreBundleEntity $cb ): bool
    {
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

        return true;
    }

    /**
     * Add a core bundle/core links
     *
     * @param   StoreCoreBundle $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function addStoreWizard( StoreCoreBundle $request ): RedirectResponse
    {
        /** @var CoreBundleEntity $cb */
        $cb = new CoreBundleEntity;
        D2EM::persist( $cb );

        $via = new VirtualInterfaceEntity;
        D2EM::persist( $via );

        $vib = new VirtualInterfaceEntity;
        D2EM::persist( $vib );

        /** @var CustomerEntity $cust */
        $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'customer' ) )  ;

        $this->setDataToCB( $request, $cb );

        // Set values to the Virtual Interface side A and B
        foreach( [ 'a' => $via , 'b' => $vib ] as $side => $vi ){
            /** @var $vi VirtualInterfaceEntity */
            $vi->setCustomer(           $cust );
            $vi->setMtu(                $request->input( "mtu"                      ) );
            $vi->setName(               $request->input( "vi-name-$side"            ) );
            $vi->setChannelgroup(       $request->input( "vi-channel-number-$side"  ) );
            $vi->setTrunk(        $request->input( 'framing'    ) ?? false      );
            $vi->setFastLACP(   $request->input( 'fast-lacp'  ) ?? false      );

            if( $request->input( "type" ) == CoreBundleEntity::TYPE_L2_LAG ){
                $vi->setLagFraming( true );
            }
        }

        // Check if there is at least 1 core link created for the core bundle
        if( $request->input( 'nb-core-links' ) == 0 || $request->input( 'nb-core-links' ) == null ){
            return Redirect::to( route( "core-bundle@add-wizard" ) )->withInput( Input::all() );
        }

        // Creating all the element linked to the core bundle ( core link, core interface , physical interface)
        for( $i = 1; $i <= $request->input( 'nb-core-links' ); $i++ ){
            $this->buildCorelink( $cb, $request, [ 'a' => $via , 'b' => $vib], $i , false  );
        }

        D2EM::flush();

        Log::notice( $request->user()->getUsername() . ' added a core bundle with (id: ' . $cb->getId() . ')' );

        AlertContainer::push( 'New core bundle created', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@list" ) );
    }

    /**
     * Edit core bundle
     *
     * @param   StoreCoreBundle $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function editStoreWizard( StoreCoreBundle $request ): RedirectResponse {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $request->input( 'cb' ) ) ) ) {
            abort('404', 'Unknown Core Bundle');
        }

        // Getting the virtual inferfaces (side A/B)
        $vis = $cb->getVirtualInterfaces();

        /** @var CustomerEntity $cust */
        $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'customer' ) )  ;

        // Set the customer to the Virtual interface for each side
        $vis[ 'A' ]->setCustomer( $cust );
        $vis[ 'B' ]->setCustomer( $cust );

        $this->setDataToCB( $request, $cb );

        D2EM::flush();

        Log::notice( $request->user()->getUsername() . ' updated a core bundle with (id: ' . $cb->getId() . ')' );

        AlertContainer::push( 'Core bundle updated.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@edit", [ "id" => $cb->getId() ] ) );
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
     * @param   Request $request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request ): RedirectResponse
    {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $request->input( 'id' ) ) ) ) {
            abort('404', 'Unknown Core bundle');
        }

        foreach( $cb->getCoreLinks() as $cl ){
            /** @var CoreLinkEntity $cl */
            foreach( $cl->getCoreInterfaces() as $ci ){
                /** @var CoreInterfaceEntity $ci */
                $pi = $ci->getPhysicalInterface();
                $vi = $pi->getVirtualInterface();

                D2EM::remove( $ci );
                D2EM::remove( $pi );
                D2EM::remove( $vi );
            }
            D2EM::remove( $cl );
        }

        D2EM::remove( $cb );

        D2EM::flush();

        Log::notice( $request->user()->getUsername()." deleted a core bundle (id: " . $request->input( 'id' ) . ')' );

        AlertContainer::push( 'Core bundle deleted.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@list" ) );
    }

}