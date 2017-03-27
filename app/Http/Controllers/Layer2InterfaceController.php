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

Use D2EM;

use Illuminate\Http\Request;
use Illuminate\View\View;

use Entities\VlanInterface;

/**
 * Layer2Interface Controller
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Layer2InterfaceController extends Controller
{
    /**
     * Display all the layer2 addresse for a VlanInterface
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @parama  int $id ID if the VlanInterface
     * @return  view
     */
    public function index( int $id ): View{
        $vli = false;

        if( !( $vli = D2EM::getRepository(VlanInterface::class)->find( $id ) ) ) {
            return abort( '404' );
        }

        return view( 'vlan-interface/index' )->with([
            'vli'       => $vli
        ]);
    }

    /**
     * store a mac address
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @parama  int $id ID if the VlanInterface
     * @return  view
     */
    public function store( $request ): View{
        $id = $request->input( 'id' );
        $mac = $request->input( 'mac' );

        dd($mac);

        if( !( $vli = D2EM::getRepository(VlanInterface::class)->find( $id ) ) ) {
            return abort( '404' );
        }

        return view( 'vlan-interface/index' )->with([
            'vli'       => $vli
        ]);
    }
}
