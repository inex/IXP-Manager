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

use Former, Log, Redirect;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{
    Aggregators\SwitcherAggregator,
    CoreBundle,
    CoreInterface,
    Customer,
    Switcher,
    SwitchPort,
    VirtualInterface
};

use IXP\Http\Requests\CoreBundle\{
    Store,
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * CoreBundle Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundleController extends Common
{
    /**
     * CoreBundleController constructor.
     */
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
            'cbs'       => CoreBundle::all()
        ]);
    }

    /**
     * Display the form to create a core bundle wizard
     *
     * @return View
     */
    public function createWizard(): View
    {
        return view( 'interfaces/core-bundle/create/form-wizard' )->with([
            'switches'                      => Switcher::select( [ 'id', 'name' ] )->orderBy( 'name' )->get(),
            'customers'                     => Customer::whereType(  Customer::TYPE_INTERNAL )->get(),
        ]);
    }

    /**
     * Display the form to edit a core bundle
     *
     * @param  Request      $request    Instance of the current HTTP request
     * @param  CoreBundle   $cb         ID of the Core bundle
     *
     * @return  View
     */
    public function edit( Request $request,  CoreBundle $cb ): View
    {
        // fill the form with the core bundle data
        Former::populate([
            'custid'                    => $request->old('custid',    $cb->customer()    ),
            'description'               => $request->old('description', $cb->description   ),
            'graph_title'               => $request->old('graph_title', $cb->graph_title   ),
            'cost'                      => $request->old('cost',        $cb->cost          ),
            'preference'                => $request->old('preference',  $cb->preference    ),
            'type'                      => $request->old('type',        $cb->type          ),
            'subnet'                    => $request->old('subnet',      $cb->ipv4_subnet   ),
            'enabled'                   => $request->old('enabled',     ( $cb->enabled ? 1 : 0 ) ),
            'bfd'                       => $request->old('bfd',         ( $cb->bfd     ? 1 : 0 ) ),
            'stp'                       => $request->old('stp',         ( $cb->stp     ? 1 : 0 ) ),
        ]);

        return view( 'interfaces/core-bundle/edit/edit-wizard' )->with([
            'cb'                            => $cb,
            'customers'                     => Customer::whereType(  Customer::TYPE_INTERNAL )->get(),
            'switchPortsSideA'              => SwitcherAggregator::allPorts( $cb->switchSideX( true  )->id ,[ SwitchPort::TYPE_CORE, SwitchPort::TYPE_UNSET ], [], true ),
            'switchPortsSideB'              => SwitcherAggregator::allPorts( $cb->switchSideX( false )->id ,[ SwitchPort::TYPE_CORE, SwitchPort::TYPE_UNSET ], [], true ),
        ]);
    }

    /**
     * Create a core bundle
     *
     * @param   Store $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function storeWizard( Store $r ): RedirectResponse
    {
        $cb = CoreBundle::create( $r->all() );

        $via = new VirtualInterface;
        $vib = new VirtualInterface;

        // Set values to the Virtual Interface side A and B
        foreach( [ 'a' => $via , 'b' => $vib ] as $side => $vi ){
            $vi->custid         =   $r->custid;
            $vi->mtu            =   $r->mtu;
            $vi->name           =   $r->input( "vi-name-$side"            );
            $vi->channelgroup   =   $r->input( "vi-channel-number-$side"  );
            $vi->trunk          =   $r->framing ?? false;
            $vi->fastlacp       =   $r->input( 'fast-lacp'  ) ?? false;

            if( (int)$r->type === CoreBundle::TYPE_L2_LAG ) {
                $vi->lag_framing = true;
            }

            $vi->save();
        }

        // Creating all the elements linked to the new core bundle (core links, core interfaces, physical interfaces)
        $this->buildCorelink( $cb, $r, [ 'a' => $via , 'b' => $vib ] , false );

        Log::notice( $r->user()->username . ' added a core bundle with (id: ' . $cb->id . ')' );
        AlertContainer::push( 'Core bundle created', Alert::SUCCESS );

        return Redirect::to( route( "core-bundle@list" ) );
    }

    /**
     * Edit core bundle
     *
     * @param Store         $r instance of the current HTTP request
     * @param CoreBundle    $cb
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function updateWizard( Store $r, CoreBundle $cb): RedirectResponse
    {
        // Getting the virtual inferfaces (side A/B)
        $vis = $cb->virtualInterfaces();

        // Set the customer to the Virtual interface for each side
        $vis[ 'a' ]->custid = $r->custid;
        $vis[ 'a' ]->save();
        $vis[ 'b' ]->custid = $r->custid;
        $vis[ 'b' ]->save();

        $cb->update( $r->all() );

        Log::notice( $r->user()->username . ' updated a core bundle with (id: ' . $cb->id . ')' );
        AlertContainer::push( 'Core bundle updated.', Alert::SUCCESS );

        return Redirect::to( route( 'core-bundle@list' ) );
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
     * @param Request       $r
     * @param CoreBundle    $cb
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function delete( Request $r, CoreBundle $cb ): RedirectResponse
    {
        $cb->deleteObject();

        Log::notice( $r->user()->username." deleted a core bundle (id: " . $cb->id . ')' );
        AlertContainer::push( 'Core bundle deleted.', Alert::SUCCESS );
        return Redirect::to( route( "core-bundle@list" ) );
    }
}