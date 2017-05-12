<?php

namespace IXP\Http\Controllers\Api\V4;


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

use D2EM;

use Entities\{
    Vlan as VlanEntity
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * VlanController API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanController extends Controller
{


    /**
     * Get the IPv4 or IPv6 list for a vlan
     *
     * @params  $request instance of the current HTTP request
     * @params  $id Vlan id
     * @return  JSON array of IPvX
     */
    public function getIPvAddress( Request $request, int $id ) : JsonResponse{

        /** @var VlanEntity $vl */
        if( !( $vl =  D2EM::getRepository( VlanEntity::class )->find( $id ) ) ){
            return abort( 404 );
        }

        $ipvList = D2EM::getRepository( VlanEntity::class )->getIPvAddress( $vl->getId() , $request->input( 'ipType' ), $request->input( 'vliid' ) );

        return response()->json( ['ipvList' => $ipvList] );
    }
}
