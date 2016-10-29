<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Carbon\Carbon;

/**
 * Controller: Public controller for publically accessable information
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PublicController extends IXP_Controller_Action
{

    /**
     * Function to export details of members in a flexable way
     *
     * Called as: https://www.example.com/ixp/public/member-details
     *
     * You can tack on additional options:
     *
     * * format/json (default HTML)
     * * template/xxx (default member-details.phtml)
     *
     * If a template is specified, it'll use public/member-details/xxx after stripping
     * all but [a-zA-Z0-9-_] from the given template
     *
     */
    public function memberDetailsAction()
    {
        $this->view->customers = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive();

        if( strtolower( $this->getParam( 'format', '0' ) ) == 'json' )
            $this->getResponse()->setHeader( 'Content-Type', 'application/json; charset=utf-8' );

        if( $this->getParam( 'template', false ) )
            $this->_helper->viewRenderer( 'member-details/' . preg_replace( '/[^0-9a-zA-Z-_]/', '', $this->getParam( 'template' ) ) );

    }


    /**
     * Function to export max traffic stats by month (past six) as JSON
     *
     */
    public function ajaxOverallStatsByMonthAction()
    {
        $this->getResponse()->setHeader( 'Content-Type', 'application/json; charset=utf-8' );

        if( $json = $this->getD2Cache()->fetch( 'public_overall_stats_by_month' ) ) {
            echo $json;
            return;
        }

        $mrtg = new IXP_Mrtg( $this->getD2R( '\\Entities\\IXP' )->getDefault()->getMrtgPath() . '/ixp_peering-aggregate-bits.log' );
        $mrtg = $mrtg->getArray();
        $data = [];

        $start   = Carbon::now()->subMonths(5)->startOfMonth();
        $startTs = $start->timestamp;

        $i = 0;
        while( $start->lt(Carbon::now()) ) {
            $data[$i]['start'  ] = $start->copy();
            $data[$i]['startTs'] = $start->timestamp;
            $data[$i]['end']     = $start->endOfMonth()->copy();
            $data[$i]['endTs']   = $start->endOfMonth()->timestamp;
            $data[$i]['max']     = 0;

            $start->startOfMonth()->addMonth();
            $i++;
        }

        $endTs = $data[$i-1]['endTs'];

        foreach( $mrtg as $m ) {
            if( count($m) != 5 ) {
                continue;
            }

            if( $m[0] < $startTs || $m[0] > $endTs ) {
                continue;
            }

            foreach( $data as $i => $d ) {
                if( $m[0] >= $d['startTs'] && $m[0] <= $d['endTs'] ) {
                    if( $m[3] > $data[$i]['max'] ) {
                        $data[$i]['max'] = $m[3];
                    }
                    if( $m[4] > $data[$i]['max'] ) {
                        $data[$i]['max'] = $m[4];
                    }

                    break;
                }
            }
        }

        // scale to bps
        foreach( $data as $i => $d ) {
            $data[$i]['start'] = $data[$i]['start']->format('Y-m-d') . 'T' . $data[$i]['start']->format('H:i:s') . 'Z';
            $data[$i]['end']   = $data[$i]['end']->format('Y-m-d')   . 'T' . $data[$i]['end']->format('H:i:s')   . 'Z';
            $data[$i]['max'] *= 8;
        }

        $json = json_encode($data,JSON_PRETTY_PRINT);
        $this->getD2Cache()->save( 'public_overall_stats_by_month', $json, 7200 );
        echo $json;
    }
}
