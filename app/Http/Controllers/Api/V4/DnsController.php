<?php

namespace IXP\Http\Controllers\Api\V4;


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

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View as FacadeView;

use IXP\Utils\IpAddress;

/**
 * DnsController API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DnsController extends Controller {


    /**
     * Validate request details and load records
     *
     * @param int $vlanid Database id of a vlan to generate the ARPA entries for (vlan.id)
     * @param int $protocol Protocol to generate the ARPA entries for
     * @return array
     */
    private function loadRecords( int $vlanid, int $protocol ): array {
        if( !( $vlan = d2r('Vlan')->find($vlanid) ) ) {
            abort( 404, "Unknown VLAN" );
        }

        if( !in_array($protocol,[4,6]) ) {
            abort( 404, "Unknown protocol" );
        }

        return array_map( function( $e ) use ($protocol) { $e['arpa'] = IpAddress::toArpa( $e['address'], $protocol ); return $e; },
            d2r('Vlan')->getArpaDetails( $vlan, $protocol )
        );
    }

    /**
     * API call to generate DNS ARPA records in a given format
     *
     * @param int $vlanid Database id of a vlan to generate the ARPA entries for (vlan.id)
     * @param int $protocol Protocol to generate the ARPA entries for
     * @return Response
     */
    public function arpa( Request $request, int $vlanid, int $protocol ) {
        return response()->json($this->loadRecords($vlanid, $protocol));
    }

    /**
     * API call to generate DNS ARPA records in a given format
     *
     * @param int $vlanid Database id of a vlan to generate the ARPA entries for (vlan.id)
     * @param int $protocol Protocol to generate the ARPA entries for
     * @param string $format The template to use to generate the response
     * @return Response
     */
    public function arpaTemplated( Request $request, int $vlanid, int $protocol, string $template )
    {
        $tmpl = sprintf('api/v4/dns/%s', preg_replace('/[^a-z0-9\-]/', '', strtolower( $template ) ) );

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        return response()
                ->view( $tmpl, [ 'arpa' => $this->loadRecords($vlanid, $protocol), 'vlan' => d2r('Vlan')->find($vlanid), 'protocol' => $protocol ], 200 )
                ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }

}
