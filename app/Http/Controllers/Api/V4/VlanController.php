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
    Vlan as VlanEntity,
    VlanInterface as VlanInterfaceEntity
};

use Illuminate\Http\{Request,Response};
use Illuminate\Support\Facades\View as FacadeView;


/**
 * Vlan API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanController extends Controller
{


    /**
     *
     *
     */
    public function smokepingTargets( Request $request, int $vlanid, int $protocol, string $template = null ): Response {
        /** @var VlanEntity $v */
        if( !( $v =  D2EM::getRepository( VlanEntity::class )->find( $vlanid ) ) ){
            return abort( 404, 'Unknown VLAN' );
        }

        if( !in_array( $protocol, [ 4, 6 ] ) ) {
            return abort( 404, 'Unknown protocol' );
        }

        if( $template === null ) {
            $tmpl = 'api/v4/vlan/smokeping/default';
        } else {
            $tmpl = sprintf( 'api/v4/vlan/smokeping/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        if( $request->input( 'probe', false ) ) {
            $probe = $request->input( 'probe' );
        } else {
            $probe = 'FPing' . ( $protocol == 4 ? '' : '6' );
        }

        return response()
            ->view( $tmpl, [
                    'vlan'     => $v,
                    'vlis'     => D2EM::getRepository( VlanInterfaceEntity::class )->getForProto( $v, $protocol, false ),
                    'probe'    => $probe,
                    'level'    => $request->input( 'level', '+++' ),
                    'protocol' => $protocol
                ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }
}
