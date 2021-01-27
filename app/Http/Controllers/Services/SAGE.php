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


use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

use Cache, Config;


use Entities\{
    IXP,
    Infrastructure,
    Vlan,
    Switcher,
    PhysicalInterface,
    VlanInterface,
    VirtualInterface,
    Customer
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

    private array $services = [

        'MEMBERFEE' => [ 'd' => 'Membership Fee (Annual)',           'l' => 4500, 'p' => '1000.00' ],
        'ASSOCFEE'  => [ 'd' => 'Associate Membership Fee (Annual)', 'l' => 4501, 'p' => '1000.00' ],

        'LAN1-1G-FIRST' => [ 'd' => 'INEX LAN1 - First 1Gb Port (per month)',      'l' => 4510, 'p' => '70.00' ],
        'LAN1-1G-ADDNL' => [ 'd' => 'INEX LAN1 - Additional 1Gb Port (per month)', 'l' => 4510, 'p' => '56.00' ],

        'LAN1-10G-FIRST' => [ 'd' => 'INEX LAN1 - First 10Gb Port (per month)',      'l' => 4510, 'p' => '275.00' ],
        'LAN1-10G-ADDNL' => [ 'd' => 'INEX LAN1 - Additional 10Gb Port (per month)', 'l' => 4510, 'p' => '220.00' ],

        'LAN1-100G-FIRST' => [ 'd' => 'INEX LAN1 - First 100Gb Port (per month)',      'l' => 4510, 'p' => '1375.00' ],
        'LAN1-100G-ADDNL' => [ 'd' => 'INEX LAN1 - Additional 100Gb Port (per month)', 'l' => 4510, 'p' => '1100.00' ],

        'LAN2-1G-FREE'  => [ 'd' => 'INEX LAN2 - First 1Gb Port (free with LAN1) (per month)',  'l' => 4511, 'p' => '0.00' ],
        'LAN2-1G-ADDNL' => [ 'd' => 'INEX LAN2 - Additional 1Gb Port (per month)',              'l' => 4511, 'p' => '56.00' ],

        'LAN2-10G-FREE'  => [ 'd' => 'INEX LAN2 - First 10Gb Port (free with LAN1) (per month)',      'l' => 4511, 'p' => '0.00' ],
        'LAN2-10G-ADDNL' => [ 'd' => 'INEX LAN2 - Additional 10Gb Port (per month)',                  'l' => 4511, 'p' => '220.00' ],

        'LAN2-100G-FIRST' => [ 'd' => 'INEX LAN2 - First 100Gb Port (per month)',                      'l' => 4511, 'p' => '1100.00' ],
        'LAN2-100G-ADDNL' => [ 'd' => 'INEX LAN2 - Additional 100Gb Port (per month)',                 'l' => 4511, 'p' => '1100.00' ],

        'CORK-1G-FIRST' => [ 'd' => 'INEX Cork - First 1Gb Port (per month)',      'l' => 4512, 'p' => '0.00' ],
        'CORK-1G-ADDNL' => [ 'd' => 'INEX Cork - Additional 1Gb Port (per month)', 'l' => 4512, 'p' => '0.00' ],

        'CORK-10G-FIRST' => [ 'd' => 'INEX Cork - First 10Gb Port (per month)',       'l' => 4512, 'p' => '0.00' ],
        'CORK-10G-ADDNL' => [ 'd' => 'INEX Cork - Additional 10Gb Port (per month)',  'l' => 4512, 'p' => '0.00' ],

    ];



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

        //return $this->pull();

        // 1 - Ledgers
        //return dd( $this->ledgers() );


        // 2 - Services
        // return dd( $this->services() );


        // 3 - Customers
        // return dd( $this->customers() );

        // 4 - Invoices
        return $this->invoices();


        return view( 'services/sage/index', [ 'suser' => Socialite::driver('sage')->user() ] );
    }

    public function pull()
    {

        /*
         * eu_sales_descriptions: STANDARD / EXEMPT / ZERO / OUTSIDE_SCOPE_OF_VAT
         *
         * tax_rates: IE_STANDARD / IE_REDUCED_HIGH / IE_REDUCED_LOW / IE_ZERO / IE_EXEMPT / IE_NO_TAX
         *
         *
         *
         */

        $suser = Socialite::driver('sage')->user();
        $c = new \GuzzleHttp\Client();

        // &visible_in=sales

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/tax_rates?items_per_page=200&attributes=all', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        dd( json_decode( $r->getBody()->getContents() ) );

    }



    public function services()
    {
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

        foreach( $this->services as $code => $s ) {
            $c = new \GuzzleHttp\Client();

            $service = [
                'service' => [
                    'description'               => $s['d'],
                    'item_code'                 => $code,
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
        }

        return $results;
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
            [ 'c' => 4551, 'n' => 'Sales - Private VLANs', ],
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

        return $results;
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
                $csvl[ $i ] = $v;
            }

            $e = [];

            $e[ 'cycle' ] = $csvl[ 0 ];
            $e[ 'ref' ]   = $csvl[ 1 ];  // Reference


            // resolds, etc.
            if( in_array( $e[ 'ref' ], [ 182, 183, 190, 171, 142 ] ) ) {
                Log::info( "***** SKIPPING {$e[ 'ref' ]}");
                continue;
            }



            $e[ 'cname' ] = $csvl[ 2 ]; // Company Name
            $e[ 'a1' ]    = $csvl[ 3 ];
            $e[ 'a2' ]    = $csvl[ 4 ];
            $e[ 'a3' ]    = $csvl[ 5 ];
            $e[ 'atc' ]   = $csvl[ 6 ];
            $e[ 'apc' ]   = $csvl[ 7 ];
            $e[ 'acc' ]   = ( $csvl[ 8 ] == 'UK' ? 'GB' : $csvl[ 8 ] );
            $e[ 'pcn' ]   = $csvl[ 9 ];
            $e[ 'pnp' ]   = $csvl[ 10 ];
            $e[ 'pnf' ]   = $csvl[ 11 ];
            $e[ 'pnp2' ]  = $csvl[ 12 ];

            $e['po'] = $csvl[14];
            $emails = explode( ',', $csvl[ 15 ] );

            if( $emails ) {
                foreach( $emails as $ei => $ee ) {
                    $emails[ $ei ] = trim( $ee );
                }
            }

            $e[ 'pne' ] = $emails;

            $e[ 'notes' ] = 'Quickbooks Import: ' . ( $csvl[ 13 ] ? $csvl[ 13 ] . " - " : '' ) . ( $csvl[ 14 ] ? $csvl[ 14 ] . " - " : '' ) . ( $csvl[ 17 ] ? ' - VAT: ' . $csvl[ 17 ] : '' );

            $e[ 'vat' ] = $csvl[ 17 ] ?? '';

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

            $cb->setVatNumber( trim( $csvl[ 17 ] ) ?? '' );
            $cb->setBillingEmail( implode( ',', $emails ) );
            $cb->setInvoiceMethod( $cb::INVOICE_METHOD_EMAIL );
            $cb->setVatRate( $e['po'] ?? '' );

//            switch( $e['cycle'] ) {
//                case 'A':
//                    $cb->setBillingFrequency( \Entities\CompanyBillingDetail::BILLING_FREQUENCY_ANNUALLY );
//                    break;
//
//                case 'H':
//                    $cb->setBillingFrequency( \Entities\CompanyBillingDetail::BILLING_FREQUENCY_HALFYEARLY );
//                    break;
//
//                case 'Q':
//                    $cb->setBillingFrequency( \Entities\CompanyBillingDetail::BILLING_FREQUENCY_QUARTERLY );
//                    break;
//
//                default:
//                    $results[] = "ERROR: " . $e['cname'] . ' - BAD BILLING CYCLE';
//            }

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

        return $results;

    }




    /**
     * Get an order list of physical interfaces for summary billing
     *
     * @return array An array of physical interfaces
     */
    private function getPhysIntsForAccounting(): array
    {
        $pis = D2EM::createQuery(
            "SELECT pi.id AS id, pi.speed AS speed, pi.status AS status,
                    c.name AS customer, c.id AS custid, c.autsys AS autsys,
                    vi.id AS vintid, 
                    v.id AS vlanid, v.number AS vlantag, v.private AS privatevlan
                    
                    FROM \\Entities\\PhysicalInterface pi
                        LEFT JOIN pi.VirtualInterface vi
                        LEFT JOIN vi.VlanInterfaces vli
                        LEFT JOIN vli.Vlan v 
                        LEFT JOIN vi.Customer c
                        
                    WHERE c.type = " . Customer::TYPE_FULL . "
                        AND pi.status = " . PhysicalInterface::STATUS_CONNECTED . "
                        
                    ORDER BY customer ASC, vlantag ASC, speed DESC"
        )->getArrayResult();

        // rearrange into per-customer, per vlan groups
        $summary = [];

        foreach( $pis as $pi ) {
            if( !isset( $summary[ $pi['autsys'] ] ) ) {
                $summary[ $pi['autsys'] ]['customer']     = $pi['customer'];
                $summary[ $pi['autsys'] ]['custid']       = $pi['custid'];
                $summary[ $pi['autsys'] ]['vlans']        = [];
                $summary[ $pi['autsys'] ]['privatevlans'] = [];
            }

            $summary[ $pi['autsys'] ][ $pi['privatevlan'] ? 'privatevlans' : 'vlans' ] [ $pi['vlantag'] ][] = $pi['speed'];
        }

        return $summary;
    }


    private function sageGetServices( $suser )
    {
        /*
         * e.g.
         *
         * array:18 [▼
              "MEMBERFEE" => "0e63d7139ddf4e8489b848793bad8927"
              "ASSOCFEE" => "c7792d9d1c5e4f05b028fcb709d4e71f"
              "LAN1-1G-FIRST" => "3381ab6a0dca43208f42984632122515"
              "LAN1-1G-ADDNL" => "b47e7729320d4a96abf99a838e70a863"
              "LAN1-10G-FIRST" => "caf5dd819c1041bb92611e5a61a0d621"
              "LAN1-10G-ADDNL" => "f5468a4b9c5841ddb27cf71174f3b667"
              "LAN1-100G-FIRST" => "e0f8941560d94c368689a507d00991cd"
              "LAN1-100G-ADDNL" => "730e2e686f954d32931328465d84a051"
              "LAN2-1G-FREE" => "beda89368a18492aaa749cb63de531fb"
              "LAN2-1G-ADDNL" => "58b2db4a25984eb39eb477b890902c58"
              "LAN2-10G-FREE" => "9355d13ba6e6452c9aebe89b50d359d2"
              "LAN2-10G-ADDNL" => "c47210a7a4b64219850c8cb3bd1b1320"
              "LAN2-100G-FIRST" => "bf547046b19e4dc189cba59495574469"
              "LAN2-100G-ADDNL" => "9e786d966d374967bda22faf967a1899"
              "CORK-1G-FIRST" => "be068952c4b64549a800d28580f156d3"
              "CORK-1G-ADDNL" => "c7d9dcc1f5d7474d827b048eded94b16"
              "CORK-10G-FIRST" => "d7a03edb857248d5bfb70a905daa12a1"
              "CORK-10G-ADDNL" => "18a9e50d4fc6416a9492c602e2470039"
            ]
         */

        $c = new \GuzzleHttp\Client();

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/services?items_per_page=200&attributes=all', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        $res = json_decode( $r->getBody()->getContents() );
        $tmp = '$items';
        $sageServices = [];
        foreach( $res->$tmp as $r ) {
            $sageServices[ $r->item_code ] = $r->id;
        }

        return $sageServices;
    }


    private function sageGetCustomers( $suser )
    {

        $c = new \GuzzleHttp\Client();

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/contacts?items_per_page=200&contact_type_id=CUSTOMER&attributes=reference', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        $res = json_decode( $r->getBody()->getContents() );
        $tmp = '$items';
        $sageCustomers = [];
        foreach( $res->$tmp as $r ) {
            if( !is_numeric($r->reference) ) {
                continue;
            }
            $sageCustomers[ $r->reference ] = $r->id;
        }

        return $sageCustomers;
    }


    private function sageGetLedgers( $suser )
    {

        $c = new \GuzzleHttp\Client();

        $r = $c->request( 'GET', 'https://api.accounting.sage.com/v3.1/ledger_accounts?items_per_page=200&visible_in=sales&attributes=nominal_code', [
            'headers' => [
                'Authorization' => 'Bearer ' . $suser->token
            ]
        ] );

        $res = json_decode( $r->getBody()->getContents() );
        $tmp = '$items';
        $sageLedgers = [];
        foreach( $res->$tmp as $r ) {
            $sageLedgers[ $r->nominal_code ] = $r->id;
        }

        return $sageLedgers;
    }


    public function invoices()
    {
        set_time_limit(0);

        $suser = Socialite::driver('sage')->user();

        $sageLedgers   = $this->sageGetLedgers( $suser );
        $sageServices  = $this->sageGetServices( $suser );
        $sageCustomers = $this->sageGetCustomers( $suser );

        $member_pis = $this->getPhysIntsForAccounting();

        $fp = fopen( base_path( 'cust-for-import.csv' ), "r" );

        // throw away first line
        fgetcsv( $fp );

        $custids = [];

        // for 2021 we're taking customers from the Quickbooks export first and then will identify any we miss later
        while( $csvl = fgetcsv( $fp ) ) {
            $custids[$csvl[1]] = (int)$csvl[1];
        }

        $dbAsnToIds = \IXP\Models\Customer::get(['id', 'autsys'])->keyBy('autsys')->toArray();


        $lan1vid = 10;
        $lan2vid = 12;
        $corkvid = 210;

        $chargeable_vlans = [ $lan1vid, $lan2vid, $corkvid ];

        $totals = [];

        foreach( $this->services as $k => $y ) {
            $totals[$k] = 0.0;
        }

        foreach( $member_pis as $asn => $pis ) {

            $invoice_lines = [];
            $notes = '';
            $ilidx = 0;

            $first_port_charge_done = false;
            $eligable_for_lan2_free = false;
            $lan2_free_applied = false;

            /** @var Customer $cust */
            $cust = d2r('Customer')->find( $dbAsnToIds[$asn]['id'] );
            unset( $dbAsnToIds[$asn] );
            unset( $custids[$cust->getId()]);


            if( in_array( $cust->getId(), [ 182, 183, 190, 171, 142 ] ) ) {
                Log::info( "***** SKIPPING {$cust->getName()}");
                continue;
            }

            Log::info( "***** START {$cust->getName()}");

            $invoice = [
                'contact_id' => $sageCustomers[ $cust->getId() ],
                'date'       => '2021-01-22',
                'status_id'  => 'DRAFT',
            ];

            if( $cust->getBillingDetails()->getVatRate() ) {
                $invoice['reference'] = "P/O: " . $cust->getBillingDetails()->getVatRate();
            }

            $notes .= "Billing period: " . Carbon::now()->startOfMonth()->format( 'M jS, Y' )
                . ' - ' . Carbon::now()->startOfMonth()->addMonths( $cust->getBillingDetails()->getFrequencyAsNumMonths() - 1 )->endOfMonth()->format( 'M jS, Y' )
                . '. ';

            // membership
            $fee = round( $this->services['MEMBERFEE']['p'], 2);
            $invoice_lines[$ilidx++] = [
                'description'             => $this->services['MEMBERFEE']['d'],
                'ledger_account_id'       => $sageLedgers[ $this->services['MEMBERFEE']['l'] ],
                'quantity'                => (string)($cust->getBillingDetails()->getFrequencyAsNumMonths()/12),
                'unit_price'              => (string)$fee,
                'service_id'              => $sageServices['MEMBERFEE'],
                'unit_price_includes_tax' => false,
            ];

            $totals[ 'MEMBERFEE' ] += $invoice_lines[$ilidx-1]['quantity']*$invoice_lines[$ilidx-1]['unit_price'];

            // getPhysIntsForAccounting() query's ORDER BY is critical in how this works:
            foreach( $pis[ 'vlans' ] as $vid => $ports ) {

                if( !in_array( $vid, $chargeable_vlans ) ) {
                    continue;
                }

                foreach( $ports as $p ) {

                    // lan2 1st 1/10Gb port free
                    if( $vid == $lan2vid && ( $p == 1000 || $p == 10000 ) && $eligable_for_lan2_free && !$lan2_free_applied ) {

                        $sc = $p == 1000 ? 'LAN2-1G-FREE' : 'LAN2-10G-FREE';

                        $fee = $this->services[$sc]['p'];
                        $invoice_lines[$ilidx++] = [
                            'description'             => $this->services[ $sc ]['d'],
                            'ledger_account_id'       => $sageLedgers[ $this->services[$sc]['l'] ],
                            'quantity'                => $cust->getBillingDetails()->getFrequencyAsNumMonths(),
                            'unit_price'              => (string)$fee,
                            'service_id'              => $sageServices[$sc],
                            'unit_price_includes_tax' => false,
                        ];

                        $lan2_free_applied = true;

                        $totals[ $sc ] += $invoice_lines[$ilidx-1]['quantity']*$invoice_lines[$ilidx-1]['unit_price'];

                        continue;
                    }

                    $sc = '';
                    if( $vid == $lan1vid ) {
                        $eligable_for_lan2_free = true;
                        $sc .= 'LAN1-';
                    } else if( $vid == $lan2vid ) {
                        $sc .= 'LAN2-';
                    } else {
                        $sc .= 'CORK-';
                    }

                    $sc .= ( $p / 1000 ) . 'G-';

                    if( !$first_port_charge_done ) {
                        $first_port_charge_done = true;
                        $sc .= 'FIRST';
                    } else {
                        $sc .= 'ADDNL';
                    }

                    $fee = $this->services[$sc]['p'];
                    $invoice_lines[$ilidx++] = [
                        'description'             => $this->services[ $sc ]['d'],
                        'ledger_account_id'       => $sageLedgers[ $this->services[$sc]['l'] ],
                        'quantity'                => $cust->getBillingDetails()->getFrequencyAsNumMonths(),
                        'unit_price'              => (string)$fee,
                        'service_id'              => $sageServices[$sc],
                    ];

                    $totals[ $sc ] += $invoice_lines[$ilidx-1]['quantity']*$invoice_lines[$ilidx-1]['unit_price'];

                }
            }

            // EU ?
            if( in_array( $cust->getBillingDetails()->getBillingCountry(), [ 'BE', 'EL', 'LT', 'PT', 'BG', 'ES', 'LU', 'RO', 'CZ', 'FR', 'HU', 'SI', 'DK', 'HR', 'MT', 'SK', 'DE', 'IT', 'NL', 'FI', 'EE', 'CY', 'AT', 'SE', 'LV', 'PL' ] ) ) {

                foreach( $invoice_lines as $i => $il ) {
                    $invoice_lines[ $i ][ 'eu_goods_services_type_id' ] = 'SERVICES';
                    $invoice_lines[$i]['eu_sales_descriptions']     = 'STANDARD';

                    $invoice_lines[ $i ][ 'tax_rate_id' ] = 'IE_ZERO';
                    $invoice_lines[ $i ][ 'tax_amount' ] = '0.00';
                }

                $notes .= 'All supplies are an intra-community supply. ';

                // Northern Ireland
            } else if( in_array( $cust->getId(), [ 22, 172, 25, 39 ] ) ) {

                    foreach( $invoice_lines as $i => $il ) {
                        $invoice_lines[$i]['eu_goods_services_type_id'] = 'SERVICES';

                        $invoice_lines[$i]['tax_rate_id']               = 'IE_ZERO';
                        $invoice_lines[$i]['tax_amount']                = '0.00';
                    }

            } else if( $cust->getBillingDetails()->getBillingCountry() == 'IE' ) {

                foreach( $invoice_lines as $i => $il ) {
                    $invoice_lines[ $i ][ 'tax_rate_id' ] = 'IE_STANDARD';
                    $invoice_lines[ $i ][ 'tax_amount' ] = (string)round( ( $invoice_lines[ $i ]['quantity'] * $invoice_lines[ $i ]['unit_price'] * 0.21 ), 2 );
                }

            } else {

                foreach( $invoice_lines as $i => $il ) {
                    $invoice_lines[ $i ][ 'tax_rate_id' ] = 'IE_ZERO';
                    $invoice_lines[ $i ][ 'tax_amount' ] = '0.00';
                }
            }

            $invoice['invoice_lines'] = $invoice_lines;
            $invoice['notes'] = $notes;

            $guzzle = new \GuzzleHttp\Client();

            $r = $guzzle->post( 'https://api.accounting.sage.com/v3.1/sales_invoices', [
                    \GuzzleHttp\RequestOptions::JSON => [ 'sales_invoice' => $invoice ],
                    'headers'                        => [
                        'Authorization' => 'Bearer ' . $suser->token
                    ]
                ]
            );

            Log::info( "***** END {$cust->getName()}");

            // dd( json_decode( $r->getBody()->getContents() ));

        }

        Log::info(json_encode($totals));

        Log::info( "NO INVOICES VIA PIs FOR:" );

        foreach( $dbAsnToIds as $asn => $id ) {
            $cust = d2r('Customer')->find($id['id']);

            if( $cust->isTypeAssociate() || $cust->hasLeft() ) {
                continue;
            }

            Log::info( "    - " . $cust->getName() );
        }


        Log::info( "NO INVOICES VIA CSV FOR:" );

        foreach( $custids as $id ) {
            $cust = d2r('Customer')->find($id);

            if( $cust->isTypeAssociate() || $cust->hasLeft() ) {
                continue;
            }

            Log::info( "    - " . $cust->getName() );
        }

        dd($totals);

    }



}

