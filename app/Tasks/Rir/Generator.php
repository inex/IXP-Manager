<?php

namespace IXP\Tasks\Rir;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Illuminate\Support\Facades\View as ViewFacade;

use IXP\Exceptions\GeneralException;

use IXP\Models\{
    Customer,
    Router,
    Vlan
};

/**
 * RIR Object Generator
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Router
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Generator
{
    /**
     * Template chosen
     *
     * @var string
     */
    private $tmpl = "";

    /**
     * Generator constructor.
     *
     * @param string $tmpl
     */
    public function __construct( string $tmpl )
    {
        $this->setTemplate( $tmpl );
    }

    /**
     * Set the template name
     *
     * @param string $tmpl
     *
     * @return Generator
     */
    public function setTemplate( string $tmpl ): Generator
    {
        $this->tmpl = $tmpl;
        return $this;
    }

    /**
     * Get the template name
     *
     * @return string
     */
    public function template(): string
    {
        return $this->tmpl;
    }

    /**
     * Generate the RIR object
     *
     * @return string
     *
     * @throws
     */
    public function generate(): string
    {
        if( !( $this->template() ) ) {
            throw new GeneralException( 'You must specify a template name so you can generate a RIR object' );
        }

        // sanitise template name
        $tmpl = preg_replace( '/[^0-9a-z_\-]/', '', strtolower( $this->template() ) );

        if( !ViewFacade::exists ( 'api/v4/rir/' . $tmpl ) ) {
            throw new GeneralException("Unknown RIR object template provided - api/v4/rir/{$tmpl}" );
        }

        // populate the template variables
        $customers =  Customer::getConnected( false, false, 'autsys' )->keyBy( 'id' );

        return view( 'api/v4/rir/' . $tmpl, [
                'customers'     => $customers,
                'asns'          => $this->generateASNs( $customers ),
                "rsclients"     => $this->generateRouteServerClientDetails( $customers ),
                "protocols"     => [ 4, 6 ]
            ] )->render();
    }

    /**
     * Gather and create the IXP customer ASN details.
     *
     * Returns an associate array indexed by ordered ASNs of active external trafficking customers:
     *
     *     [
     *         [65500] => [
     *                        ['name']    => Customer Name
     *                        ['asmacro'] => AS-CUSTOMER
     *                    ],
     *                    ...
     *     ]
     *
     * @param   Collection $customers Array of all active external trafficking customers
     *
     * @return array Associate array indexed by ordered ASNs
     *
     * @throws
     */
    private function generateASNs( Collection $customers ): array
    {
        $asns = [];
        foreach( $customers as $c ) {
            /** @var $c Customer */
            $asns[ $c->autsys ] = [
                'asmacro' => $c->asMacro( 4, 'AS' ),
                'name'    => $c->name
            ];
        }

        ksort( $asns, SORT_NUMERIC );

        return $asns;
    }

    /**
     * Gather up route server client information for building RIR objects
     *
     * Returns an array of the form:
     *
     *     [
     *         [ vlans ] => [
     *             [ $vlanid ] => [
     *                 [servers] => [   // route server IP addresses by protocol
     *                     [4] => [
     *                         [0] => 193.242.111.8
     *                         ...
     *                     ],
     *                     [6] => [
     *                         ...
     *                     ]
     *                 ]
     *             ],
     *             [ $another_vlanid ] => [
     *                 ...
     *             ],
     *             ...
     *         ],
     *         [clients] => [
     *             [$customer_asn] => [
     *                 [id] => customer id,
     *                 [ vlans ] => [
     *                     [ vlanid ] => [
     *                         [$vlan_interface_id] => [    // customer's IP addresses by protocol
     *                             [4] => 193.242.111.xx
     *                             [6] => 2001:7f8:18::xx
     *                         ],
     *                         ...   // if the user has more than one VLAN interface on this VLAN
     *                     ],
     *                     ...
     *                 ],
     *             ],
     *         ],
     *     ]
     *
     * @param Collection $customers
     *
     * @return array As defined above
     *
     * @throws
     */
    private function generateRouteServerClientDetails( Collection $customers ): array
    {
        // get the public peering VLANs
        $vlans = Vlan::publicOnly()->orderBy( 'number' )->get();

        $rsclients = [];

        foreach( $vlans as $vlan ) {
            foreach( [ 4, 6 ] as $proto ) {
                // get the available route servers
                $servers = Router::routeServer()->where( 'vlan_id', $vlan->id )
                    ->where( 'protocol', 4 )->get()->pluck( 'peering_ip' )->toArray();

                if( !count( $servers ) ){
                    continue;
                }

                $rsclients[ 'vlans' ][ $vlan->id ][ 'servers' ][ $proto ] = [];

                foreach( $servers as $server ){
                    $rsclients[ 'vlans' ][ $vlan->id ][ 'servers' ][ $proto ][] = $server;
                }

                foreach( $vlan->vlanInterfaces as $vli ) {
                    if( !$vli->rsclient ){
                        continue;
                    }

                    $oneConnectedInterface = false;

                    foreach( $vli->virtualInterface->physicalInterfaces as $pi ) {
                        if( $pi->statusConnected() ) {
                            $oneConnectedInterface = true;
                            break;
                        }
                    }

                    if( !$oneConnectedInterface ){
                        continue;
                    }

                    $cust = $vli->virtualInterface->customer;

                    if( !$cust->statusNormal() ){
                        continue;
                    }

                    // Customer still active?
                    if( !isset( $customers[ $cust->id ] ) ){
                        continue;
                    }

                    if( !isset( $rsclients[ 'clients' ][ $cust->autsys ] ) ) {
                        $rsclients[ 'clients' ][ $cust->autsys ][ 'id' ]       = $cust->id;
                        $rsclients[ 'clients' ][ $cust->autsys ][ 'vlans' ]    = [];
                    }

                    $fnEnabled = "ipv{$proto}enabled";

                    if( $vli->$fnEnabled ) {
                        $fnIpaddress = "ipv{$proto}address";
                        $rsclients[ 'clients' ][ $cust->autsys ][ 'vlans' ][ $vlan->id ][ $vli->id ][ $proto ] = $vli->$fnIpaddress->address;
                    }
                }
            }
        }

        if( isset( $rsclients[ 'clients' ] ) ){
            ksort( $rsclients[ 'clients' ], SORT_NUMERIC );
        }

        return $rsclients;
    }
}