<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Illuminate\Http\{
    JsonResponse,
    Response
};

use Illuminate\Support\Facades\View as FacadeView;

use IXP\Models\{
    Aggregators\VlanAggregator,
    Vlan
};

use IXP\Utils\IpAddress;

/**
 * DnsController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DnsController extends Controller
{
    /**
     * API call to generate DNS ARPA records in a given format
     *
     * @param Vlan  $vlan       Vlan to generate the ARPA entries for (vlan.id)
     * @param int   $protocol   Protocol to generate the ARPA entries for
     *
     * @return JsonResponse
     */
    public function arpa( Vlan $vlan, int $protocol ): JsonResponse
    {
        return response()->json( $this->loadRecords( $vlan, $protocol ) );
    }

    /**
     * Validate request details and load records
     *
     * @param Vlan $vlan    Vlan to generate the ARPA entries for (vlan.id)
     * @param int $protocol Protocol to generate the ARPA entries for
     *
     * @return array
     *
     * @throws
     */
    private function loadRecords( Vlan $vlan, int $protocol ): array
    {
        if( !in_array( $protocol,[ 4,6 ] ) ) {
            abort( 404, "Unknown protocol" );
        }

        return array_map( function( $e ) use ( $protocol ) {
                $e['arpa'] = IpAddress::toArpa( $e['address'], $protocol );
                return $e;
            },
            VlanAggregator::arpaDetails( $vlan, $protocol )
        );
    }

    /**
     * API call to generate DNS ARPA records in a given format
     *
     * @param Vlan      $vlan       Vlan to generate the ARPA entries for (vlan.id)
     * @param int       $protocol   Protocol to generate the ARPA entries for
     * @param string    $template   The template to use to generate the response
     *
     * @return Response
     */
    public function arpaTemplated( Vlan $vlan, int $protocol, string $template ): Response
    {
        $tmpl = sprintf('api/v4/dns/%s', preg_replace('/[^a-z0-9\-]/', '', strtolower( $template ) ) );

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        return response()
            ->view( $tmpl,
                [
                    'arpa'      => $this->loadRecords( $vlan , $protocol),
                    'vlan'      => $vlan,
                    'protocol'  => $protocol
                ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }
}