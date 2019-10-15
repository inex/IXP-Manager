<?php

namespace IXP\Http\Controllers;

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

use Auth, D2EM, Log, Redirect;

use Former;
use Illuminate\View\View;


use Entities\{
    Customer            as CustomerEntity,
    RouteServerFilter   as RouteServerFilterEntity,
    Vlan                as VlanEntity
};


use Illuminate\Http\{
    RedirectResponse,
};

use IXP\Http\Requests\RouteServerFilter\{
    CheckPrivsCustAdmin,
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
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RsFilterController extends Controller
{

    /**
     * Display the list of Route Server Filter
     *
     * @param CheckPrivsCustAdmin $request
     *
     * @return  View
     */
    public function list( CheckPrivsCustAdmin $request ): View
    {
        return view( 'rs-filter/list' )->with([
            "rsFilters"         => D2EM::getRepository( RouteServerFilterEntity::class )->findBy( [ "customer" => $request->c->getId() ], [ 'order_by' => 'ASC'] ),
            "c"                 => $request->c
        ]);
    }

    /**
     * Allow to add index/value to an existing array without reindexing the array
     *
     * @param array $array
     * @param $index
     * @param string $value
     *
     * @return  array
     */
    private function addValueToArray( array $array, $index, string $value )
    {
        $arr = array_reverse( $array, true );
        $arr[ $index ] = $value;
        $arr = array_reverse( $arr, true );

        return $arr;
    }

    /**
     * Allow to display the form to create a route server filter
     *
     * @param CheckPrivsCustAdmin $request
     *
     * @return  View
     */
    public function add( CheckPrivsCustAdmin $request ): View
    {
        $vlanid     = $request->old( 'vlan_id',         "Null" );
        $protocol   = $request->old( 'protocol',        4      );

        Former::populate( [
            'vlan_id'               => $vlanid,
            'protocol'              => $protocol,
            'action_advertise'      => $request->old( 'action_advertise',   "Null"  ),
            'action_receive'        => $request->old( 'action_receive',     "Null"  ),
        ] );

        $vlanid     = $request->old( 'vlan_id',         null );

        return view( 'rs-filter/edit' )->with( [
            'rsf'       => false,
            'c'         => $request->c,
            'vlans'     => $this->addValueToArray( D2EM::getRepository( VlanEntity::class )->getPublicPeeringManagerAsArray( $request->c->getId() ), null, "All LANs" ),
            'peers'     => D2EM::getRepository( CustomerEntity::class )->getByVlanAndProtocol( $vlanid , $protocol ),
        ] );
    }

    /**
     * Allow to display the form to create/edit a route server filter
     *
     * @param CheckPrivsCustAdmin   $request
     *
     * @return  View
     */
    public function edit( CheckPrivsCustAdmin $request ): View
    {
        $vlanid     = $request->old( 'vlan_id', $request->rsf->getVlan() ? $request->rsf->getVlan()->getId() : "Null" );
        $protocol   = $request->old( 'protocol',        $request->rsf->getProtocol() );

        Former::populate( [
            'vlan_id'               => $vlanid,
            'protocol'              => $protocol,
            'peer_id'               => $request->old( 'peer_id',            $request->rsf->getPeer()->getId() ),
            'prefix'                => $request->old( 'prefix',             $request->rsf->getPrefix() ),
            'action_advertise'      => $request->old( 'action_advertise',   $request->rsf->getActionReceive() ),
            'action_receive'        => $request->old( 'action_receive',     $request->rsf->getActionAdvertise() ),
        ] );

        $vlanid     = $request->old( 'vlan_id',         null );

        return view( 'rs-filter/edit' )->with( [
            'rsf'       => $request->rsf,
            'c'         => $request->c,
            'vlans'     => $this->addValueToArray( D2EM::getRepository( VlanEntity::class )->getPublicPeeringManagerAsArray( $request->c->getId() ), null, "All LANs" ),
            'peers'     => D2EM::getRepository( CustomerEntity::class )->getByVlanAndProtocol( $vlanid , $protocol ),
        ] );
    }

    /**
     * Function to store A Route Server Filter object
     *
     * @param Store $request
     *
     * @return redirect
     *
     * @throws
     */
    public function store( Store $request )
    {
        // If we add
        if( !$request->input( 'id' ) ) {
            $request->rsf->setCustomer( $request->c );
            $request->rsf->setEnabled( true );
            $request->rsf->setOrderBy( D2EM::getRepository( RouteServerFilterEntity::class )->getNextOrderByForCustomer( $request->c->getId() ) );
        }

        $request->rsf->setPeer( D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'peer_id' ) ) );
        $request->rsf->setVlan( $request->input( 'vlan_id' ) ? D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlan_id' ) ) : null );
        $request->rsf->setPrefix(   $request->input( 'prefix'   ) );
        $request->rsf->setProtocol( $request->input( 'protocol' ) );
        $request->rsf->setActionAdvertise(  $request->input( 'action_advertise' ) );
        $request->rsf->setActionReceive(    $request->input( 'action_receive'   ) );
        $request->rsf->setLive( "" );

        D2EM::flush();

        $action = $request->input( 'id' ) ? 'edited' : 'added';

        Log::notice( Auth::user()->getUsername() . ' ' . $action . ' a router server filter with ID ' . $request->rsf->getId() );

        AlertContainer::push( "Route Server Filter " . $action, Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "custid" => $request->rsf->getCustomer()->getId() ] )  );
    }

    /**
     * Display the details of a Route Server Filter
     *
     * @param CheckPrivsCustAdmin $request
     *
     * @return View
     */
    public function view( CheckPrivsCustAdmin $request ): View
    {
        return view( 'rs-filter/view' )->with( [
            'rsf'   => $request->rsf
        ] );
    }

    /**
     * Enable or disable a router server filter
     *
     * @param CheckPrivsCustAdmin $request
     * @param int $id
     * @param int $enable
     *
     * @return RedirectResponse
     *
     */
    public function toggleEnable( CheckPrivsCustAdmin $request, int $id, int $enable ): RedirectResponse
    {
        $status = $enable ? 'enable' : 'disable';

        $request->rsf->setEnabled( ( bool )$enable );

        D2EM::flush();

        AlertContainer::push( 'The route server filter has been '.$status, Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "custid" => $request->rsf->getCustomer()->getId() ] ) );
    }


    /**
     * Change the order by of a route server filter (up/down)
     *
     * @param CheckPrivsCustAdmin $request
     * @param int $id
     * @param int $up If true move up if false move down
     *
     * @return RedirectResponse
     *
     */
    public function changeOrderBy( CheckPrivsCustAdmin $request, int $id,  int $up ): RedirectResponse
    {
        // Getting the list of all the route server filters for the customer
        $listRsf = D2EM::getRepository( RouteServerFilterEntity::class )->findBy( [ "customer" => $request->c->getId() ], [ 'order_by' => 'ASC'] );

        // Getting the index of the requested route server filter within the list
        $index = array_search( $request->rsf, $listRsf);

        // Adding +1 (moving up) or -1 (moveing down) to the index of the route serve filter
        $newIndex = $up ? $index - 1 : $index + 1;

        $upText = $up ? "up" : "down";

        // Check if the new index exist in the list
        if( !array_key_exists( $newIndex, $listRsf ) ) {
            AlertContainer::push( "Not possible to move that route server filter " . $upText , Alert::DANGER );
            return redirect( route( "rs-filter@list", [ "custid" => $request->rsf->getCustomer()->getId() ] ) );
        }

        // Getting the route server filter object that we will have switch
        /** @var RouteServerFilterEntity $rsfToMove  */
        $rsfToMove = $listRsf[ $newIndex ];

        $newOrder = $rsfToMove->getOrderBy();
        $oldOrder = $request->rsf->getOrderBy();

        // temporary order to avoid unique constrain violation
        $rsfToMove->setOrderBy( 0 );
        D2EM::flush();

        $request->rsf->setOrderBy( $newOrder );
        D2EM::flush();

        $rsfToMove->setOrderBy( $oldOrder );
        D2EM::flush();

        AlertContainer::push( 'The route server filter has been moved ' . $upText, Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "custid" => $request->rsf->getCustomer()->getId() ] ) );
    }

    /**
     * Function to Delete a route serve filter
     *
     * @param CheckPrivsCustAdmin $request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( CheckPrivsCustAdmin $request )
    {
        D2EM::remove( $request->rsf );
        D2EM::flush();

        Log::notice( Auth::getUser()->getUsername()." deleted the route server filter with the ID:" . $request->input( "id" ) );
        AlertContainer::push( 'Router server filter deleted.', Alert::SUCCESS );

        return Redirect::to( route( "rs-filter@list", [ "custid" => $request->c->getId() ] ) );
    }
}