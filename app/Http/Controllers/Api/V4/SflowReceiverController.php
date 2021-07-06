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

use Illuminate\Http\Response;

use IXP\Models\SflowReceiver;

/**
 * SflowReceiverController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SflowReceiverController extends Controller
{
    /**
     *
     * @return Response
     */
    public function pretagMap(): Response
    {
        $map = [];

        foreach( SflowReceiver::all() as $sr ) {
            foreach( $sr->virtualInterface->macAddresses as $mac ) {
                // looks like there's some crud in the MAC table so filter that:
                if( strlen( $mac->mac ) !== 12 ) {
                    continue;
                }

                $m['virtualinterfaceid']    = $sr->virtual_interface_id;
                $m['mac']                   = $mac->macColonsFormatted();
                $map[]                      = $m;
            }
        }

        return response()
                ->view('api/v4/sflow-receiver/pretagMap', [ 'map' => $map ], 200 )
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     *
     * @return Response
     */
    public function receiversLst(): Response
    {
        $map = [];

        foreach( SflowReceiver::all() as $sr ) {
            $m['virtualinterfaceid']    = $sr->virtual_interface_id;
            $m['dst_ip']                = $sr->dst_ip;
            $m['dst_port']              = $sr->dst_port;
            $map[]                      = $m;
        }

        return response()
                ->view( 'api/v4/sflow-receiver/receiversLst', [ 'map' => $map ], 200 )
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     *
     * @param string|null $format
     *
     * @return Response
     *
     * @throws
     */
    public function getReceiverList( string $format = null ): Response
    {
        $map = [];

        foreach( SflowReceiver::all() as $sr ) {
            $m['virtualinterfaceid'] = $sr->virtual_interface_id;
            $m['dst_ip']             = $sr->dst_ip;
            $m['dst_port']           = $sr->dst_port;
            $macs = [];
            foreach( $sr->virtualInterface->macAddresses as $mac ) {
                $macs[] = $mac->macColonsFormatted();
            }
            $m['macaddresses']['learned'] = $macs;
            $macs = [];
            foreach( $sr->virtualInterface->vlanInterfaces as $vli ) {
                foreach( $vli->layer2addresses as $mac ) {
                    $macs[] = $mac->macFormatted( ':' );
                }
            }
            $m['macaddresses']['configured'] = $macs;
            $map[] = $m;
        }

        $output['receiver_list'] = $map;

        return $this->structuredResponse( $output, $format );
    }

    /**
     * Generate a formatted output version of the given structure.
     *
     * This takes two arguments: the array structure and the output format.
     *
     * @param array     $array
     * @param string    $format
     *
     * @return Response
     *
     * @throws
     */
    private function structuredResponse( array $array, string $format ): Response
    {
        $output         = null;
        $contenttype    = 'text/plain; charset=utf-8';
        $httpresponse   = 200;

        $array['timestamp']             = now()->format( 'Y-m-d\TH:i:s\Z' );
        $array['ixpmanager_version']    = APPLICATION_VERSION;

        switch ( $format ) {
            case 'yaml':
                $output = yaml_emit ( $array, YAML_UTF8_ENCODING );
                break;
            case 'json':
                $output = json_encode($array,
                        JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
                $contenttype = 'application/json';
                break;
        }

        if ( !$output ) {
            $httpresponse = 200;
        }

        return response( $output, $httpresponse )->header('Content-Type', $contenttype );
    }
}