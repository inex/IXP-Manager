<?php

namespace IXP\Http\Controllers;

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

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Http\{
    RedirectResponse, Request
};

use Illuminate\View\View;

use IXP\Models\{
    Contact,
    Customer,
    CustomerToUser,
    PatchPanelPort,
    RsPrefix,
    User,
    VirtualInterface,
    VlanInterface};

/**
 * Search Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SearchController extends Controller
{
    /**
     * Search different type of objects ( IP, User, Mac address)
     *
     * @param   Request $r instance of the current HTTP request
     *
     * @return  RedirectResponse|View
     */
    public function do( Request $r ): RedirectResponse|View
    {
        $type       = '';
        $results    = $interfaces = [];

        if( $search = trim( trim( htmlspecialchars( $r->search ) ), '%' ) ) {
            // what kind of search are we doing?
            if( preg_match( '/^PPP\-(\d+)$/', $search, $matches ) ) {
                // patch panel port search
                if( $ppp = PatchPanelPort::find( $matches[1] ) ) {
                    return redirect( route( 'patch-panel-port@view', [ 'ppp' => $ppp->id ] ) );
                }
            }
            else if( preg_match( '/^xc:\s*(.*)\s*$/', $search, $matches ) ) {
                // patch panel x-connect ID search
                // wild card search
                $type = 'ppp-xc';
                $results = PatchPanelPort::where( function( $query ) use( $matches ){
                    $query->where( 'colo_circuit_ref', 'LIKE', '%' . $matches[1] . '%'  )
                        ->orWhere( 'colo_billing_ref', 'LIKE', '%' . $matches[1] . '%' );
                } )->orderByRaw( 'id ASC' )
                ->with( [ 'patchPanel.cabinet.location', 'customer' ] )
                ->get();

                if( count( $results ) === 1 ) {
                    return redirect( route( 'patch-panel-port@view', [ 'ppp' => $results[0]->id ] ) );
                }
            }
            else if( preg_match( '/^\.\d{1,3}$/', $search ) || preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $search ) ) {
                // IPv4 search
                $type   = 'ipv4';
                $result = VlanInterface::leftJoin( 'ipv4address AS ip', 'ip.id', 'vlaninterface.ipv4addressid' )
                    ->where( 'ip.address', 'LIKE', strtolower( '%' . $search ) )
                    ->with( 'virtualInterface.customer' )->get();

                $ips        = $this->processIPSearch( $result );
                $results    = $ips[ 'results' ];
                $interfaces = $ips[ 'interfaces' ];
            }
            else if( preg_match( '/^[a-f0-9]{12}$/', strtolower( $search ) ) || preg_match( '/^[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}$/', strtolower( $search ) ) ) {
                // mac address search
                $type   = 'mac';
                $search = preg_replace( '/[^a-f0-9]/', '', strtolower( $search ) );

                $vis = VirtualInterface::select( 'virtualinterface.*' )
                    ->leftJoin( 'macaddress', 'macaddress.virtualinterfaceid', 'virtualinterface.id' )
                    ->where( 'macaddress.mac', $search )
                    ->distinct()
                    ->with( 'customer' )->get();

                $vlis = VlanInterface::select( 'vlaninterface.*' )
                    ->leftJoin( 'l2address', 'l2address.vlan_interface_id', 'vlaninterface.id' )
                    ->where( 'l2address.mac', $search )
                    ->with( 'virtualInterface.customer' )->get();

                $discoveredMACs = $this->processMACSearch( $vis );
                $configuredMACs = $this->processMACSearch( $vlis );
                $macs           = $this->mergeMacs( $discoveredMACs, $configuredMACs );
                $results        = $macs[ 'results' ];
                $interfaces     = $macs[ 'interfaces' ];
            }
            else if( preg_match( '/^:[0-9a-fA-F]{1,4}$/', $search ) || preg_match( '/^[0-9a-fA-F]{1,4}:.*:[0-9a-fA-F]{1,4}$/', $search ) ) {
                // IPv6 search
                $type = 'ipv6';

                $result = VlanInterface::select( 'vlaninterface.*' )
                    ->leftJoin( 'ipv6address AS ip', 'ip.id', 'vlaninterface.ipv6addressid' )
                    ->where( 'ip.address', 'LIKE', strtolower( '%' . $search ) )
                    ->with( 'ipv4address' , 'ipv6address' )->get();

                $ips        =  $this->processIPSearch( $result );
                $results    = $ips[ 'results' ];
                $interfaces = $ips[ 'interfaces' ];
            }
            else if( preg_match( '/^as(\d+)$/', strtolower( $search ), $matches ) || preg_match( '/^(\d+)$/', $search, $matches ) ) {
                // user by ASN search
                $type       = 'asn';
                $results    =  Customer::where('autsys', $matches[1] )->get();
            }
            else if( preg_match( '/^AS-(.*)$/', strtoupper( $search ) ) ) {
                // user by ASN macro search
                $type       = 'asmacro';
                $results    = Customer::where( 'peeringmacro', $search )
                    ->orWhere( 'peeringmacrov6', $search )->get();
            }
            else if( preg_match( '/^@([a-zA-Z0-9]+)$/', $search, $matches ) ) {
                // user by username search
                $type = 'username';
                $results[ 'users' ] = User::where( 'username', 'LIKE' , '%' . $matches[1] . '%' )
                    ->with( 'customers' )->get();
            }
            else if( filter_var( $search, FILTER_VALIDATE_EMAIL ) !== false ) {
                // user by email search
                $type = 'email';
                $results[ 'users' ]     = User::where( 'email', $search )
                    ->with( 'customers' )->get();
                $results[ 'contacts' ]  = Contact::where( 'email', $search )->get();
            }
            else if( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}$/', $search ) || preg_match( '/^[0-9a-fA-F]{1,4}:.*:[0-9a-fA-F]{0,4}\/\d{1,3}$/', $search ) ) {
                // rsprefix search
                $type       = 'rsprefix';
                $results    = RsPrefix::wherePrefix( $search )->with( 'customer' )->get();
            }
            else {
                // wild card search
                $type       = 'cust_wild';
                $wildsearch = '%' . $search . '%';
                $results    = Customer::select( 'cust.*' )
                    ->leftJoin( 'company_registration_detail AS r', 'r.id', 'cust.company_registered_detail_id' )
                    ->where( 'cust.name', 'LIKE' , $wildsearch )->orWhere( 'cust.shortname', 'LIKE' , $wildsearch )
                    ->orWhere( 'cust.abbreviatedName', 'LIKE' , $wildsearch )->orWhere( 'r.registeredName', 'LIKE' , $wildsearch )
                    ->orderBy( 'cust.name' )->get();
            }
        }

        return view( 'search/do' )->with([
            'results'           => $results,
            'interfaces'        => $interfaces,
            'type'              => $type,
            'search'            => $search
        ]);
    }

    /**
     * Process the IP search (IPv4 and IPv6)
     *
     * @param Collection $vlis vlan interfaces list
     *
     * @return  array array composed of the the result (customer) and the interface (vlan interfaces)
     */
    private function processIpSearch( Collection $vlis ): array
    {
        $results = $interfaces = [];
        foreach( $vlis as $vli ) {
            /** @var $vli VlanInterface */
            $results[ $vli->virtualInterface->custid ] = $vli->virtualInterface->customer;
            $interfaces[ $vli->virtualInterface->custid ][] = $vli;
        }
        return [ 'results' => $results, 'interfaces' => $interfaces ];
    }

    /**
     * Process the mac address search
     *
     * @param Collection|null  $is virtual interfaces list
     *
     * @return  array array composed of the the result (customer) and the interface (vlan interfaces)
     */
    private function processMACSearch( Collection $is = null ): array
    {
        $results = $interfaces = [];

        foreach( $is as $i ) {
            if( $i instanceof VlanInterface ) {
                $c  = $i->virtualInterface->customer;
                $vi = $i->virtualInterface;
            } else {
                $c  = $i->customer;
                $vi = $i;
            }

            $results[ $c->id ]      = $c;
            $interfaces[ $c->id ][] = $vi;
        }
        return [ 'results' => $results, 'interfaces' => $interfaces ];
    }

    /**
     * Merge configured and discovered mac address results
     *
     * @param array $discovered
     * @param array $configured
     *
     * @return array
     */
    private function mergeMacs( array $discovered, array $configured ): array
    {
        $results    = $interfaces = [];

        foreach( [ $discovered, $configured ] as $a ) {
            foreach( $a['results'] as $cid => $c ) {
                if( !isset( $results[ $cid ] ) ) {
                    $results[ $cid ] = $c;
                }
            }

            foreach( $a['interfaces'] as $viid => $vi ) {
                if( !isset( $interfaces[ $viid ] ) ) {
                    $interfaces[ $viid ] = $vi;
                }
            }
        }

        return [ 'results' => $results, 'interfaces' => $interfaces ];
    }
}