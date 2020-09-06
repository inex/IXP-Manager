<?php

namespace IXP\Http\Controllers;

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

use Auth, Former, Log, Redirect;

use IXP\Models\{Customer, IrrdbPrefix, Router, RouteServerFilter, Vlan};

use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse
};

use IXP\Http\Requests\RouteServerFilter\{
    Store
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Route Server Filtering Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RsFilterController extends Controller
{
    /**
     * Display the list of Route Server Filter
     *
     * @param Customer $cust
     *
     * @return  View
     *
     * @throws
     */
    public function list( Customer $cust ): View
    {
        $this->authorize( 'checkCustObject',  [ RouteServerFilter::class, $cust ]  );

        return view( 'rs-filter/list' )->with([
            "rsFilters"         => RouteServerFilter::where( "customer_id" , $cust->id )->orderBy( 'order_by' )->get(),
            "c"                 => $cust
        ]);
    }

    /**
     * Allow to display the form to create a route server filter
     *
     * @param Customer $cust
     *
     * @return  View
     *
     * @throws
     */
    public function create( Customer $cust ): View
    {
        $this->authorize( 'checkCustObject',  [ RouteServerFilter::class, $cust ]  );

        $vlanid     = request()->old( 'vlan_id',         null );
        $protocol   = request()->old( 'protocol',        null );
        $peer       = request()->old( 'peer_id',        null  );

        Former::populate( [
            'peer_id'               => $peer        ?? "Null",
            'vlan_id'               => $vlanid      ?? "Null",
            'protocol'              => $protocol    ?? "Null",
            'action_advertise'      => request()->old( 'action_advertise',   "Null"  ),
            'action_receive'        => request()->old( 'action_receive',     "Null"  ),
            'received_prefix'       => request()->old( 'received_prefix',     "*"     ),
            'advertised_prefix'     => request()->old( 'advertised_prefix',     "*"     ),
        ] );

        $advertisedPrefixes = [];
        if( $cust->maxprefixes < 2000 ) {
            $advertisedPrefixes = IrrdbPrefix::where( 'customer_id', $cust->id )->where( 'protocol', $protocol )->get()->toArray();
        }

        $peers = array_merge( [ '0' => [ 'id' => '0', 'name' => "All Peers" ] ], Customer::getByVlanAndProtocol( $vlanid , $protocol ) );
        foreach( $peers as $i => $p ) {
            if( $p['id'] === $cust->id ) {
                unset( $peers[$i] );
                break;
            }
        }

        return view( 'rs-filter/edit' )->with( [
            'rsf'                   => false,
            'c'                     => $cust,
            'vlans'                 => array_merge( [ '0' => [ 'id' => '0', 'name' => "All LANs" ] ], $this->getPublicPeeringVLANs( $cust->id ) ),
            'protocols'             => Router::$PROTOCOLS,
            'peers'                 => $peers,
            'advertisedPrefixes'    => $advertisedPrefixes
        ] );
    }

    /**
     * Allow to display the form to edit a route server filter
     *
     * @param RouteServerFilter   $rsf
     *
     * @return  View
     *
     * @throws
     */
    public function edit( RouteServerFilter $rsf ): View
    {
        $this->authorize( 'checkRsfObject',  [ RouteServerFilter::class, $rsf ] );

        $vlanid     = request()->old( 'vlan_id',     $rsf->vlan_id  ?? null );
        $protocol   = request()->old( 'protocol',    $rsf->protocol ?? null );
        $peerid     = request()->old( 'peer_id',    $rsf->peer_id   ?? null );

        Former::populate( [
            'vlan_id'               => $vlanid      ?? "null",
            'protocol'              => $protocol    ?? "null",
            'peer_id'               => $peerid      ?? 'null',
            'received_prefix'       => request()->old( 'received_prefix',           $rsf->received_prefix ),
            'advertised_prefix'     => request()->old( 'advertised_prefix',         $rsf->advertised_prefix ),
            'action_advertise'      => request()->old( 'action_advertise',   $rsf->action_advertise ?? 'Null' ),
            'action_receive'        => request()->old( 'action_receive',     $rsf->action_receive ?? 'Null' ),
        ] );

        return view( 'rs-filter/edit' )->with( [
            'rsf'       => $rsf,
            'c'         => $rsf->customer,
            'vlans'     => array_merge( [ '0' => [ 'id' => '0', 'name' => "All LANs" ] ], $this->getPublicPeeringVLANs( $rsf->customer_id ) ),
            'protocols' => Router::$PROTOCOLS,
            'peers'     => array_merge( [ '0' => [ 'id' => '0', 'name' => "All Peers" ] ], Customer::getByVlanAndProtocol( $vlanid , $protocol ) ),
        ] );
    }

    /**
     * Function to store A Route Server Filter object
     *
     * @param Store $request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function store( Store $request ): RedirectResponse
    {
        $cust = Customer::findOrFail( request( "custid" ) );

        $this->authorize( 'checkCustObject',  [ RouteServerFilter::class, $cust ]  );

        $rsf = RouteServerFilter::create( array_merge( $request->except( [ 'customer_id', 'enabled', 'order_by' ] ),
            [
                'customer_id'   => $cust->id,
                'enabled'       => true,
                'order_by'      => RouteServerFilter::where( 'customer_id', $cust->id )->get()->max( 'order_by' ) +1,
            ]
        ));

        Log::notice( Auth::user()->getUsername() . ' created a router server filter with ID ' . $rsf->id );

        AlertContainer::push( "Route Server Filter created", Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "cust" => $cust->id ] )  );
    }

    /**
     * Function to update A Route Server Filter object
     *
     * @param Store             $request
     * @param RouteServerFilter $rsf
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function update( Store $request, RouteServerFilter $rsf ): RedirectResponse
    {
        $this->authorize( 'checkRsfObject',  [ RouteServerFilter::class, $rsf ] );

        $rsf->update( $request->all() );

        Log::notice( Auth::user()->getUsername() . ' updated a router server filter with ID ' . $rsf->id );

        AlertContainer::push( "Route Server Filter updated", Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "cust" => $rsf->customer_id ] )  );
    }

    /**
     * Display the details of a Route Server Filter
     *
     * @param RouteServerFilter $rsf
     *
     * @return View
     */
    public function view( RouteServerFilter $rsf ): View
    {
        $this->authorize( 'checkRsfObject',  [ RouteServerFilter::class, $rsf ]  );

        return view( 'rs-filter/view' )->with( [
            'rsf'   => $rsf
        ] );
    }

    /**
     * Enable or disable a router server filter
     *
     * @param RouteServerFilter $rsf
     * @param int               $enable
     *
     * @return RedirectResponse
     *
     */
    public function toggleEnable( RouteServerFilter $rsf, int $enable ): RedirectResponse
    {
        $this->authorize( 'checkRsfObject',  [ RouteServerFilter::class, $rsf ]  );

        $status = $enable ? 'enabled' : 'disabled';

        $rsf->enabled =  ( bool )$enable;
        $rsf->save();

        Log::notice( Auth::user()->getUsername() . ' ' . $status . ' a router server filter with ID ' . $rsf->id );

        AlertContainer::push( 'The route server filter has been ' . $status, Alert::SUCCESS );
        return redirect( route( "rs-filter@list", [ "cust" => $rsf->customer_id ] ) );
    }


    /**
     * Change the order by of a route server filter (up/down)
     *
     * @param RouteServerFilter     $rsf
     * @param int                   $up     If true move up if false move down
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function changeOrderBy( RouteServerFilter $rsf,  int $up ): RedirectResponse
    {
        $this->authorize( 'checkRsfObject',  [ RouteServerFilter::class, $rsf ]  );
        // Getting the list of all the route server filters for the customer
        $listRsf = RouteServerFilter::where( "customer_id", $rsf->customer_id )->orderBy( 'order_by' )->get();

        // Getting the index of the requested route server filter within the list
        $index = $listRsf->search( function ($value, $key) use ( $rsf ) {
            return $value->id === $rsf->id;
        });

        // Adding +1 (moving up) or -1 (moving down) to the index of the route serve filter
        $newIndex = $up ? $index-1 : $index+1;

        $upText = $up ? "up" : "down";

        // Check if the new index exist in the list
        if( !$listRsf->get( $newIndex ) ) {
            AlertContainer::push( "Not possible to move that route server filter " . $upText , Alert::DANGER );
            return redirect( route( "rs-filter@list", [ "cust" => $rsf->customer_id ] ) );
        }

        // Getting the route server filter object that we will have to switch
        $rsfToMove = $listRsf->get( $newIndex );

        $newOrder = $rsfToMove->order_by;
        $oldOrder = $rsf->order_by;

        // temporary order to avoid unique constrain violation
        $rsfToMove->order_by = 0;
        $rsfToMove->save();

        $rsf->order_by = $newOrder;
        $rsf->save();

        $rsfToMove->order_by = $oldOrder;
        $rsfToMove->save();

        AlertContainer::push( 'The route server filter has been moved ' . $upText, Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "cust" => $rsf->customer_id ] ) );
    }

    /**
     * Function to Delete a route serve filter
     *
     * @param RouteServerFilter $rsf
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( RouteServerFilter $rsf ): RedirectResponse
    {
        $this->authorize( 'checkRsfObject',  [ RouteServerFilter::class, $rsf ]  );
        $rsf->delete();

        Log::notice( Auth::getUser()->getUsername()." deleted the route server filter with the ID:" . $rsf->id );
        AlertContainer::push( 'Router server filter deleted.', Alert::SUCCESS );

        return Redirect::to( route( "rs-filter@list", [ "cust" => $rsf->customer_id ] ) );
    }

    /**
     * Return an array of all public peering vlans names where the array key is the vlan id.
     *
     * @param int $custid
     *
     * @return array
     */
    private function getPublicPeeringVLANs( int $custid ): array
    {
        return Vlan::select( [ 'vlan.id AS id', 'vlan.name' ] )
            ->leftJoin( 'vlaninterface AS vli', 'vli.vlanid', 'vlan.id' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->where( 'vi.custid',  $custid )
            ->where( 'vlan.private',  false )
            ->where( 'vlan.peering_manager',  true )
            ->where( 'vli.rsclient',  true )
            ->orderBy( 'vlan.name' )->get()->keyBy( 'id' )->toArray();
    }


}