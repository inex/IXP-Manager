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

use Illuminate\Http\Request;

class SflowReceiverController extends Controller {

    /**
     *
     * @return Response
     */
    public function pretagMap( Request $request )
    {
        $map = [];

        foreach( d2r('SflowReceiver')->findAll() as $sr ) {
            foreach( $sr->getVirtualInterface()->getMACAddresses() as $mac ) {

                // looks like there's some crud in the MAC table so filter that:
                if( strlen( $mac->getMac() ) != 12 ) {
                    continue;
                }

                $m['virtualinterfaceid'] = $sr->getVirtualInterface()->getId();
                $m['mac']             = $mac->getMacFormattedWithColons();
                $map[] = $m;
            }
        }

        return response()
                ->view('api/v4/sflow-receiver/pretagMap', ['map' => $map], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     *
     * @return Response
     */
    public function receiversLst( Request $request )
    {
        $map = [];

        foreach( d2r('SflowReceiver')->findAll() as $sr ) {
            $m['virtualinterfaceid'] = $sr->getVirtualInterface()->getId();
            $m['dst_ip']          = $sr->getDstIp();
            $m['dst_port']        = $sr->getDstPort();
            $map[] = $m;
        }

        return response()
                ->view('api/v4/sflow-receiver/receiversLst', ['map' => $map], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }

}
