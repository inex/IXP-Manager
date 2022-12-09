<?php

namespace IXP\Http\Controllers\Utils;

/*
 * Copyright (C) 2009 - 2022 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth;

use Former\Facades\Former;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use IXP\Http\Controllers\Controller;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * IX-F Member Compare Controller
 *
 * Routes to access this controller are dependent on the controller being enabled.
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Customer
 * @copyright  Copyright (C) 2009 - 2022 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IxfCompareController extends Controller
{
    public function index(): View
    {
        Former::populate([
            'sourcea_dd'                => request()->old( 'sourcea_dd' ),
            'sourcea_tf'                => request()->old( 'sourcea_tf' ),
            'sourceb_dd'                => request()->old( 'sourcea_dd' ),
            'sourceb_tf'                => request()->old( 'sourcea_tf' ),
        ]);


        return view( 'utils/ixf-compare' )->with([
            'sources' => array_combine( array_keys( config('ixp_fe.ixfsources')), array_keys( config('ixp_fe.ixfsources')) ),
            'results' => false,
        ]);
    }


    public function compare( Request $r ): View|RedirectResponse
    {
        $sources = config('ixp_fe.ixfsources');

        if( !isset( $sources[ $r->sourcea_dd ] ) || !isset( $sources[ $r->sourceb_dd ] ) || $r->sourcea_dd === $r->sourceb_dd ) {
            AlertContainer::push( 'Bad sources for IX-F data.', Alert::DANGER );
            return redirect()->back();
        }

        // get API data
        $a = Cache::remember( 'ixf_compare_' . $r->sourcea_dd, 3600, function () use ( $r, $sources ) { return json_decode( file_get_contents( $sources[ $r->sourcea_dd ][ 'url' ] ) ); } );
        $b = Cache::remember( 'ixf_compare_' . $r->sourceb_dd, 3600, function () use ( $r, $sources ) { return json_decode( file_get_contents( $sources[ $r->sourceb_dd ][ 'url' ] ) ); } );

        $netsa = $this->getNetworks( $a, $sources[ $r->sourcea_dd ][ 'ixid' ] );
        $netsb = $this->getNetworks( $b, $sources[ $r->sourceb_dd ][ 'ixid' ] );

        return view( 'utils/ixf-compare' )->with([
            'sources' => array_combine( array_keys( config('ixp_fe.ixfsources')), array_keys( config('ixp_fe.ixfsources')) ),
            'results' => $this->compareIXs( $netsa, $netsb ),
        ]);
    }

    private function getNetworks( object $ixf, int $ixid ): array
    {
        $nets = [];

        foreach( $ixf->member_list as $net ) {
            foreach( $net->connection_list as $cl ) {
                if( $cl->ixp_id != $ixid ) {
                    continue;
                }

                if( !isset( $nets[ $net->asnum ] ) ) {
                    $nets[ $net->asnum ]['name']  = $net->name;
                    $nets[ $net->asnum ]['speed'] = 0;
                }

                foreach( $cl->if_list as $ifl ) {
                    $nets[ $net->asnum ]['speed'] += $ifl->if_speed;
                }
            }
        }

        return $nets;
    }

    private function compareIXs( array $a, array $b ): array
    {
        $shared = [];

        foreach( $a as $as => $details ) {
            if( isset( $b[ $as ] ) ) {
                $shared[ $as ] = [
                    'name'   => $details['name'],
                    'aspeed' => $details['speed'],
                    'bspeed' => $b[$as]['speed'],
                ];
                unset( $a[$as] );
                unset( $b[$as] );
            }
        }

        ksort( $a, SORT_NUMERIC );
        ksort( $b, SORT_NUMERIC );
        ksort( $shared, SORT_NUMERIC );

        return [ 'shared' => $shared, 'aonly' => $a, 'bonly' => $b ];
    }

}