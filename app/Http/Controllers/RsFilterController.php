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
    Request

};

use IXP\Http\Requests\StoreRouteServerFilter;

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
     * @param Request $request
     * @param int $custid
     *
     * @return  View
     */
    public function list( Request $request,  int $custid ): View
    {
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $custid ) ) ) {
            abort( 404, "Unknown customer" );
        }

        if( $c->getId() != $request->user()->getCustomer->getId() ){
            abort( 403, "Access forbidden" );
        }

        return view( 'rs-filter/list' )->with([
            "rsFilters"          => D2EM::getRepository( RouteServerFilterEntity::class )->findBy( [ "customer" => $c->getId() ], [ 'order_by' => 'ASC'] )
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
     * Allow to display the form to create/edit a route server filter
     *
     * @param Request   $request
     * @param int       $id       ID of the patch panel
     *
     * @return  View
     */
    public function edit( Request $request, int $id = null ): View
    {
        if( !$request->user()->getCustomer()->isRouteServerClient() ){
            return Redirect::to( "");
        }

        /** @var RouteServerFilterEntity $rsf */
        $rsf = false;
        if( $id ) {
            if( !( $rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( $id ) ) ) {
                abort( 404 );
            }

            if( $rsf->getCustomer()->getId() != $request->user()->getCustomer->getId() ){
                abort( 403, "Access forbidden" );
            }

            $vlanid     = $request->old( 'vlan_id',     $rsf->getVlan()->getId()    );
            $protocol   = $request->old( 'protocol',    $rsf->getProtocol()         );

            Former::populate( [
                'vlan_id'               => $vlanid,
                'protocol'              => $protocol,
                'peer_id'               => $request->old( 'peer_id',            $rsf->getPeer()->getId() ),
                'customer_id'           => $request->old( 'customer_id',        $rsf->getCustomer()->getId() ),
                'prefix'                => $request->old( 'prefix',             $rsf->getPrefix() ),
                'action_advertise'      => $request->old( 'action_advertise',   $rsf->getActionReceive() ),
                'action_receive'        => $request->old( 'action_receive',     $rsf->getActionAdvertise() ),
            ] );

            $peers = D2EM::getRepository( CustomerEntity::class )->getByVlanAndProtocol( $vlanid, $protocol );

        } else {

            $vlanid     = $request->old( 'vlan_id',         "Null" );
            $protocol   = $request->old( 'protocol',        4      );

            Former::populate( [
                'vlan_id'               => $vlanid,
                'protocol'              => $protocol,
                'action_advertise'      => $request->old( 'action_advertise',   "Null"    ),
                'action_receive'        => $request->old( 'action_receive',     "Null"    ),

            ] );

            $vlanid     = $request->old( 'vlan_id',         null );

            $peers = D2EM::getRepository( CustomerEntity::class )->getByVlanAndProtocol( $vlanid , $protocol );
        }

        return view( 'rs-filter/edit' )->with( [
            'rsf'       => $rsf,
            'vlans'     => $this->addValueToArray( D2EM::getRepository( VlanEntity::class )->getPublicPeeringManagerAsArray( $request->user()->getCustomer()->getId() ), null, "All LANs" ),
            'peers'     => $peers,
        ] );
    }

    /**
     * Function to store A Route Server Filter object
     *
     * @param StoreRouteServerFilter $request
     *
     * @return redirect
     *
     * @throws
     */
    public function store( StoreRouteServerFilter $request )
    {
        $isEdit = $request->input( 'id' ) ? true : false;

        /** @var RouteServerFilterEntity $rsf */
        if( $isEdit && $rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$rsf ) {
                abort(404, 'Router Server Filter not found' );
            }

            if( $rsf->getCustomer()->getId() != $request->user()->getCustomer->getId() ){
                abort( 403, "Access forbidden" );
            }
        } else {
            $rsf = new RouteServerFilterEntity;
            D2EM::persist( $rsf );
        }

        $rsf->setCustomer( $request->user()->getCustomer());
        $rsf->setPeer( D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'peer_id' ) ) );
        $rsf->setVlan( $request->input( 'vlan_id' ) ? D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlan_id' ) ) : null );
        $rsf->setPrefix( $request->input( 'prefix' ) );
        $rsf->setProtocol( $request->input( 'protocol' ) );
        $rsf->setActionAdvertise( $request->input( 'action_advertise' ) );
        $rsf->setActionReceive( $request->input( 'action_advertise' ));
        $rsf->setEnabled( true );
        $rsf->setOrderBy( D2EM::getRepository( RouteServerFilterEntity::class )->getNextOrderByForCustomer( $request->user()->getCustomer()->getId() ) );
        $rsf->setLive( "test" );

        D2EM::flush();

        Log::notice( Auth::user()->getUsername() . ' ' . $isEdit ? 'edited' : 'added' . ' a router server filter with ID ' . $rsf->getId() );

        AlertContainer::push( "Route Server Filter " . $isEdit ? 'edited.' : 'added.', Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "custid" => $rsf->getCustomer()->getId() ] )  );
    }

    /**
     * Display the details of a Route Server Filter
     *
     * @param  int    $id        Route Server Filter that need to be displayed
     * @return View
     */
    public function view( int $id ): View
    {
        /** @var RouteServerFilterEntity $rsf */
        if( !( $rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( $id ) ) ) {
            abort(404 , 'Unknown router' );
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'rs-filter/view' )->with([
            'rsf'                => $rsf
        ]);
    }

    /**
     * Enable or disable a router server filter
     *
     * @param int $id
     * @param int $enable
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function toggleEnable( int $id, int $enable ): RedirectResponse
    {
        /** @var RouteServerFilterEntity $rsf  */
        if( !( $rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $status = $enable ? 'enable' : 'disable';

        $rsf->setEnabled( ( bool )$enable );

        D2EM::flush();

        AlertContainer::push( 'The route server filter has been '.$status, Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "custid" => $rsf->getCustomer()->getId() ] ) );
    }


    /**
     * Change the order by of a route server filter (up/down)
     *
     * @param Request $request
     * @param int $id
     * @param int $up
     * @return RedirectResponse
     *
     * @throws
     */
    public function changeOrderBy( Request $request, int $id, int $up ): RedirectResponse
    {
        /** @var RouteServerFilterEntity $rsf  */
        if( !( $rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $listRsf = D2EM::getRepository( RouteServerFilterEntity::class )->findBy( [ "customer" => $request->user()->getCustomer()->getId() ], [ 'order_by' => 'ASC'] );

        $index = array_search( $rsf, $listRsf);

        $newIndex = $up ? $index - 1 : $index + 1;

        $upText = $up ? "up" : "down";

        if( !array_key_exists( $newIndex, $listRsf ) ) {
            AlertContainer::push( "Not possible to move that route server filter " . $upText , Alert::DANGER );
            return redirect( route( "rs-filter@list", [ "custid" => $rsf->getCustomer()->getId() ] ) );
        }

        /** @var RouteServerFilterEntity $rsfToMove  */
        $rsfToMove = $listRsf[ $newIndex ];

        $newOrder = $rsfToMove->getOrderBy();
        $oldOrder = $rsf->getOrderBy();

        // temporary order
        $rsfToMove->setOrderBy( 0 );
        D2EM::flush();

        $rsf->setOrderBy( $newOrder );
        D2EM::flush();

        $rsfToMove->setOrderBy( $oldOrder );
        D2EM::flush();

        AlertContainer::push( 'The route server filter has been moved ' . $upText, Alert::SUCCESS );

        return redirect( route( "rs-filter@list", [ "custid" => $rsf->getCustomer()->getId() ] ) );
    }

    /**
     * Function to Delete a route serve filter
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request )
    {
        /** @var RouteServerFilterEntity $rsf */
        if( !( $rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( $request->input( "id" ) ) ) ) {
            abort(404);
        }

        if( $rsf->getCustomer()->getId() != $request->user()->getCustomer->getId() ){
            abort( 403, "Access forbidden" );
        }

        D2EM::remove( $rsf );
        D2EM::flush();

        Log::notice( Auth::getUser()->getUsername()." deleted the route server filter with the ID:" . $request->input( "id" ) );
        AlertContainer::push( 'Router server filter deleted.', Alert::SUCCESS );

        return Redirect::to( route( "rs-filter@list", [ "custid" => $request->user()->getCustomer()->getId() ] ) );
    }
}