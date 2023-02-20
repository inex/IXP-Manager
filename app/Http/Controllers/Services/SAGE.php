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
use Illuminate\Support\Facades\DB;
use IXP\Models\CompanyBillingDetail;
use Laravel\Socialite\Facades\Socialite;

use Cache, Config;

use ErrorException;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

use IXP\Contracts\LookingGlass as LookingGlassContract;

use IXP\Exceptions\Services\LookingGlass\GeneralException as LookingGlassGeneralException;

use IXP\Http\Controllers\Controller;

use IXP\Models\Customer;


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

        'MEMBERFEE' => [ 'd' => 'Membership Fee (Annual)',           'l' => 4500, 'p' => 1000.00 ],
        'ASSOCFEE'  => [ 'd' => 'Associate Membership Fee (Annual)', 'l' => 4501, 'p' => 1000.00 ],

        'LAN1-1G-FIRST' => [ 'd' => 'INEX LAN1 - First 1Gb Port (per month)',      'l' => 4510, 'p' => 0.00 ],
        'LAN1-1G-ADDNL' => [ 'd' => 'INEX LAN1 - Additional 1Gb Port (per month)', 'l' => 4510, 'p' => 56.00 ],

        'LAN1-10G-FIRST' => [ 'd' => 'INEX LAN1 - First 10Gb Port (per month)',      'l' => 4510, 'p' => 250.00 ],
        'LAN1-10G-ADDNL' => [ 'd' => 'INEX LAN1 - Additional 10Gb Port (per month)', 'l' => 4510, 'p' => 200.00 ],

        'LAN1-100G-FIRST' => [ 'd' => 'INEX LAN1 - First 100Gb Port (per month)',      'l' => 4510, 'p' => 1250.00 ],
        'LAN1-100G-ADDNL' => [ 'd' => 'INEX LAN1 - Additional 100Gb Port (per month)', 'l' => 4510, 'p' => 1000.00 ],

        'LAN2-1G-FREE'  => [ 'd' => 'INEX LAN2 - First 1Gb Port (free with LAN1) (per month)',  'l' => 4511, 'p' => 0.00 ],
        'LAN2-1G-ADDNL' => [ 'd' => 'INEX LAN2 - Additional 1Gb Port (per month)',              'l' => 4511, 'p' => 56.00 ],

        'LAN2-10G-FREE'  => [ 'd' => 'INEX LAN2 - First 10Gb Port (free with LAN1) (per month)',      'l' => 4511, 'p' => 0.00 ],
        'LAN2-10G-ADDNL' => [ 'd' => 'INEX LAN2 - Additional 10Gb Port (per month)',                  'l' => 4511, 'p' => 200.00 ],

        'LAN2-100G-FIRST' => [ 'd' => 'INEX LAN2 - First 100Gb Port (per month)',                      'l' => 4511, 'p' => 1100.00 ],
        'LAN2-100G-ADDNL' => [ 'd' => 'INEX LAN2 - Additional 100Gb Port (per month)',                 'l' => 4511, 'p' => 1000.00 ],

        'CORK-1G-FIRST' => [ 'd' => 'INEX Cork - First 1Gb Port (per month)',      'l' => 4512, 'p' => 0.00 ],
        'CORK-1G-ADDNL' => [ 'd' => 'INEX Cork - Additional 1Gb Port (per month)', 'l' => 4512, 'p' => 0.00 ],

        'CORK-10G-FIRST' => [ 'd' => 'INEX Cork - First 10Gb Port (per month)',       'l' => 4512, 'p' => 0.00 ],
        'CORK-10G-ADDNL' => [ 'd' => 'INEX Cork - Additional 10Gb Port (per month)',  'l' => 4512, 'p' => 0.00 ],

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

        // as at 2021-01:
        //  4000 => "77e046ce330811ebb6a30662051ba57b"
        //  4010 => "77e08ef3330811ebb6a30662051ba57b"
        //  4020 => "77e0c9a5330811ebb6a30662051ba57b"
        //  4200 => "77e0e7c4330811ebb6a30662051ba57b"
        //  4400 => "77e1253c330811ebb6a30662051ba57b"
        //  4900 => "77e16735330811ebb6a30662051ba57b"
        //  4910 => "77e1a46a330811ebb6a30662051ba57b"
        //  4920 => "77f132c8330811ebb6a30662051ba57b"
        //  4930 => "77f1be64330811ebb6a30662051ba57b"
        //  4940 => "77f1f49c330811ebb6a30662051ba57b"
        //  4500 => "46681eff6743457bb2c33f567a3aeaa3"
        //  4501 => "f6ff842cb1d5417db8d256b174e04a34"
        //  4510 => "4137b16419ce4a37926cf2e27be50dd3"
        //  4511 => "37a1d03a2dc24daa89fd9955a173b3da"
        //  4512 => "a4125c9171734b3999dd3840f2abfa0f"
        //  4550 => "fda2ab6d58de4ea78086a1ec01069dfe"
        //  4551 => "f0a2e44c02f6434e968c5117b482f8e4"
        //  4600 => "c6c0f2a0e28f456884385d6a0de51e5d"
        //  4905 => "6a7dcd0e6ebc444c8016aab2d4f53288"


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
        /*
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
        */

        $pis = \DB::table( 'physicalinterface' )
            ->select( DB::raw(
                    "physicalinterface.id AS physicalinterface, physicalinterface.speed AS speed,  physicalinterface.rate_limit AS rate_limit, 
                    physicalinterface.status AS status,
                    cust.name AS customer, cust.id AS custid, cust.autsys AS autsys,
                    virtualinterface.id AS vintid, 
                    vlan.id AS vlanid, vlan.number AS vlantag, vlan.private AS privatevlan" )
            )
            ->leftJoin( 'virtualinterface', 'physicalinterface.virtualinterfaceid', '=', 'virtualinterface.id' )
            ->leftJoin( 'vlaninterface', 'vlaninterface.virtualinterfaceid', '=', 'virtualinterface.id' )
            ->leftJoin( 'vlan', 'vlaninterface.vlanid', '=', 'vlan.id' )
            ->leftJoin( 'cust', 'virtualinterface.custid', '=', 'cust.id' )

            ->where( 'cust.type', \IXP\Models\Customer::TYPE_FULL )
            ->where( 'physicalinterface.status', \IXP\Models\PhysicalInterface::STATUS_CONNECTED )
            ->orderBy( 'cust.name', 'ASC' )
            ->orderBy( 'vlan.number', 'ASC' )
            ->orderBy( 'physicalinterface.speed', 'DESC' )
            ->get();

        // rearrange into per-customer, per vlan groups
        $summary = [];

        foreach( $pis as $pi ) {
            if( !isset( $summary[ $pi->autsys ] ) ) {
                $summary[ $pi->autsys ]['customer']     = $pi->customer;
                $summary[ $pi->autsys ]['custid']       = $pi->custid;
                $summary[ $pi->autsys ]['vlans']        = [];
                $summary[ $pi->autsys ]['privatevlans'] = [];
            }

            $summary[ $pi->autsys ][ $pi->privatevlan ? 'privatevlans' : 'vlans' ][ $pi->vlantag ][] = $pi->speed;
        }

        return $summary;
    }


    private function sageGetServices( $suser )
    {
        // as at 2022-01
        //  "MEMBERFEE" => "e55c9c57ab304a1795a8ea16a6a7af71"
        //  "ASSOCFEE" => "c2ead80bebdf4bfd9d2d82ba5cfd3474"
        //  "LAN1-1G-FIRST" => "0a09a1b16f1f410fbb51b8da06bfc60b"
        //  "LAN1-1G-ADDNL" => "9f047564115848b8afe43c923e0dde57"
        //  "LAN1-10G-FIRST" => "ab0d5475596e48e091e5e15e19c6f2d2"
        //  "LAN1-10G-ADDNL" => "da149423cd2e4894af5b5e6df6edc955"
        //  "LAN1-100G-FIRST" => "eda4b5ced92a4916bd701baf20c96268"
        //  "LAN1-100G-ADDNL" => "2da95779856a40c29d9598f655b06669"
        //  "LAN2-1G-FREE" => "d9b44cda01a74692b8e5d137eef3d71d"
        //  "LAN2-1G-ADDNL" => "0c035ee109e54668bfb244fdf84c3168"
        //  "LAN2-10G-FREE" => "0c9ce99e43134215a997e1b5dd5ce7ec"
        //  "LAN2-10G-ADDNL" => "ab1217c07f6748f49f210c2b83b8c82a"
        //  "LAN2-100G-FIRST" => "a5656b816d9743669d49c4e490f64031"
        //  "LAN2-100G-ADDNL" => "6bc3d69e12784c0b9aa10111bbc412b8"
        //  "CORK-1G-FIRST" => "1cc07bb2af124e3ba7464b3fecb2170a"
        //  "CORK-1G-ADDNL" => "f6522d68bf0c44d9998484e8abe91154"
        //  "CORK-10G-FIRST" => "6bef031be1794f479a18feff826a8e38"
        //  "CORK-10G-ADDNL" => "b3b9f5a45af041319f67197d5e8676d9"
        //  "XCONNECT" => "32be0e66257c401eb1abec98790f5d29"
        //  "RESELLER-LAN1-1G-FIRST" => "4ff1e78e2822449cbd60dc16626b3a0a"
        //  "RESELLER-LAN1-1G-ADDNL" => "ab453e6bc63a45a89d343ceb793fd55c"
        //  "POWER" => "87ec8e8bcdcb454d823f38fbe6207dc9"

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
        // example as at 2022-01
        //  2 => "acef0b940e4a427c92ff6a20fefa7334"
        //  21 => "399e91a2d14e4505b52ea001c8db17ab"
        //  71 => "5da9e1a1353c4a71aa9eb1867d70fac6"
        //  161 => "cd894054695b4760b731c6466d35e062"
        //  69 => "d7a2f19298424254be42c38c2cbb218e"
        //  106 => "e4ab461ac88a407eabff7ec8b9f7e166"
        //  146 => "c4eeb5371e4a4626a778c303fdf254ad"
        //  94 => "0ad5440a56974efdb5bf48681b0b1c03"
        //  159 => "c95e91b0b8a44f87948f9e00af7d00e5"
        //  150 => "2d8db4e3b0b84327aced9f829872dd37"
        //  64 => "8970091a17af46f5bcd9490c68e05608"
        //  65 => "d059cde83c554f7c9f22b01f929b8739"
        //  56 => "8543cd210f4d4c5ea5f5b163acf3e1b5"
        //  166 => "cc2b15d804304f5f88556ec72cf03f82"
        //  167 => "62efe73add4b42d4b99ceac07090fa08"
        //  75 => "ccc8ffddb9c64e7bb4de3c97a41ac7ba"
        //  177 => "2d062759ee0f4f11b7acbd04650d10ea"
        //  22 => "e3f24bfe8add477ea13d15dd30f2a9d2"
        //  46 => "94d3d4263d8541609f59e1e77d9737ea"
        //  172 => "1d5e63fecd6a4ff7ae034d7ba32d7252"
        //  76 => "a5743fbc21ed467f8dc8fb70310eebdd"
        //  189 => "9e7875b78de4406d9c6c5a108e668576"
        //  29 => "806350b9ad5d4e2e85bef6da0f739286"
        //  87 => "b0eb0741537d462997eae88e3d7cb2c8"
        //  34 => "51c94fc3d61b4e6a83f16214cf20b515"
        //  154 => "5410c808f3934e34a196971918544b42"
        //  4 => "81fdada6e5a14b9d86a3d3570672fa73"
        //  160 => "1c56798cebcf413b89b22a61499b452f"
        //  144 => "fe377487d7a94733a05986082ad9fc89"
        //  175 => "e7a4f6d0fd7e4f5cab039236b32890cc"
        //  131 => "5e1db9522aaa47b08b3ff51ba8ccd34b"
        //  133 => "e4950947d56d45fab0d11bb8558e2218"
        //  66 => "42c2a927e0b0469493234a744858c6be"
        //  3 => "1ff5f2e2f0514af6a21a9367cdedb1a1"
        //  165 => "db8cb69f02934ce7a1c7492c7a4674bc"
        //  129 => "215bd8940c3e40aead3b32dd25973fc6"
        //  113 => "497cf2a285f646dda17ab9ce0c0d73a8"
        //  118 => "f8c4f1c25e1d487cac479de4d1daac47"
        //  105 => "ea486181dcd940278914d6538ec23c1e"
        //  88 => "37cc5c86f980438f9b303826db821388"
        //  116 => "2ccd86be6255422998e4d12ee7d9839b"


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

        $lan1vid = 10;
        $lan2vid = 12;
        $corkvid = 210;

        $chargeable_vlans = [ $lan1vid, $lan2vid, $corkvid ];

        $totals = [];

        foreach( $this->services as $k => $y ) {
            $totals[$k] = 0.0;
        }

        $fp = fopen( base_path( 'custs-invoiced.csv' ), "w" );

        $reached = false;
        foreach( $member_pis as $asn => $pis ) {

            $invoice_lines = [];
            $notes = '';
            $ilidx = 0;

            $first_port_charge_done = false;
            $eligable_for_lan2_free = false;
            $lan2_free_applied = false;
            $lan1_free_applied = false;

            if( !( $cust = Customer::find( $pis['custid'] ) ) ) {
                Log::error("Could not load model for customer ID {$pis['custid']}");
                continue;
            }


//            if( $cust->id == 39 ) {
//                $reached = true;
//            }
//
//            if( !$reached ) {
//                continue;
//            }

//            if( $cust->companyBillingDetail->billingFrequency != CompanyBillingDetail::BILLING_FREQUENCY_QUARTERLY ) {
//                continue;
//            }


            // 182 -  Convergenze [AS39120] FULL MEMBER RESOLD CUSTOMER
            // 183 - Sirius Technology SRL [AS60501] FULL MEMBER RESOLD CUSTOMER
            // 190 -  Swisscom [AS3303] FULL MEMBER RESOLD CUSTOMER
            // 171 -  Telin [AS7713] FULL MEMBER ACCOUNT CLOSED RESOLD CUSTOMER
            if( in_array( $cust->id, [ 182, 183, 190, 171, ] ) ) {
                Log::info( "***** SKIPPING {$cust->name}");
                continue;
            }

            Log::info( "***** START {$cust->name}");

            $invoice = [
                'contact_id' => $sageCustomers[ $cust->id ] ?? 'XXX',
                'date'       => '2023-02-20',
                'status_id'  => 'DRAFT',
            ];

            if( $cust->companyBillingDetail->vatRate ) {
                $invoice['reference'] = "P/O: " . $cust->companyBillingDetail->vatRate;
            }

            $notes .= "Billing period: " . Carbon::now()->startOfMonth()->format( 'M jS, Y' )
                . ' - ' . Carbon::now()->startOfMonth()->addMonths( $cust->companyBillingDetail->getFrequencyAsNumMonths() - 1 )->endOfMonth()->format( 'M jS, Y' )
                . '. ';

            // membership
            $fee = round( $this->services['MEMBERFEE']['p'], 2);
            $invoice_lines[$ilidx++] = [
                'description'             => $this->services['MEMBERFEE']['d'],
                'ledger_account_id'       => $sageLedgers[ $this->services['MEMBERFEE']['l'] ],
                'quantity'                => (string)($cust->companyBillingDetail->getFrequencyAsNumMonths()/12),
                'unit_price'              => (string)$fee,
                'service_id'              => $sageServices['MEMBERFEE'],
                'unit_price_includes_tax' => false,
            ];

            // membership
            fputcsv( $fp, [
                'cust_asn'   => $asn,
                'cust_name'  => $cust->name,
                'category'   => 'MEMBER_FEE',
                'service'    => 'MEMBER_FEE',
                'cost'       => round( (int)$this->services['MEMBERFEE']['p'], 2) * ( $cust->companyBillingDetail->getFrequencyAsNumMonths() / 12 ),
            ]);

            Log::info( "    - Fee {$invoice_lines[$ilidx-1]['description']} @ {$invoice_lines[$ilidx-1]['unit_price']} x {$invoice_lines[$ilidx-1]['quantity']}" );

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
                            'quantity'                => $cust->companyBillingDetail->getFrequencyAsNumMonths(),
                            'unit_price'              => (string)$fee,
                            'service_id'              => $sageServices[$sc],
                            'unit_price_includes_tax' => false,
                        ];
                        Log::info( "    -     {$invoice_lines[$ilidx-1]['description']} @ {$invoice_lines[$ilidx-1]['unit_price']} x {$invoice_lines[$ilidx-1]['quantity']}" );

                        $lan2_free_applied = true;

                        fputcsv( $fp, [
                            'cust_asn'   => $asn,
                            'cust_name'  => $cust->name,
                            'category'   => 'LAN2',
                            'service'    => $sc,
                            'cost'       => 0,
                        ]);

                        $totals[ $sc ] += $invoice_lines[$ilidx-1]['quantity']*$invoice_lines[$ilidx-1]['unit_price'];

                        continue;
                    }

                    // lan1 1st 1Gb port free
                    if( $vid == $lan1vid && ( $p == 1000 ) && !$lan1_free_applied ) {

                        $sc = 'LAN1-1G-FIRST';

                        $fee = $this->services[$sc]['p'];
                        $invoice_lines[$ilidx++] = [
                            'description'             => $this->services[ $sc ]['d'],
                            'ledger_account_id'       => $sageLedgers[ $this->services[$sc]['l'] ],
                            'quantity'                => $cust->companyBillingDetail->getFrequencyAsNumMonths(),
                            'unit_price'              => (string)$fee,
                            'service_id'              => $sageServices[$sc],
                            'unit_price_includes_tax' => false,
                        ];
                        Log::info( "    -     {$invoice_lines[$ilidx-1]['description']} @ {$invoice_lines[$ilidx-1]['unit_price']} x {$invoice_lines[$ilidx-1]['quantity']}" );

                        $lan1_free_applied = true;
                        $first_port_charge_done = true;
                        $eligable_for_lan2_free = true;

                        fputcsv( $fp, [
                            'cust_asn'   => $asn,
                            'cust_name'  => $cust->name,
                            'category'   => 'LAN1',
                            'service'    => $sc,
                            'cost'       => 0,
                        ]);

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
                        'quantity'                => $cust->companyBillingDetail->getFrequencyAsNumMonths(),
                        'unit_price'              => (string)$fee,
                        'service_id'              => $sageServices[$sc],
                    ];
                    Log::info( "    -     {$invoice_lines[$ilidx-1]['description']} @ {$invoice_lines[$ilidx-1]['unit_price']} x {$invoice_lines[$ilidx-1]['quantity']}" );

                    fputcsv( $fp, [
                        'cust_asn'   => $asn,
                        'cust_name'  => $cust->name,
                        'category'   => substr( $sc, 0, 4 ),
                        'service'    => $sc,
                        'cost'       => $fee * $cust->companyBillingDetail->getFrequencyAsNumMonths(),
                    ]);

                    $totals[ $sc ] += $invoice_lines[$ilidx-1]['quantity']*$invoice_lines[$ilidx-1]['unit_price'];

                }
            }

            // EU ?
            if( in_array( $cust->companyBillingDetail->billingCountry, [ 'BE', 'EL', 'LT', 'PT', 'BG', 'ES', 'LU', 'RO', 'CZ', 'FR', 'HU', 'SI', 'DK', 'HR', 'MT', 'SK', 'DE', 'IT', 'NL', 'FI', 'EE', 'CY', 'AT', 'SE', 'LV', 'PL' ] ) ) {

                foreach( $invoice_lines as $i => $il ) {
                    $invoice_lines[ $i ][ 'eu_goods_services_type_id' ] = 'SERVICES';
                    $invoice_lines[$i]['eu_sales_descriptions']     = 'STANDARD';

                    $invoice_lines[ $i ][ 'tax_rate_id' ] = 'IE_ZERO';
                    $invoice_lines[ $i ][ 'tax_amount' ] = '0.00';
                }

                $notes .= 'All supplies are an intra-community supply. ';

                // Northern Ireland
            } else if( in_array( $cust->id, [ 22, 87, 113 ] ) ) {

                foreach( $invoice_lines as $i => $il ) {
                    $invoice_lines[$i]['eu_goods_services_type_id'] = 'SERVICES';

                    $invoice_lines[$i]['tax_rate_id']               = 'IE_ZERO';
                    $invoice_lines[$i]['tax_amount']                = '0.00';
                }

            } else if( $cust->companyBillingDetail->billingCountry == 'IE' ) {

                foreach( $invoice_lines as $i => $il ) {
                    $invoice_lines[ $i ]['eu_goods_services_type_id'] = 'SERVICES';
                    $invoice_lines[ $i ][ 'tax_rate_id' ] = 'IE_STANDARD';
                    $invoice_lines[ $i ][ 'tax_amount' ] = (string)round( ( $invoice_lines[ $i ]['quantity'] * $invoice_lines[ $i ]['unit_price'] * 0.23 ), 2 );
                }

            } else {

                foreach( $invoice_lines as $i => $il ) {
                    $invoice_lines[ $i ]['eu_goods_services_type_id'] = 'SERVICES';
                    $invoice_lines[ $i ][ 'tax_rate_id' ] = 'IE_ZERO';
                    $invoice_lines[ $i ][ 'tax_amount' ] = '0.00';
                }
            }

            $invoice['invoice_lines'] = $invoice_lines;
            $invoice['notes'] = $notes;


            dump($invoice);

//            $guzzle = new \GuzzleHttp\Client();
//
//            $r = $guzzle->post( 'https://api.accounting.sage.com/v3.1/sales_invoices', [
//                    \GuzzleHttp\RequestOptions::JSON => [ 'sales_invoice' => $invoice ],
//                    'headers'                        => [
//                        'Authorization' => 'Bearer ' . $suser->token
//                    ]
//                ]
//            );

            Log::info( "***** END {$cust->name}");

        }

        //             if( in_array( $cust->id, [ 182, 183, 190, 171 ] ) ) {
        // specials
//        fputcsv( $fp, [
//            'cust_asn'   => 39120,
//            'cust_name'  => 'Convergenze [RESOLD]',
//            'category'   => 'MEMBER_FEE',
//            'service'    => 'MEMBER_FEE',
//            'cost'       => 1000,
//        ]);
//        fputcsv( $fp, [
//            'cust_asn'   => 39120,
//            'cust_name'  => 'Convergenze [RESOLD]',
//            'category'   => 'LAN1',
//            'service'    => 'LAN1-1G-FIRST',
//            'cost'       => 0,
//        ]);
//        fputcsv( $fp, [
//            'cust_asn'   => 39120,
//            'cust_name'  => 'Convergenze [RESOLD]',
//            'category'   => 'LAN1',
//            'service'    => 'LAN1-1G-ADDNL',
//            'cost'       => 403,
//        ]);
//
//
//        fputcsv( $fp, [
//            'cust_asn'   => 60501,
//            'cust_name'  => 'Sirius Technology SRL [RESOLD]',
//            'category'   => 'MEMBER_FEE',
//            'service'    => 'MEMBER_FEE',
//            'cost'       => 1000,
//        ]);
//        fputcsv( $fp, [
//            'cust_asn'   => 60501,
//            'cust_name'  => 'Sirius Technology SRL [RESOLD]',
//            'category'   => 'LAN1',
//            'service'    => 'LAN1-1G-FIRST',
//            'cost'       => 0,
//        ]);
//
//        fputcsv( $fp, [
//            'cust_asn'   => 6774,
//            'cust_name'  => 'BICS / Belgacom International Carrier',
//            'category'   => 'MEMBER_FEE',
//            'service'    => 'MEMBER_FEE',
//            'cost'       => 1000,
//        ]);
//        fputcsv( $fp, [
//            'cust_asn'   => 3303,
//            'cust_name'  => 'Swisscom [RESOLD]',
//            'category'   => 'MEMBER_FEE',
//            'service'    => 'MEMBER_FEE',
//            'cost'       => 1000,
//        ]);
//        fputcsv( $fp, [
//            'cust_asn'   => 3303,
//            'cust_name'  => 'Swisscom [RESOLD]',
//            'category'   => 'LAN1',
//            'service'    => 'LAN1-1G-FIRST',
//            'cost'       => 0,
//        ]);
//
//
//        fputcsv( $fp, [
//            'cust_asn'   => 7713,
//            'cust_name'  => 'Telin [RESOLD]',
//            'category'   => 'MEMBER_FEE',
//            'service'    => 'MEMBER_FEE',
//            'cost'       => 1000,
//        ]);
//
//        fputcsv( $fp, [
//            'cust_asn'   => 7713,
//            'cust_name'  => 'Telin [RESOLD]',
//            'category'   => 'LAN1',
//            'service'    => 'LAN1-1G-FIRST',
//            'cost'       => 0,
//        ]);

        fclose( $fp );

        Log::info(json_encode($totals));

        dd($totals);

    }



}

