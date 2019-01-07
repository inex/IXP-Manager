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

use Auth, D2EM;

use Entities\{
    RSPrefix            as RSPrefixEntity,
    Customer            as CustomerEntity,
    User                as UserEntity,
    VirtualInterface    as VirtualInterfaceEntity,
    VlanInterface       as VlanInterfaceEntity
};

use Illuminate\Http\Request;
use Illuminate\View\View;


/**
 * Route Server Prefixes Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RsPrefixesController extends Controller {

    /**
     * Display all the RsPrefixes
     *
     * @return  View
     */
    public function list(): View {

        return view( 'rs-prefixes/list' )->with([
            'types'                 => RSPrefixEntity::$SUMMARY_TYPES_FNS,
            'rsRouteTypes'          => array_keys( RSPrefixEntity::$ROUTES_TYPES_FNS ),
            'cPrefixes'             => D2EM::getRepository( RSPrefixEntity::class )->aggregateRouteSummaries()
        ]);
    }

    /**
     * Display all the RsPrefixes for a Customer
     *
     * Optional get parameters:
     *
     * type       type of Rs prefix (adv_nacc|adv_acc|nadv_acc)
     * protocol   protocol selected (4/6)
     *
     * @param   Request $r
     * @param   int     $cid customer ID
     * @return  View
     */
    public function view( Request $r, $cid ) : View {
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $cid ) ) ) {
            abort(404);
        }

        if( !in_array( $type = $r->input( 'type', false ), array_merge( [ false ], array_keys( RSPrefixEntity::$SUMMARY_TYPES_FNS ) ) ) ) {
            abort( 404 );
        }

        if( !in_array( $protocol = $r->input( 'protocol' ), [ null, 4, 6 ] ) ) {
            abort( 404 );
        }

        // does the customer have VLAN interfaces that filtering is disabled on?
        $totalVlanInts = 0;
        $filteredVlanInts = 0;

        foreach( $c->getVirtualInterfaces() as $vi ) {
            /** @var VirtualInterfaceEntity $vi */
            foreach( $vi->getVlanInterfaces() as $vli ) {
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
            'protocol'                  => $protocol ?? false,
            'type'                      => $type,
            'rsRouteTypes'              => array_keys( RSPrefixEntity::$ROUTES_TYPES_FNS ),
            'c'                         => $c,
            'aggRoutes'                 => D2EM::getRepository( RSPrefixEntity::class )->aggregateRoutes( $c->getId(), $protocol )
        ]);
    }
}
