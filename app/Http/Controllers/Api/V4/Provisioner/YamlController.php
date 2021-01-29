<?php

namespace IXP\Http\Controllers\Api\V4\Provisioner;

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

use Carbon\Carbon;

use Illuminate\Http\Response;

use IXP\Http\Controllers\Api\V4\Controller;

use IXP\Models\{
    Aggregators\SwitcherAggregator,
    CoreBundle,
    Switcher,
    Vlan
};

use IXP\Tasks\Yaml\SwitchConfigurationGenerator;

/**
 * YamlController
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4\Provisioner
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class YamlController extends Controller
{
    /**
     * Generate a formatted output version of the given structure.
     *
     * This takes two arguments: the array structure and the output format.
     *
     * @param array     $array
     * @param string    $format
     *
     * @return Response
     *
     * @throws
     */
    private function structuredResponse( array $array, string $format ): Response
    {
        $output = null;
        $contentType = 'text/plain; charset=utf-8';
        $httpResponse = 200;

        switch ( $format ) {
            case 'yaml':
                $output = yaml_emit( $array, YAML_UTF8_ENCODING );
                break;
            case 'json':
                $output = json_encode($array,
                        JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
                $contentType = 'application/json';
                break;
        }

        if ( !$output ) {
            $httpResponse = 200;
        }

        return response( $output, $httpResponse )->header('Content-Type', $contentType );
    }

    /**
     * Generate a Yaml configuration file for a given switch
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @param Switcher      $switch
     * @param string        $format
     *
     * @return Response
     *
     * @throws
     */
    public function forSwitch( Switcher $switch, string $format ): Response
    {
        return $this->structuredResponse( ( new SwitchConfigurationGenerator( $switch ) )->generate(), $format );
    }

    /**
     * Generate a Yaml configuration file for a given switch
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @param string $switchname
     * @param string $format
     *
     * @return Response
     */
    public function forSwitchByName( string $switchname, string $format ): Response
    {
        if( !( $switch = Switcher::whereName( $switchname )->first()  ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->forSwitch( $switch, $format );
    }

    /**
     * Generate a Yaml file of the vlans for a given switch id
     *
     * This takes one argument: the router handle to generate the vlans for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @param Switcher  $switch
     * @param string    $format
     *
     * @return Response
     *
     * @throws
     */
    public function vlansForSwitch( Switcher $switch, string $format ): Response
    {
        $listVlans['vlans'] = Vlan::selectRaw( 'v.name, v.number as tag, v.private, v.config_name' )
            ->from( 'vlan AS v' )
            ->leftJoin( 'infrastructure AS i', 'i.id', 'v.infrastructureid' )
            ->leftJoin( 'switch AS s', 's.infrastructure', 'i.id' )
            ->where( 's.id', $switch->id )->orderBy( 'config_name' )->get()->toArray();

        return $this->structuredResponse( $listVlans, $format );
    }

    /**
     * Generate a Yaml file of the vlans for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the vlansForSwitch() function.
     *
     * @param string $switchname
     * @param string $format
     *
     * @return Response
     */
    public function vlansForSwitchByName( string $switchname, string $format ): Response
    {
        if( !( $switch = Switcher::whereName( $switchname )->first()  ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->vlansForSwitch( $switch, $format );
    }

    /**
     * Generate a list of switches
     *
     * @param string $format
     *
     * @return Response
     *
     * @throws
     */
    public function listSwitch( string $format ): Response
    {
        $switches = Switcher::selectRaw( 's.name AS name, s.id AS id, i.id AS infrastructure, s.active AS active' )
            ->from( 'switch AS s' )
            ->leftJoin( 'infrastructure AS i', 'i.id', 's.infrastructure' )
            ->orderByRaw( 'i.id, s.id ASC' )->get()->toArray();


        return $this->structuredResponse( [ 'switches' => $switches ] , $format );
    }

    /**
     * Generate a Yaml/JSON response for a switch
     *
     * @param Switcher  $switch
     * @param string    $format
     *
     * @return Response
     *
     * @throws
     */
    public function showSwitch( Switcher $switch, string $format ): Response
    {
        return $this->structuredResponse( $this->showSwitchRestructureOutput( $switch ), $format );
    }

    /**
     * Generate a Yaml/JSON response for a switch
     *
     * @param string        $switchname
     * @param string        $format
     *
     * @return Response
     *
     * @throws
     */
    public function showSwitchByName( string $switchname, string $format ): Response
    {
        if( !( $switch = Switcher::whereName( $switchname )->first()  ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->structuredResponse( $this->showSwitchRestructureOutput( $switch ), $format );
    }

    /**
     * Restructure the output from showSwitch.
     *
     * @param Switcher $switch
     *
     * @return array
     */
    private function showSwitchRestructureOutput( Switcher $switch ): array
    {
        $data = $switch->toArray();
        foreach ( ['name', 'asn', 'hostname', 'loopback_ip', 'loopback_name', 'ipv4addr',
                    'ipv6addr', 'model', 'active', 'os', 'id' ] as $key ) {
            if ( !is_null( $data[ $key ] ) && $data[ $key ] !== '' ) {
                $output[ $key ] = $data[ $key ];
            }
        }

        if( $data[ 'mgmt_mac_address' ] ) { $output[ 'macaddress' ]    = implode(':', str_split( $data[ 'mgmt_mac_address' ], 2 ) ); }
        if( $data[ 'serialNumber' ] )     { $output[ 'serial' ]        = $data[ 'serialNumber' ];     }
        if( $data[ 'lastPolled' ] )       { $output[ 'lastpolled' ]    = Carbon::parse( $data[ 'lastPolled' ] )->format('c'); }
        if( $data[ 'osVersion' ] )        { $output[ 'osversion' ]     = $data[ 'osVersion' ];        }
        if( $data[ 'snmppasswd' ] )       { $output[ 'snmpcommunity' ] = $data[ 'snmppasswd' ];       }

        return array("switch" => $output);
    }


    /**
     * Generate a Yaml file of the core link interfaces for a given switch id
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @param Switcher      $switch
     * @param string        $format
     *
     * @return Response
     *
     * @throws
     */
    public function coreLinkForSwitch( Switcher $switch, string $format ): Response
    {
        $cis = [];
        foreach( [ 'A', 'B' ] as $side ) {
            $listCoreInterface = CoreBundle::selectRaw( "cb.type, cb.ipv4_subnet as cbSubnet, cb.enabled as cbEnabled, 
                        cl.enabled as clEnabled, cb.description, cl.bfd, sp{$side}.name,
                        pi{$side}.speed, pi{$side}.autoneg, cl.ipv4_subnet as clSubnet, s{$side}.id as saId" )
                ->from( 'corebundles AS cb' )
                ->leftJoin( 'corelinks AS cl', 'cl.core_bundle_id', 'cb.id' )
                ->leftJoin( "coreinterfaces AS ci{$side}", "ci{$side}.id", "cl.core_interface_side{$side}_id"  )
                ->leftJoin( "physicalinterface AS pi{$side}", "pi{$side}.id", "ci{$side}.physical_interface_id" )
                ->leftJoin( "switchport AS sp{$side}", "sp{$side}.id", "pi{$side}.switchportid" )
                ->leftJoin( "switch AS s{$side}", "s{$side}.id", "sp{$side}.switchid" )
                ->whereIn( 'cb.type', [ CoreBundle::TYPE_ECMP, CoreBundle::TYPE_L3_LAG ] )
                ->where( "s{$side}.id", $switch->id )->get()->toArray();

            foreach( $listCoreInterface as $ci ) {
                $export = [];
                $subnet = ( $ci[ 'type' ] === CoreBundle::TYPE_ECMP ) ? $ci[ 'clSubnet' ] : $ci[ 'cbSubnet' ];

                $export[ 'ipv4' ]         = SwitcherAggregator::linkAddr( $subnet, $side, true );
                $export[ 'description' ]  = $ci[ 'description' ];
                $export[ 'bfd' ]          = (bool)$ci[ 'bfd' ];
                $export[ 'speed' ]        = $ci[ 'speed' ];
                $export[ 'name' ]         = $ci[ 'name' ];
                $export[ 'autoneg' ]      = (bool)$ci[ 'autoneg' ];
                $export[ 'shutdown' ]     = !( $ci[ 'cbEnabled' ] && $ci[ 'clEnabled' ] );

                $cis[] = $export;
            }
        }

        if( $switch->loopback_ip && $switch->loopback_name ) {
            $ci2['description']  = 'Loopback interface';
            $ci2['loopback']     = true;
            $ci2['ipv4']         = $switch->loopback_ip . '/32';
            $ci2['name']         = $switch->loopback_name;
            $ci2['shutdown']     = false;

            $cis[] = $ci2;
        }

        return $this->structuredResponse( [ 'layer3interfaces' => $cis ] , $format );
    }

    /**
     * Generate a Yaml file of the core link interfaces for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the coreLinkForSwitch() function.
     *
     * @param string    $switchname
     * @param string    $format
     *
     * @return Response
     */
    public function coreLinkForSwitchByName( string $switchname, string $format ): Response
    {
        if( !( $switch = Switcher::whereName( $switchname )->first()  ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->coreLinkForSwitch( $switch, $format );
    }


    /**
     * Generate a Yaml file of the BGP for a given switch id
     *
     * This just takes one argument: the router handle to generate the configuration for. All
     * other parameters are defined by the handle's array in config/router.php.
     *
     * @param Switcher  $switch
     * @param string    $format
     *
     * @return Response
     *
     * @throws
     */
    public function bgpForSwitch( Switcher $switch, string $format ): Response
    {
        $listFlood = Switcher::selectRaw( 'loopback_ip' )
            ->where( 'infrastructure', function( $q ) use( $switch ) {
                $q->selectRaw( 'i.id' )
                    ->from('switch AS s2')
                    ->leftJoin('infrastructure AS i', 'i.id', 's2.infrastructure' )
                    ->where( 's2.id', $switch->id );
            })
            ->whereNotNull( 'loopback_ip' )
            ->where( 'active', true )
            ->where( 'id', '!=', $switch->id )
            ->get()->pluck( 'loopback_ip' )->toArray();

        $listNeighbors = [];
        foreach( SwitcherAggregator::coreBundleNeighbors( $switch ) as $bgp ){
            $side = ( $bgp[ 'sAid' ] === $switch->id ) ? 'B' : 'A';
            $subnet = ( $bgp[ 'type' ] === CoreBundle::TYPE_ECMP ) ? $bgp['clSubnet'] : $bgp['cbSubnet'];
            $listNeighbors[] = [
                'ip'            => SwitcherAggregator::linkAddr( $subnet , $side , false ),
                'description'   => $bgp[ 's' .$side. 'name'],
                'asn'           => $bgp[ 's' .$side. 'asn'],
                'cost'          => $bgp[ 'cost'],
                'preference'    => $bgp[ 'preference'],
            ] ;
        }

        $listAdjacentASNs = [];
        foreach( SwitcherAggregator::coreBundleNeighbors( $switch ) as $bgp ){
            $side = ( $bgp[ 'sAid' ] === $switch->id ) ? 'B' : 'A';
            $remoteAsn = $bgp[ 's' .$side. 'asn'];
            $listAdjacentASNs[ $remoteAsn ] = [
                'description'   => $bgp[ 's' .$side. 'name'],
                'asn'           => $bgp[ 's' .$side. 'asn'],
                'cost'          => $bgp[ 'cost'],
                'preference'    => $bgp[ 'preference'],
            ] ;
        }

        $out[ 'bgp' ][ 'floodlist' ]    = $listFlood;
        $out[ 'bgp' ][ 'adjacentasns' ] = $listAdjacentASNs;
        $out[ 'bgp' ][ 'routerid' ]     = $switch->loopback_ip;
        $out[ 'bgp' ][ 'local_as' ]     = $switch->asn;

        $pgentry = null;
        foreach( $listNeighbors as $neighbor ) {
            $n = [];
            $n[ 'description' ]             = $neighbor[ 'description' ];
            $n[ 'remote_as' ]               = $neighbor[ 'asn' ];
            $n[ 'cost' ]                    = $neighbor[ 'cost' ];
            $n[ 'preference' ]              = $neighbor[ 'preference' ];
            $pgentry[ $neighbor[ 'ip' ] ]   = $n;
        }

        # XXX replace pg-ebgp-ipv4-ixp with dynamic value
        $out[ 'bgp' ][ 'out' ][ 'pg-ebgp-ipv4-ixp' ][ 'neighbors' ] = $pgentry;

        return $this->structuredResponse( $out, $format );
    }

    /**
     * Generate a Yaml file of the BGP for a given switch name
     *
     * This just takes one argument: the router name to generate the configuration for. All
     * other parameters are handled by the coreLinkForSwitch() function.
     *
     * @param string $switchname
     * @param string $format
     *
     * @return Response
     */
    public function bgpForSwitchByName( string $switchname, string $format ): Response
    {
        if( !( $switch = Switcher::whereName( $switchname )->first()  ) ) {
            abort( 404, "Unknown switch" );
        }

        return $this->bgpForSwitch( $switch, $format );
    }

    /**
     * Generate a list of Core Bundles
     *
     * @return Response
     *
     * @throws
     */
    public function listCoreBundle( string $format ): Response
    {
        foreach( CoreBundle::all() as $cb ) {
            $entry = [];

            $entry['id']           	=  $cb->id;
            $entry['description']  	=  $cb->description;
            $entry['graphtitle']   	=  $cb->graph_title;
            $entry['cost'] 	   	    =  $cb->cost;
            $entry['preference']   	=  $cb->preference;
            $entry['enabled'] 	   	=  (bool)$cb->enabled;
            $entry['type'] 	   	    =  $cb->type;
            $entry['switchsidea']  	=  $cb->switchSideX( true )->name;
            $entry['switchsideb']  	=  $cb->switchSideX( false )->name;

            $speed = $cb->corelinks()->count() * $cb->speedPi() * 1000000;

            $entry['bandwidth']    	=  $speed;

            $formats = [ "bits", "K", "M", "G", "T" ];
            $nb = count( $formats );
            for( $i = 0; $i < $nb; $i++ ) {
                if( ( $speed / 1000.0 < 1.0 ) || ( count( $formats ) === $i + 1 ) ) {
                    $prettybandwidth =  round( $speed ) . $formats[ $i ];
                    break;
                }
                $speed /= 1000.0;
            }

            $entry['prettybandwidth']   =  $prettybandwidth;

            $corelinkids = [];
            foreach ($cb->corelinks as $cl) {
                $corelinkids[] = $cl->id;
                $entry['infrastructure']   = $cl->coreInterfaceSideA->physicalInterface->switchPort->switcher->infrastructure;
            }
            $entry['corelinks']   =  $corelinkids;

            $corebundles['corebundles'][] = $entry;
        }

        return $this->structuredResponse( $corebundles, $format );
    }

}
