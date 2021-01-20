<?php
declare(strict_types=1);

namespace IXP\Http\Controllers\Services;

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

use Auth, D2EM;


use Laravel\Socialite\Facades\Socialite;
use Entities\{
    Router as RouterEntity
};

use ErrorException;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

use IXP\Contracts\LookingGlass as LookingGlassContract;

use IXP\Exceptions\Services\LookingGlass\GeneralException as LookingGlassGeneralException;

use IXP\Http\Controllers\Controller;


/**
 * SAGE Accounting Controller
 *
 * *** INEX INTERNAL TESTING ***
 * *** INEX INTERNAL TESTING ***
 * *** INEX INTERNAL TESTING ***
 * *** INEX INTERNAL TESTING ***
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   LookingGlass
 * @package    IXP\Services\SAGE
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SAGE extends Controller
{

    public function login()
    {

        return Socialite::driver('sage')->redirect();
    }

    /**
     * OAuth with SAGE returns here. It yields:
     *
     * SocialiteProviders\Manager\OAuth2\User {#2666 ▼
     *    +accessTokenResponseBody: array:7 [
     *     "access_token" => "eyJhbGciOiJSUzUxMiIsImtpZCI6IjRLR1RqZmN4TGpOWXdac1BiSjZkZ2Y1Zi1MdG9aSmp2cFR2c0hsUnFGX2s9In0.eyJqdGkiOiI0ZWFlYzMzNS05MTY2LTRmZmEtYTk1My0xYjlmZmU1Nzk5OWIiLCJpYXQi ▶"
     *     "expires_in" => 300
     *     "token_type" => "bearer"
     *     "refresh_token" => "eyJhbGciOiJSUzUxMiIsImtpZCI6IjRLR1RqZmN4TGpOWXdac1BiSjZkZ2Y1Zi1MdG9aSmp2cFR2c0hsUnFGX2s9In0.eyJqdGkiOiI5MTg4MjUzNC02NmEyLTRiYjAtYmY1My1iOThlYjYwMmJhZGIiLCJpYXQi ▶"
     *     "refresh_token_expires_in" => 2678400
     *     "scope" => "full_access"
     *     "requested_by_id" => "110fa7de-12d0-4b2d-9cc8-187feec5548a"
     *    ]
     *    +token: "eyJhbGciOiJSUzUxMiIsImtpZCI6IjRLR1RqZmN4TGpOWXdac1BiSjZkZ2Y1Zi1MdG9aSmp2cFR2c0hsUnFGX2s9In0.eyJqdGkiOiI0ZWFlYzMzNS05MTY2LTRmZmEtYTk1My0xYjlmZmU1Nzk5OWIiLCJpYXQi ▶"
     *    +refreshToken: "eyJhbGciOiJSUzUxMiIsImtpZCI6IjRLR1RqZmN4TGpOWXdac1BiSjZkZ2Y1Zi1MdG9aSmp2cFR2c0hsUnFGX2s9In0.eyJqdGkiOiI5MTg4MjUzNC02NmEyLTRiYjAtYmY1My1iOThlYjYwMmJhZGIiLCJpYXQi ▶"
     *    +expiresIn: 300
     *    +id: "110fa7de12d04b2d9cc8187feec5548a"
     *    +nickname: null
     *    +name: "Sales Solutions"
     *    +email: "sales@opensolutions.ie"
     *    +avatar: null
     *    +user: null
     *    +"first_name": "Sales"
     *    +"last_name": "Solutions"
     *    +"locale": "en-IE"
     * }
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function callback()
    {
        return $this->services();
        return view( 'services/sage/index', [ 'suser' => Socialite::driver('sage')->user() ] );
    }

    public function pull()
    {
        $suser = Socialite::driver('sage')->user();
        $c = new \GuzzleHttp\Client();

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/ledger_accounts?items_per_page=200&visible_in=sales&attributes=nominal_code', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        dd( json_decode( $r->getBody()->getContents() ) );

    }



    public function services()
    {
        $services = [
            [ 'd' => 'INEX LAN1 - First 1Gb Port',      'c' => 'LAN1-1G-FIRST', 'l' => 4510, 'p' => 70.00 ],
            [ 'd' => 'INEX LAN1 - Additional 1Gb Port', 'c' => 'LAN1-1G-ADDNL', 'l' => 4510, 'p' => 56.00 ],

            [ 'd' => 'INEX LAN1 - First 10Gb Port',      'c' => 'LAN1-1G-FIRST', 'l' => 4510, 'p' => 275.00 ],
            [ 'd' => 'INEX LAN1 - Additional 10Gb Port', 'c' => 'LAN1-1G-ADDNL', 'l' => 4510, 'p' => 220.00 ],

            [ 'd' => 'INEX LAN1 - First 100Gb Port',      'c' => 'LAN1-1G-FIRST', 'l' => 4510, 'p' => 1375.00 ],
            [ 'd' => 'INEX LAN1 - Additional 100Gb Port', 'c' => 'LAN1-1G-ADDNL', 'l' => 4510, 'p' => 1100.00 ],
        ];

        $suser = Socialite::driver('sage')->user();


        ///// Rate Type

        $c = new \GuzzleHttp\Client();

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/service_rate_types?items_per_page=200', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        $res = json_decode( $r->getBody()->getContents() );
        $tmp = '$items';
        foreach( $res->$tmp as $r ) {
            if( $r->displayed_as === 'Rate' ) {
                $rid = $r->id;
            }
        }

        ///// Ledgers
        ///
        ///
        $c = new \GuzzleHttp\Client();

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/ledger_accounts?items_per_page=200&visible_in=sales&attributes=nominal_code', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        $ledger_account_ids = [];

        $res = json_decode( $r->getBody()->getContents() );
        $tmp = '$items';
        foreach( $res->$tmp as $r ) {
            $ledger_account_ids[ $r->nominal_code ] = $r->id;
        }

        dd($ledger_account_ids);

        ///
        ///


        $results = [];

        foreach( $services as $s ) {
            $c = new \GuzzleHttp\Client();

            $service = [
                'service' => [
                    'description'               => $s['d'],
                    'item_code'                 => $s['c'],
                    'sales_ledger_account_id'   => $ledger_account_ids[ $s['l'] ],
                    'sales_tax_rate_id'         => 'IE_STANDARD',
                    'sales_rates' => [
                        [
                            'rate_name' => 'Rate',
                            'rate'      => $s['p'],
                            'rate_includes_tax' => false,
                            'service_rate_type_id' => $rid,
                        ]
                    ]
                ]
            ];

            $r = $c->post( 'https://api.accounting.sage.com/v3.1/services', [
                    \GuzzleHttp\RequestOptions::JSON => $service,
                    'headers'                        => [
                        'Authorization' => 'Bearer ' . $suser->token
                    ]
                ]
            );

//            $results[] = [ 'name' => $l['n'], 'id' => json_decode( $r->getBody()->getContents() )->id ];
            dd( json_decode( $r->getBody()->getContents() ) );
        }

        dd( $results );

//
//        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/tax_rates?items_per_page=200', [
//            'headers' => [
//                'Authorization' => 'Bearer ' . $suser->token
//            ]
//        ] );
//
//        dd( json_decode( $r->getBody()->getContents() ) );

    }

    public function ledgers()
    {
        $ledgers = [
            [ 'c' => 4500, 'n' => 'Sales - Membership Fees', ],
            [ 'c' => 4501, 'n' => 'Sales - Associate Fees', ],
            [ 'c' => 4510, 'n' => 'Sales - INEX LAN1 - Ports', ],
            [ 'c' => 4511, 'n' => 'Sales - INEX LAN2 - Ports', ],
            [ 'c' => 4512, 'n' => 'Sales - INEX Cork - Ports', ],
            [ 'c' => 4550, 'n' => 'Sales - Cross Connects', ],
            [ 'c' => 4551, 'n' => 'Sales - Private VLAN', ],
        ];

        $suser = Socialite::driver('sage')->user();

        $results = [];

        foreach( $ledgers as $l ) {
            $c = new \GuzzleHttp\Client();

            $ledger = [
                'ledger_account' => [
                    'ledger_account_type_id'    => 'SALES',
                    'included_in_chart'         => true,
                    'name'                      => $l['n'],
                    'display_name'              => $l['n'],
                    'nominal_code'              => $l['c'],
                    'tax_rate_id'               => 'IE_STANDARD',
                    'visible_in_sales'          => true,
                    'visible_in_other_receipts' => true,
                    'visible_in_reporting'      => true,
                    'visible_in_journals'       => true,
                ]
            ];

            $r = $c->post( 'https://api.accounting.sage.com/v3.1/ledger_accounts', [
                    \GuzzleHttp\RequestOptions::JSON => $ledger,
                    'headers'                        => [
                        'Authorization' => 'Bearer ' . $suser->token
                    ]
                ]
            );

            $results[] = [ 'name' => $l['n'], 'id' => json_decode( $r->getBody()->getContents() )->id ];
        }

        dd( $results );

//
//        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/tax_rates?items_per_page=200', [
//            'headers' => [
//                'Authorization' => 'Bearer ' . $suser->token
//            ]
//        ] );
//
//        dd( json_decode( $r->getBody()->getContents() ) );

    }

}
