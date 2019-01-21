<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM, Redirect;

use Illuminate\View\View;

use Entities\{
    Customer         as   CustomerEntity,
    Contact          as   ContactEntity,
    IPv4Address      as   IPv4AddressEntity,
    IPv6Address      as   IPv6AddressEntity,
    Layer2Address    as   Layer2AddressEntity,
    MACAddress       as   MACAddressEntity,
    PatchPanelPort   as   PatchPanelPortEntity,
    RSPrefix         as   RSPrefixEntity,
    User             as   UserEntity,
    VlanInterface    as   VlanInterfaceEntity
};

use Illuminate\Http\{
    RedirectResponse, Request
};



/**
 * Search Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SearchController extends Controller {

    /**
     * Search different type of objects ( IP, User, Mac address)
     *
     * @param   Request $request instance of the current HTTP request
     * @return  RedirectResponse|View
     */
    public function do( Request $request ) {
        $type       = '';
        $results    = [];
        $interfaces = [];

        if( $search = trim( htmlspecialchars( $request->input( 'search' ) ) ) ) {

            // what kind of search are we doing?
            if( preg_match( '/^PPP\-(\d+)$/', $search, $matches ) ) {
                // patch panel port search
                if( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $matches[1] ) ) {
                    return Redirect::to( route( 'patch-panel-port@view', [ 'id' => $ppp->getId() ] ) );
                }
            }
            else if( preg_match( '/^xc:\s*(.*)\s*$/', $search, $matches ) ) {
                // patch panel x-connect ID search
                // wild card search
                $type = 'ppp-xc';
                $results = D2EM::getRepository( PatchPanelPortEntity::class )->findByColoRefs( $matches[1] );

                if( count( $results ) === 1 ) {
                    return Redirect::to( route( 'patch-panel-port@view', [ 'id' => $results[0]->getId() ] ) );
                }
            }
            else if( preg_match( '/^\.\d{1,3}$/', $search ) || preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $search ) ) {
                // IPv4 search
                $type = 'ipv4';
                $ips = $this->processIPSearch( D2EM::getRepository( IPv4AddressEntity::class )->findVlanInterfaces( $search ) );
                $results = $ips[ 'results' ];
                $interfaces = $ips[ 'interfaces' ];
            }
            else if( preg_match( '/^[a-f0-9]{12}$/', strtolower( $search ) ) || preg_match( '/^[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}$/', strtolower( $search ) ) ) {
                // mac address search
                $type = 'mac';
                $discoveredMACs = $this->processMACSearch( D2EM::getRepository( MACAddressEntity::class )->findVirtualInterface( $search ) );
                $configuredMACs = $this->processMACSearch( D2EM::getRepository( Layer2AddressEntity::class )->findVlanInterface( $search ) );
                $macs = $this->mergeMacs( $discoveredMACs, $configuredMACs );
                $results    = $macs[ 'results' ];
                $interfaces = $macs[ 'interfaces' ];
            }
            else if( preg_match( '/^:[0-9a-fA-F]{1,4}$/', $search ) || preg_match( '/^[0-9a-fA-F]{1,4}:.*:[0-9a-fA-F]{1,4}$/', $search ) ) {
                // IPv6 search
                $type = 'ipv6';
                $ips =  $this->processIPSearch( D2EM::getRepository( IPv6AddressEntity::class )->findVlanInterfaces( $search ) );
                $results = $ips[ 'results' ];
                $interfaces = $ips[ 'interfaces' ];
            }
            else if( preg_match( '/^as(\d+)$/', strtolower( $search ), $matches ) || preg_match( '/^(\d+)$/', $search, $matches ) ) {
                // user by ASN search
                $type = 'asn';
                $results = D2EM::getRepository( CustomerEntity::class )->findByASN( $matches[1] );
            }
            else if( preg_match( '/^AS-(.*)$/', strtoupper( $search ) ) ) {
                // user by ASN macro search
                $type = 'asmacro';
                $results = D2EM::getRepository( CustomerEntity::class )->findByASMacro( $search );
            }
            else if( preg_match( '/^@([a-zA-Z0-9]+)$/', $search, $matches ) ) {
                // user by username search
                $type = 'username';
                $results = D2EM::getRepository( UserEntity::class )->findByUsername( $matches[1] . '%' );
            }
            else if( filter_var( $search, FILTER_VALIDATE_EMAIL ) !== false ) {
                // user by email search
                $type = 'email';

                $results[ 'users' ]     = D2EM::getRepository( UserEntity::class    )->findByEmail( $search );
                $results[ 'contacts' ]  = D2EM::getRepository( ContactEntity::class )->findByEmail( $search );
            }
            else if( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}$/', $search ) || preg_match( '/^[0-9a-fA-F]{1,4}:.*:[0-9a-fA-F]{0,4}\/\d{1,3}$/', $search ) ) {
                // rsprefix search
                $type = 'rsprefix';
                $results = D2EM::getRepository( RSPrefixEntity::class )->findBy( [ 'prefix' => $search ] );
            }
            else {
                // wild card search
                $type = 'cust_wild';
                $results = D2EM::getRepository( CustomerEntity::class )->findWild( $search );
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
     * @param   array $vlis vlan interfaces list
     * @return  array array composed of the the result (customer) and the interface (vlan interfaces)
     */
    private function processIpSearch( array $vlis = [] ) {
        $results = [];
        $interfaces = [];
        foreach( $vlis as $vli ) {
            $results[ $vli->getVirtualInterface()->getCustomer()->getId() ] = $vli->getVirtualInterface()->getCustomer();
            $interfaces[ $vli->getVirtualInterface()->getCustomer()->getId() ][] = $vli;
        }

        return [ 'results' => $results, 'interfaces' => $interfaces ];
    }

    /**
     * Process the mac address search
     *
     * @param   array $is virtual interfaces list
     *
     * @return  array array composed of the the result (customer) and the interface (vlan interfaces)
     */
    private function processMACSearch( array $is = [] ) {
        $results = [];
        $interfaces = [];

        foreach( $is as $i ) {

            if( $i instanceof VlanInterfaceEntity ) {
                $c = $i->getVirtualInterface()->getCustomer();
            } else {
                $c = $i->getCustomer();
            }

            $results[ $c->getId()    ]   = $c;
            $interfaces[ $c->getId() ][] = $i instanceof VlanInterfaceEntity ? $i->getVirtualInterface() : $i;
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
    private function mergeMacs( array $discovered, array $configured ): array {
        $results    = [];
        $interfaces = [];

        foreach( [ $discovered, $configured ] as $a ) {
            foreach( $a['results'] as $cid => $c ) {
                if( !isset( $results[$cid] ) ) {
                    $results[ $cid ] = $c;
                }
            }

            foreach( $a['interfaces'] as $viid => $vi ) {
                if( !isset( $interfaces[$viid] ) ) {
                    $interfaces[ $viid ] = $vi;
                }
            }
        }

        return [ 'results' => $results, 'interfaces' => $interfaces ];
    }
}
