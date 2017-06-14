<?php
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

namespace IXP\Http\Controllers\Interfaces;

use D2EM, Former, Input, Redirect;

use Entities\{
    CoreBundle          as CoreBundleEntity,
    CoreLink            as CoreLinkEntity,
    CoreInterface       as CoreInterfaceEntity,
    Switcher            as SwitcherEntity,
    SwitchPort          as SwitchPortEntity,
    Customer            as CustomerEntity,
    VirtualInterface    as VirtualInterfaceEntity,
    PhysicalInterface   as PhysicalInterfaceEntity
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
        return view( 'core-bundle/index' )->with([
            'listCb'       => D2EM::getRepository( CoreBundleEntity::class )->findAll( )
        ]);
    }

    /**
     * Display the form to edit a core bundle
     *
     * @return View
     */
    public function editWizard(): View {
        Former::open()->rules([
            'description'                => 'required|string|max:255',
            'graph-title'                => 'required|string|max:255',
            'cost'                       => 'integer',
        ]);

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'core-bundle/edit-wizard' )->with([
            'types'                 => CoreBundleEntity::$TYPES,
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
        $returnHTML = view('core-bundle/core-link-frag')->with([
            'switches'                      => D2EM::getRepository( SwitcherEntity::class )->getNames(),
            'nbLink'                        => $nb,
            'enabled'                       => $request->input("enabled" ) ? true : false,
            'bundleType'                    => array_key_exists( $request->input("bundleType" ), CoreBundleEntity::$TYPES ) ? $request->input("bundleType" ) : CoreBundleEntity::TYPE_ECMP ,
        ])->render();

        return response()->json( ['success' => true, 'htmlFrag' => $returnHTML, 'nbCoreLinks' => $nb ] );
    }

    /**
     * Add a core bundle/core links (set all the data needed)
     *
     * @param   StoreCoreBundle $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function storeWizard( StoreCoreBundle $request ): RedirectResponse {
        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'customer' ) ) ) ) {
            abort('404', 'Unknown Customer');
        }

        if( $request->input( 'nb-core-links' ) == 0 || $request->input( 'nb-core-links' ) == null ){
            // redirect
        }

        /** @var VirtualInterfaceEntity $vi */
        $vi = new VirtualInterfaceEntity;
        D2EM::persist( $vi );

        $vi->setCustomer( $cust );

        /** @var CoreBundleEntity $cb */
        $cb = new CoreBundleEntity;
        D2EM::persist( $cb );

        $cb->setDescription( $request->input( 'description' ) );
        $cb->setGraphTitle( $request->input( 'graph-title' ) );
        $cb->setCost( $request->input( 'cost' ) );
        $cb->setType( $request->input( 'type' ) );
        $cb->setEnabled( $request->input( 'enabled' ) ? $request->input( 'enabled' ) : false );
        $cb->setBFD( $request->input( 'bfd' ) ? $request->input( 'bfd' ) : false );
        $cb->setIPv4Subnet( $request->input( 'subnet' ) ? $request->input( 'subnet' ) : null );

        for( $i = 1; $i <= $request->input( 'nb-core-links' ); $i++ ){
            /** @var CoreLinkEntity $cl */
            $cl = new CoreLinkEntity;
            D2EM::persist( $cl );

            $cl->setCoreBundle( $cb );
            $cl->setEnabled( $request->input( 'enabled-cl-'.$i ) ? $request->input( 'enabled-cl-'.$i ) : false );

            if( $request->input( 'type' ) == CoreBundleEntity::TYPE_ECMP ){
                $cl->setBFD( $request->input( 'bfd-'.$i ) );
                $cl->setIPv4Subnet( $request->input( 'subnet-'.$i ) );
            }

            // Side A
            /** @var SwitchPortEntity $spA */
            if( !( $spA = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'hidden-sp-a-'.$i ) ) ) ) {
                return Redirect::to( 'core-bundle/add-wizard' )->withInput( Input::all() );
            }

            $spA->setType( SwitchPortEntity::TYPE_CORE );

            /** @var PhysicalInterfaceEntity $piSideA */
            $piSideA = new PhysicalInterfaceEntity;
            D2EM::persist( $piSideA );

            $piSideA->setSwitchPort( $spA );
            $piSideA->setVirtualInterface( $vi );

            /** @var CoreInterfaceEntity $ciSideA */
            $ciSideA = new CoreInterfaceEntity;
            D2EM::persist( $ciSideA );
            $ciSideA->setPhysicalInterface( $piSideA );


            // Side B
            if( !( $spB = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'hidden-sp-b-'.$i ) ) ) ) {
                return Redirect::to( 'core-bundle/add-wizard' )->withInput( Input::all() );
            }

            $spB->setType( SwitchPortEntity::TYPE_CORE );

            /** @var PhysicalInterfaceEntity $piSideB */
            $piSideB = new PhysicalInterfaceEntity;
            D2EM::persist( $piSideB );

            $piSideB->setSwitchPort( $spB );
            $piSideB->setVirtualInterface( $vi );

            /** @var CoreInterfaceEntity $ciSideB */
            $ciSideB = new CoreInterfaceEntity;
            D2EM::persist( $ciSideB );
            $ciSideB->setPhysicalInterface( $piSideB );

            $cl->setCoreInterfaceSideA( $ciSideA );
            $cl->setCoreInterfaceSideB( $ciSideB );
        }

        D2EM::flush();

        AlertContainer::push( 'The core bundle has been added successfully.', Alert::SUCCESS );

        return Redirect::to( 'core-bundle/list' );
    }
}
