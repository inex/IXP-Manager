<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Entities\{
    BgpSession                  as BgpSessionEntity,
    Vlan                        as VlanEntity
};

use Illuminate\Http\{
    RedirectResponse
};

use Illuminate\View\View;

use Illuminate\Http\{
    Request
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use D2EM, Redirect;

/**
 * PeeringMatrixController Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringMatrixController extends Controller
{

    /**
     * Display dashboard
     *
     * @param   Request $r
     *
     * @return  View|RedirectResponse
     *
     * @throws
     */
    public function index( Request $r ) {

        if( config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ) {
            AlertContainer::push( 'The peering matrix has been disabled.', Alert::DANGER );
            return Redirect::to('');
        }

        if( !ixp_min_auth( config( 'ixp.peering-matrix.min-auth' ) ) ) {
            AlertContainer::push( 'You do not have the required privileges to access the peering matrix', Alert::DANGER );
            return Redirect::to('');
        }

        $protos = [
            4 => 'IPv4',
            6 => 'IPv6'
        ];

        if( $r->input( 'vlan' )  !== null ) {
            /** @var VlanEntity $s */
            if(  $vlan = D2EM::getRepository( VlanEntity::class )->find( $r->input( 'vlan' ) ) ) {
                $vl = $vlan->getId();
                $r->session()->put( "peering-matrix-vlan", $vl );
            } else {
                $r->session()->remove( "peering-matrix-vlan" );
                $vl = false;
            }
        } else if( $r->session()->exists( "peering-matrix-vlan" ) ) {
            $vl = $r->session()->get( "peering-matrix-vlan" );
        } else {
            $vl = config( "identity.vlans.default" );
        }


        if( $r->input( 'proto' )  !== null ) {
            if( array_key_exists( $r->input( 'proto' ) , $protos ) ) {
                $proto = $r->input( 'proto' );
                $r->session()->put( "peering-matrix-proto", $proto );
            } else {
                $r->session()->remove( "peering-matrix-proto" );
                $proto = 4;
            }
        } else if( $r->session()->exists( "peering-matrix-proto" ) ) {
            $proto = $r->session()->get( "peering-matrix-proto" );
        } else {
            $proto = 4;
        }


        if( !count( ( $vlans = D2EM::getRepository( VlanEntity::class )->getPeeringMatrixVLANs() ) ) ) {

            AlertContainer::push( 'No VLANs have been enabled for the peering matrix. Please see <a href="'
                . 'https://github.com/inex/IXP-Manager/wiki/Peering-Matrix">these instructions</a>'
                . ' / contact our support team.', Alert::DANGER );

            return Redirect::to( '');
        }


        if( !isset( $vlans[ $vl ] ) ){
            $vl = config( "identity.vlans.default", null );

            if( !isset( $vlans[ $vl ] ) ) {
                AlertContainer::push( 'There is no default VLAN set for the peering matrix. Please '
                    . 'set <code>IDENTITY_DEFAULT_VLAN</code> in your <code>.env</code> file to a valid DB ID '
                    . 'of the VLAN you would like the peering matrix to show by default.', Alert::DANGER );

                return Redirect::to( '');
            }
        }

        $cust = D2EM::getRepository( VlanEntity::class   )->getCustomers( $vl, $proto );
        $asns = array_keys( $cust );

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'peering-matrix/index' )->with([
            'sessions'                      => D2EM::getRepository( BgpSessionEntity::class       )->getPeers( $vl, $proto ),
            'custs'                         => $cust,
            'vlans'                         => $vlans,
            'protos'                        => $protos,
            'proto'                         => $proto,
            'vl'                            => $vl,
            'asnStringFormat'               => count($asns) > 0 ? "% " . strlen( $asns[ count( $asns ) - 1 ] ) . "s" : "% 0s",
        ]);
    }



}