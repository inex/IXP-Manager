<?php
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

namespace IXP\Http\Controllers;

use D2EM;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

use Entities\{
    VlanInterface as VlanInterfaceEntity,
    Layer2Address as Layer2AddressEntity,
    OUI as OUIEntity,
    Vlan as VlanEntity
};

/**
 * Layer2Address Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Layer2AddressController extends Controller
{

    /**
     * Display all the layer2addresses for a VlanInterface
     *
     * @param  int $vliid ID if the VlanInterface
     * @return  View
     */
    public function index( int $vliid ): View {
        if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $vliid ) ) ) {
            return abort( '404' );
        }

        return view( 'layer2-address/vlan-interface' )->with([
            'vli'       => $vli
        ]);
    }

    /**
     * Display known MAC addresses
     *
     * @param  int $vlid display only the mac addresses of this vlan
     * @return  View
     */
    public function list( int $vlid = null) : View {
        $vlan = false;
        if( $vlid != null and !( $vlan = D2EM::getRepository( VlanEntity::class )->find( $vlid ) ) ) {
            abort( 404 );
        }

        // get all layer2addresses:
        $l2as    = D2EM::getRepository( Layer2AddressEntity::class )->getAll( $vlid );
        // and turn the OUI/MAC into a manufacturer:
        $listOui = D2EM::getRepository( OUIEntity::class )->getForLayer2Addresses( $l2as );

        return view( 'layer2-address/list' )->with([
            'list'              => $l2as,
            'listOui'           => $listOui,
            'Vlans'             => D2EM::getRepository( VlanEntity::class )->findAll(),
            'Vlan'              => $vlan
        ]);
    }
}
