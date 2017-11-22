<?php

namespace IXP\Http\Controllers;

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

use Auth, D2EM;

use Entities\{
    RSPrefix            as RSPrefixEntity,
    Customer            as CustomerEntity,
    User                as UserEntity,
    VirtualInterface    as VirtualInterfaceEntity,
    VlanInterface       as VlanInterfaceEntity
};

use Illuminate\View\View;


/**
 * Route Server Prefixes Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RsPrefixesController extends Controller {

    /**
     * Display all the RsPrefixes
     *
     * @return  View
     */
    public function list( ): View {

        return view( 'rs-prefixes/list' )->with([
            'types'                 => RSPrefixEntity::$SUMMARY_TYPES_FNS,
            'rsRouteTypes'          => array_keys( RSPrefixEntity::$ROUTES_TYPES_FNS ),
            'cust_prefixes'         => D2EM::getRepository( RSPrefixEntity::class )->aggregateRouteSummaries()
        ]);
    }

    /**
     * Display all the RsPrefixes for a Customer in Restricted version for the user type CustUser
     *
     * @param int|null $protocol protocol selected
     * @return  View
     */
    public function viewRestricted( $protocol = null ) {
        if( Auth::getUser()->getPrivs() != UserEntity::AUTH_CUSTADMIN || Auth::getUser()->getPrivs() != UserEntity::AUTH_SUPERUSER ) {
            abort( 403 );
        }

        return $this->view( Auth::getUser()->getCustomer()->getId() , null, $protocol);
    }

    /**
     * Display all the RsPrefixes for a Customer filtered by protocol or for all protocol
     *
     * @param int       $cid        customer ID
     * @param int|null  $protocol   protocol selected
     * @return  View
     */
    public function viewFiltered( $cid, $protocol = null ) {
        return $this->view( $cid, null, $protocol);
    }

    /**
     * Display all the RsPrefixes for a Customer
     *
     * @param int       $cid        customer ID
     * @param string    $type       type of Rs prefix (adv_nacc|adv_acc|nadv_acc)
     * @param int|null $protocol    protocol selected
     * @return  View
     */
    public function view( $cid, $type , $protocol = null ) : View {
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $cid ) ) ) {
            abort(404);
        }

        if( $type ){
            if( !in_array( $type, array_keys( RSPrefixEntity::$SUMMARY_TYPES_FNS ) ) ) {
                abort(404);
            }
        } else{
            $type = false;
        }


        if( !in_array( $protocol, [ 4, 6 ] ) ){
            $protocol = false;
        }

        // does the customer have VLAN interfaces that filtering is disabled on?
        $totalVlanInts = 0;
        $filteredVlanInts = 0;

        foreach( $c->getVirtualInterfaces() as $vi ) {
            /** @var VirtualInterfaceEntity $vi */
            foreach( $vi->getVlanInterfaces() as $vli ) {
                /** @var VlanInterfaceEntity $vli */
                if( $vli->getVlan()->getPrivate() ){
                    continue;
                }

                if( $vli->getIrrdbfilter() ){
                    $filteredVlanInts++;
                }
                $totalVlanInts++;
            }
        }

        return view( 'rs-prefixes/view' )->with([
            'totalVl'                   => $totalVlanInts,
            'filteredVl'                => $filteredVlanInts ,
            'protocol'                  => $protocol,
            'type'                      => $type,
            'rsRouteTypes'              => array_keys( RSPrefixEntity::$ROUTES_TYPES_FNS ),
            'c'                         => $c,
            'aggRoutes'                 => D2EM::getRepository( RSPrefixEntity::class )->aggregateRoutes( $c->getId(), $protocol ? $protocol : null )
        ]);


    }

}
