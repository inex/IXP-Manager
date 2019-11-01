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

use D2EM, Log, Redirect;

use Entities\{
    CoreBundle as CoreBundleEntity,
    CoreLink as CoreLinkEntity,
    CoreInterface as CoreInterfaceEntity,
    SwitchPort as SwitchPortEntity,
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Http\Requests\{
    StoreCoreLink
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Core Link Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreLinkController extends Common
{
    public function __construct()
    {
        if( !config( 'ixp_fe.frontend.beta.core_bundles', false ) ) {
            AlertContainer::push( 'The core bundle functionality is not ready for production use.', Alert::DANGER );
            Redirect::to('')->send();
        }
    }

    /**
     * Add a core link to a core bundle only when EDITING a core bundle
     *
     * @param   StoreCoreLink $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function addStore( StoreCoreLink $request ): RedirectResponse
    {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $request->input( 'core-bundle' ) ) ) ) {
            abort('404', 'Unknown Core Bundle');
        }

        // Creating all the elements linked to the new core link (core interfaces, physical interfaces)
        $this->buildCorelink( $cb, $request, [ 'a' => $cb->getVirtualInterfaces()[ 'A' ] , 'b' =>  $cb->getVirtualInterfaces()[ 'B' ] ] , true );

        D2EM::flush();

        Log::notice( $request->user()->getUsername() . ' added a core link for the core bundle with (id: ' . $cb->getId() . ')' );

        AlertContainer::push( 'Core link added.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@edit" , [ "id" => $cb->getId() ] ) );
    }

    /**
     * Edit the core links (enabled/BFD/Subnet) associated to a core bundle
     *
     * @param   Request $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function editStore( Request $request ): RedirectResponse
    {
        /** @var CoreBundleEntity $cb */
        if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $request->input( "cb" ) ) ) ) {
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

        Log::notice( $request->user()->getUsername() . ' edited the core links from the core bundle with (id: ' . $cb->getId() . ')' );

        AlertContainer::push( 'Core links updated.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@edit", [ "id" => $cb->getId() ] ) );

    }

    /**
     * Delete a Core link
     *
     * Delete the associated core interface/ physical interface
     * Change the type of the switch ports to UNSET
     *
     * @param  Request $request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request ) : RedirectResponse
    {
        /** @var CoreLinkEntity $cl */
        if( !( $cl = D2EM::getRepository( CoreLinkEntity::class )->find( $request->input( "id" ) ) ) ) {
            abort( 404 );
        }

        $cb = $cl->getCoreBundle();

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

        Log::notice( $request->user()->getUsername()." deleted a core link (id: " . $request->input( 'id' ) . ')' );

        AlertContainer::push( 'Core link deleted.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@edit", [ "id" => $cb->getId() ] ) );
    }
}