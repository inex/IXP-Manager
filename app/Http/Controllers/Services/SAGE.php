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

use Auth, D2EM, Log;


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
        return $this->customers();
        //return $this->pull();
        return view( 'services/sage/index', [ 'suser' => Socialite::driver('sage')->user() ] );
    }

    public function pull()
    {
        $suser = Socialite::driver('sage')->user();
        $c = new \GuzzleHttp\Client();

        // &visible_in=sales

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/country_groups?items_per_page=200&attributes=all', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        dd( json_decode( $r->getBody()->getContents() ) );

    }



    public function services()
    {
        $services = [
            [ 'd' => 'INEX LAN1 - First 1Gb Port',      'c' => 'LAN1-1G-FIRST', 'l' => 4510, 'p' => '70.00' ],
            [ 'd' => 'INEX LAN1 - Additional 1Gb Port', 'c' => 'LAN1-1G-ADDNL', 'l' => 4510, 'p' => '56.00' ],

            [ 'd' => 'INEX LAN1 - First 10Gb Port',      'c' => 'LAN1-10G-FIRST', 'l' => 4510, 'p' => '275.00' ],
            [ 'd' => 'INEX LAN1 - Additional 10Gb Port', 'c' => 'LAN1-10G-ADDNL', 'l' => 4510, 'p' => '220.00' ],

            [ 'd' => 'INEX LAN1 - First 100Gb Port',      'c' => 'LAN1-100G-FIRST', 'l' => 4510, 'p' => '1375.00' ],
            [ 'd' => 'INEX LAN1 - Additional 100Gb Port', 'c' => 'LAN1-100G-ADDNL', 'l' => 4510, 'p' => '1100.00' ],

            [ 'd' => 'INEX LAN2 - First 1Gb Port (free with LAN1)',       'c' => 'LAN2-1G-FREE',  'l' => 4511, 'p' => '0.00' ],
            [ 'd' => 'INEX LAN2 - Additional 1Gb Port',                   'c' => 'LAN2-1G-ADDNL', 'l' => 4511, 'p' => '56.00' ],

            [ 'd' => 'INEX LAN2 - First 10Gb Port (free with LAN1)',      'c' => 'LAN2-10G-FREE',  'l' => 4511, 'p' => '0.00' ],
            [ 'd' => 'INEX LAN2 - Additional 10Gb Port',                  'c' => 'LAN2-10G-ADDNL', 'l' => 4511, 'p' => '220.00' ],

            [ 'd' => 'INEX LAN2 - First 100Gb Port',                      'c' => 'LAN2-100G-FIRST', 'l' => 4511, 'p' => '1100.00' ],
            [ 'd' => 'INEX LAN2 - Additional 100Gb Port',                 'c' => 'LAN2-100G-ADDNL', 'l' => 4511, 'p' => '1100.00' ],

            [ 'd' => 'INEX Cork - First 1Gb Port',      'c' => 'LANC-1G-FIRST', 'l' => 4512, 'p' => '0.00' ],
            [ 'd' => 'INEX Cork - Additional 1Gb Port', 'c' => 'LANC-1G-ADDNL', 'l' => 4512, 'p' => '0.00' ],

            [ 'd' => 'INEX Cork - First 10Gb Port',      'c' => 'LANC-10G-FIRST', 'l' => 4512, 'p' => '0.00' ],
            [ 'd' => 'INEX Cork - Additional 10Gb Port', 'c' => 'LANC-10G-ADDNL', 'l' => 4512, 'p' => '0.00' ],

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

            $results[] = [ 'name' => $s['d'], 'id' => json_decode( $r->getBody()->getContents() )->id ];
//            dd( json_decode( $r->getBody()->getContents() ) );
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


    public function customers()
    {
        set_time_limit(0);

        $suser = Socialite::driver('sage')->user();

        $fp = fopen( base_path( 'cust-for-import.csv' ), "r" );

        // throw away first line
        fgetcsv( $fp );

        $results = [];

        while( $csvl = fgetcsv( $fp ) ) {

            // clean the entries
            foreach( $csvl as $i => $v ) {
                $v = trim( $v );
                if( substr( $v, -1 ) === ',' ) {
                    $v = substr( $v, 0, -1 );
                }
                $d[ $i ] = $v;
            }

            $e = [];

            $e[ 'cycle' ] = $d[ 0 ];
            $e[ 'ref' ] = $d[ 1 ];  // Reference
            $e[ 'cname' ] = $d[ 2 ]; // Company Name
            $e[ 'a1' ] = $d[ 3 ];
            $e[ 'a2' ] = $d[ 4 ];
            $e[ 'a3' ] = $d[ 5 ];
            $e[ 'atc' ] = $d[ 6 ];
            $e[ 'apc' ] = $d[ 7 ];
            $e[ 'acc' ] = ( $d[ 8 ] == 'UK' ? 'GB' : $d[ 8 ] );
            $e[ 'pcn' ] = $d[ 9 ];
            $e[ 'pnp' ] = $d[ 10 ];
            $e[ 'pnf' ] = $d[ 11 ];
            $e[ 'pnp2' ] = $d[ 12 ];

            $e['po'] = $d[14];
            $emails = explode( ',', $d[ 15 ] );

            if( $emails ) {
                foreach( $emails as $ei => $ee ) {
                    $emails[ $ei ] = trim( $ee );
                }
            }

            $e[ 'pne' ] = $emails;

            $e[ 'notes' ] = 'Quickbooks Import: ' . ( $d[ 13 ] ? $d[ 13 ] . " - " : '' ) . ( $d[ 14 ] ? $d[ 14 ] . " - " : '' ) . ( $d[ 17 ] ? ' - VAT: ' . $d[ 17 ] : '' );

            $e[ 'vat' ] = $d[ 17 ] ?? '';

            if( $e[ 'acc' ] === 'IE' || substr( $e[ 'vat' ], 0, 2 ) == 'IE' || in_array( $e[ 'ref' ], [ 186 ] ) ) {
                $e[ 'vat' ] = '';
            }
//            else if( !is_numeric( substr( $e[ 'vat' ], 0, 2 ) ) ) {
//                $e[ 'vat' ] = substr( $e[ 'vat' ], 2 );
//            }


            // country group
            switch( $e[ 'acc' ] ) {
                case 'IE':
                case 'GB':
                    $cg = 'GBIE';
                    break;

                case 'US':
                    $cg = 'US';
                    break;

                case 'CA':
                    $cg = 'CA';
                    break;

                case 'AT':
                case 'BE':
                case 'DE':
                case 'IT':
                case 'NL':
                    $cg = 'EU';
                    break;

                default:
                    $cg = 'ALL';
                    break;
            }


            /** @var \Entities\Customer $c */
            if( !( $c = d2r( 'Customer' )->find( $e['ref'] ) ) ) {
                $results[] = "ERROR: " . $e['cname'] . ' NOT FOUND';
                continue;
            }

            $cc = $c->getRegistrationDetails();
            $cb = $c->getBillingDetails();

            $cc->setRegisteredName( $e['cname'] );
            $cc->setAddress1( $e[ 'a1' ] );     $cb->setBillingAddress1( $e[ 'a1' ] );
            $cc->setAddress2( $e[ 'a2' ] );     $cb->setBillingAddress2( $e[ 'a2' ] );
            $cc->setAddress3( $e[ 'a3' ] );     $cb->setBillingAddress3( $e[ 'a3' ] );
            $cc->setTownCity( $e[ 'atc' ] );    $cb->setBillingTownCity( $e[ 'atc' ] );
            $cc->setPostcode( $e[ 'apc' ] );    $cb->setBillingPostcode( $e[ 'apc' ] );
            $cc->setCountry( $e['acc'] );       $cb->setBillingCountry( $e['acc'] );

            $cb->setVatNumber( trim( $d[ 17 ] ) ?? '' );
            $cb->setBillingEmail( implode( ',', $emails ) );
            $cb->setInvoiceMethod( $cb::INVOICE_METHOD_EMAIL );
            $cb->setVatRate( $e['po'] ?? '' );

            switch( $e['cycle'] ) {
                case 'A':
                    $cb->setBillingFrequency( \Entities\CompanyBillingDetail::BILLING_FREQUENCY_ANNUALLY );
                    break;

                case 'H':
                    $cb->setBillingFrequency( \Entities\CompanyBillingDetail::BILLING_FREQUENCY_HALFYEARLY );
                    break;

                case 'Q':
                    $cb->setBillingFrequency( \Entities\CompanyBillingDetail::BILLING_FREQUENCY_QUARTERLY );
                    break;

                default:
                    $results[] = "ERROR: " . $e['cname'] . ' - BAD BILLING CYCLE';
                    continue;
            }

            D2EM::flush();

            Log::info( "***** {$c->getName()}");

            $scust = [
                'name'             => $cc->getRegisteredName(),
                'contact_type_ids' => [ 'CUSTOMER' ],
                'reference'        => $c->getId(),
                'main_address'     => [
                    'address_type_id'  => 'ACCOUNTS',
                    'address_line_1'   => $cb->getBillingAddress1(),
                    'address_line_2'   => $cb->getBillingAddress2(),
                    'city'             => $cb->getBillingAddress3(),
                    'region'           => $cb->getBillingTownCity(),
                    'postal_code'      => $cb->getBillingPostcode(),
                    'country_id'       => $cb->getBillingCountry(),
                    'country_group_id' => $cg,
                    'is_main_address'  => true,
                ],
                'notes'            => $e[ 'notes' ],
                'credit_days'      => 30,
                'currency_id'      => 'EUR',
                'main_contact_person' => [
                    'name'      => $e[ 'pcn' ],
                    'telephone' => $e[ 'pnp' ],
                    'mobile'    => $e[ 'pnp2' ],
                    'fax'       => $e[ 'pnf' ],
                    'is_main_contact' => true,
                    'is_preferred_contact' => false,
                ]
            ];

            if( $e['vat'] ) {
                $scust['tax_number'] = $e['vat'];
            }

            if( count( $e['pne'] ) ) {
                $scust['main_contact_person']['email'] = array_shift($e['pne']);
            }



            // ********
            $scust['name'] .= ' ' . rand(0,10000);
            $scust['reference'] = ' ' . rand(1000,10000);
            // ********

            $guzzle = new \GuzzleHttp\Client();

            $scust = [ 'contact' => $scust ];

            $r = $guzzle->post( 'https://api.accounting.sage.com/v3.1/contacts', [
                    \GuzzleHttp\RequestOptions::JSON => $scust,
                    'headers'                        => [
                        'Authorization' => 'Bearer ' . $suser->token
                    ]
                ]
            );

            $newcust = json_decode( $r->getBody()->getContents() );
            //$results[] = $c->getName();
            // $results[] = [ 'name' => $l['n'], 'id' => json_decode( $r->getBody()->getContents() )->id ];
            // dd(json_decode( $r->getBody()->getContents() ));

            foreach( $e['pne'] as $e ) {
                $newcontact = [
                    'contact_person' => [
                        'address_id' => $newcust->main_address->id,
                        'name' => $e,
                        'contact_person_type_ids' => [ 'ACCOUNTS' ],
                        'email' => $e,
                        'is_preferred_contact' => true,
                    ]
                ];

                $guzzle = new \GuzzleHttp\Client();

                $r = $guzzle->post( 'https://api.accounting.sage.com/v3.1/contact_persons', [
                        \GuzzleHttp\RequestOptions::JSON => $newcontact,
                        'headers'                        => [
                            'Authorization' => 'Bearer ' . $suser->token
                        ]
                    ]
                );

            }

        }

        dd($results);

    }


    

}
