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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use Exception, Former, Log, Redirect;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{
    Aggregators\SwitcherAggregator,
    CoreBundle,
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
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Interfaces
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundleController extends Common
{
    /**
     * Display the core bundles list
     *
     * @return  View
     */
    public function list(): View
    {
        return view( 'interfaces/core-bundle/list' )->with([
            'cbs'       => CoreBundle::with( 'coreLinks' )->get()
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
            'switches'      => Switcher::select( [ 'id', 'name' ] )
                ->orderBy( 'name' )->get(),
            'customers'     => Customer::internal()->get(),
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
            $vi->trunk          =   $r->framing ?? false;
            $vi->fastlacp       =   $r->input( 'fast-lacp'  ) ?? false;

            if( (int)$r->type === CoreBundle::TYPE_L2_LAG ) {
                $vi->lag_framing = true;
            }

            if( (int)$r->type !== CoreBundle::TYPE_ECMP ) {
                $r->merge( [ "vi-name-$side" => trim( $r->input( "vi-name-$side" ) , '"') ] );
                $vi->name           =   $r->input( "vi-name-$side"            );
                $vi->channelgroup   =   $r->input( "vi-channel-number-$side"  );
            }

            $vi->save();
        }

        // Creating all the elements linked to the new core bundle (core links, core interfaces, physical interfaces)
        $this->buildCorelink( $cb, $r, [ 'a' => $via , 'b' => $vib ] , false );

        Log::notice( $r->user()->username . ' created a core bundle with (id: ' . $cb->id . ')' );
        AlertContainer::push( 'Core bundle created', Alert::SUCCESS );
        return Redirect::to( route( "core-bundle@list" ) );
    }

    /**
     * Display the form to edit a core bundle
     *
     * @param  Request      $request      Instance of the current HTTP request
     * @param  CoreBundle   $coreBundle     Core bundle
     *
     * @return  View
     */
    public function edit( Request $request, CoreBundle $coreBundle ): View
    {
        $customer = $coreBundle->customer();
        $customerId = !$customer ? 0 : $customer->id;

        // fill the form with the core bundle data
        Former::populate( [
            'custid'      => $request->old( 'custid', (string)$customerId ),
            'description' => $request->old( 'description', $coreBundle->description ),
            'graph_title' => $request->old( 'graph_title', $coreBundle->graph_title ),
            'cost'        => $request->old( 'cost', (string)$coreBundle->cost ),
            'preference'  => $request->old( 'preference', (string)$coreBundle->preference ),
            'type'        => $request->old( 'type', (string)$coreBundle->type ),
            'ipv4_subnet' => $request->old( 'ipv4_subnet', $coreBundle->ipv4_subnet ),
            'enabled'     => $request->old( 'enabled', (string)$coreBundle->enabled ),
            'bfd'         => $request->old( 'bfd', (string)$coreBundle->bfd ),
            'stp'         => $request->old( 'stp', (string)$coreBundle->stp ),
        ] );

        $switchSideA = $coreBundle->switchSideX();
        $switchSideAId = $switchSideA ? $switchSideA->id : null;
        $switchSideB = $coreBundle->switchSideX( false );
        $switchSideBId = $switchSideB ? $switchSideB->id : null;

        return view( 'interfaces/core-bundle/edit/edit-wizard' )->with( [
            'cb'               => $coreBundle,
            'customers'        => Customer::internal()->get(),
            'switchPortsSideA' => SwitcherAggregator::allPorts( $switchSideAId, [ SwitchPort::TYPE_CORE, SwitchPort::TYPE_UNSET ], notAssignToPI: true ),
            'switchPortsSideB' => SwitcherAggregator::allPorts( $switchSideBId, [ SwitchPort::TYPE_CORE, SwitchPort::TYPE_UNSET ], notAssignToPI: true ),
        ] );
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
        return redirect( route( 'core-bundle@list' ) );
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
     * @param  Request  $r
     * @param  CoreBundle  $cb
     *
     * @return  RedirectResponse
     *
     * @throws Exception
     */
    public function delete( Request $r, CoreBundle $cb ): RedirectResponse
    {
        $cb->deleteObject();
        Log::notice( $r->user()->username." deleted a core bundle (id: " . $cb->id . ')' );
        AlertContainer::push( 'Core bundle deleted.', Alert::SUCCESS );
        return redirect( route( "core-bundle@list" ) );
    }
}