<?php

namespace IXP\Http\Controllers\Interfaces;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Log, Redirect;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Http\Requests\{
    StoreCoreLink
};

use IXP\Models\{
    CoreBundle,
    CoreInterface,
    CoreLink,
    SwitchPort
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
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
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
     * @param StoreCoreLink $r instance of the current HTTP request
     * @param CoreBundle    $cb
     *
     * @return  RedirectResponse
     *
     */
    public function store( StoreCoreLink $r, CoreBundle $cb ): RedirectResponse
    {
        // Creating all the elements linked to the new core link (core interfaces, physical interfaces)
        $this->buildCorelink( $cb, $r, $cb->virtualInterfaces(), true );

        Log::notice( $r->user()->getUsername() . ' added a core link for the core bundle with (id: ' . $cb->id . ')' );
        AlertContainer::push( 'Core link added.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@edit" , [ "cb" => $cb->id ] ) );
    }

    /**
     * Edit the core links (enabled/BFD/Subnet) associated to a core bundle
     *
     * @param Request       $r instance of the current HTTP request
     * @param CoreBundle    $cb
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function update( Request $r, CoreBundle $cb ): RedirectResponse
    {
        foreach( $cb->corelinks as $cl ){
            $cl->enabled = $r->input( 'enabled-' . $cl->id ) ?? false;

            if( $cb->typeECMP() ){
                $cl->bfd =  $r->input( 'bfd-'.$cl->id ) ?? false;
                $cl->ipv4_subnet = $r->input( 'subnet-'.$cl->id );
            }

            $cl->save();
        }

        Log::notice( $r->user()->getUsername() . ' edited the core links from the core bundle with (id: ' . $cb->id . ')' );
        AlertContainer::push( 'Core links updated.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@edit", [ "cb" => $cb->id ] ) );

    }

    /**
     * Delete a Core link
     *
     * Delete the associated core interface/ physical interface
     * Change the type of the switch ports to UNSET
     *
     * @param Request       $r
     * @param CoreBundle    $cb
     * @param CoreLink      $cl
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function delete( Request $r, CoreBundle $cb, CoreLink $cl ) : RedirectResponse
    {
        $cl->delete();
        foreach( $cl->coreInterfaces() as $ci ) {
            /** @var CoreInterface $ci */
            $pi = $ci->physicalInterface;
            $pi->switchPort->update( [ 'type' => SwitchPort::TYPE_UNSET ] );

            $ci->delete();
            $pi->delete();
        }

        Log::notice( $r->user()->getUsername()." deleted a core link (id: " . $cl->id . ')' );
        AlertContainer::push( 'Core link deleted.', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@edit", [ "cb" => $cb->id ] ) );
    }
}