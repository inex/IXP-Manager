<?php

namespace IXP\Utils\Export;

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

use Illuminate\Support\Facades\Auth;
use stdClass;

use IXP\Exceptions\Utils\ExportException;

use IXP\Models\{
    Customer,
    Infrastructure,
    NetworkInfo,
    Router
};

use Log;

/**
 * JSON Schema Exporter
 *
 * Usage:
 *
 *     $jexport = new \IXP\Utils\Export\JsonSchema;
 *     $json_schema = $jexport->get( \IXP\Utils\Export\JsonSchema::EUROIX_JSON_LATEST );
 *
 * @see        https://github.com/euro-ix/json-schemas
 * @author     Nick Hilliard <nick@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class JsonSchema
{
    // Supported versions:
    // ended 201705: const EUROIX_JSON_VERSION_0_3 = "0.3";
    // ended 201705: const EUROIX_JSON_VERSION_0_4 = "0.4";
    // ended 201705: const EUROIX_JSON_VERSION_0_5 = "0.5";
    public const EUROIX_JSON_VERSION_0_6 = "0.6";
    public const EUROIX_JSON_VERSION_0_7 = "0.7";
    public const EUROIX_JSON_VERSION_1_0 = "1.0";
    // adding a new version? update sanitiseVersion() below also!

    public const EUROIX_JSON_LATEST = self::EUROIX_JSON_VERSION_1_0;

    public const EUROIX_JSON_VERSIONS = [
        self::EUROIX_JSON_VERSION_0_6,
        self::EUROIX_JSON_VERSION_0_7,
        self::EUROIX_JSON_VERSION_1_0,
    ];
    
    /**
     * Get the JSON schema (for a given version or for the latest version)
     *
     * @param string|null   $version    The version to get (or, if null / not present then the latest)
     * @param bool          $asArray    Do not convert to JSON but rather return the PHP array
     * @param bool          $detailed   Create the very detailed version (usually for logged in users)
     * @param bool          $tags       Include customer tags
     * @return string|array
     * @throws
     */
    public function get( string $version = null, $asArray = false, $detailed = true, $tags = false )
    {
        if( $version === null ) {
            $version = self::EUROIX_JSON_LATEST;
        } else {
            $version = $this->sanitiseVersion( $version );
        }

        $output = [
            'version' => $version,
            'generator' => 'IXP Manager v' . APPLICATION_VERSION,
        ];

        // normalise times to UTC for exports
        date_default_timezone_set('UTC');
        $output['timestamp'] = now()->toIso8601ZuluString();

        $output['ixp_list']    = $this->getIXPInfo( $version );
        $output['member_list'] = $this->getMemberInfo( $version, $detailed, $tags );

        // apply filters as some IXs don't want to export all details
        $output = $this->filter($output);

        if( $asArray ) {
            return $output;
        }

        return json_encode( $output, JSON_PRETTY_PRINT )."\n";
    }

    /**
     * Ensure a given version exists or default to latest
     *
     * @param string $version Version string to sanitise
     *
     * @return string Sanitised version
     */
    public function sanitiseVersion( string $version ): string
    {
        if( in_array( $version, self::EUROIX_JSON_VERSIONS ) ) {
            return $version;
        }

        return self::EUROIX_JSON_LATEST;
    }

    /**
     * Collate the IXP specific information for the JSON schema export
     *
     * @param string $version The version to collate the detail for
     *
     * @return array
     *
     * @throws
     */
    private function getIXPInfo( string $version ): array
    {
        $ixpinfo = [];

        foreach( Infrastructure::with( ['switchers.cabinet.location'] )->get() as $infra ) {
            $i = [];
            $i['shortname'] = $infra->name;
            $i['name'] = config('identity.legalname');
            $i['country'] = $infra->country ?? config('identity.location.country');
            $i['url'] = config('identity.corporate_url');

            if( $infra->peeringdb_ix_id ) {
                $i[ 'peeringdb_id' ] = (int)$infra->peeringdb_ix_id;
            }

            if( $infra->ixf_ix_id ) {
                $i[ 'ixf_id' ] = (int)$infra->ixf_ix_id;
            } else if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {

                // The IX-F ID is officially required for >= v0.7 of the schema.
                // This shouldn't prevent the IX_F exporter from working though if someone wishes to pull the
                // information regardless of that being set.
                //
                // Two options for this:

                // first pass an ixfid for **every** infrastructure that does not have one
                // e.g. http://ixp-inex.ldev/api/v4/member-export/ixf/1.0?ixfid_1=30&ixfid_2=31&ixfid_3=30
                if( request('ixfid_' . $infra->id, false ) ) {
                    $i[ 'ixf_id' ] = (int)request( 'ixfid_' . $infra->id );
                }

                // second, just ignore it and set it to zero:
                // http://ixp-inex.ldev/api/v4/member-export/ixf/1.0?ignore_missing_ixfid=1
                else if( request('ignore_missing_ixfid', false ) ) {
                    $i[ 'ixf_id' ] = 0;
                }

                // by default, we will throw an exception:
                else {
                    throw new ExportException( "The IX-F ID is a required parameter for IX-F Export Schema >=v0.7. Set this in IXP Manager in the 'Infrastructures' management page." );
                }
            }

            $i['ixp_id'] = $infra->id;    // referenced in member's connections section

            $i['support_email'] = config('identity.support_email');
            $i['support_phone'] = config('identity.support_phone');
            $i['support_contact_hours'] = config('identity.support_hours');

            // $infra['stats_api'] = FIXME;
            $i['emergency_email'] = config('identity.support_email');
            $i['emergency_phone'] = config('identity.support_phone');
            $i['emergency_contact_hours'] = config('identity.support_hours');
            $i['billing_contact_hours'] = config('identity.billing_hours');
            $i['billing_email'] = config('identity.billing_email');
            $i['billing_phone'] = config('identity.billing_phone');

            $i['peering_policy_list'] = array_values( Customer::$PEERING_POLICIES);

            $result = NetworkInfo::leftJoin( 'vlan', 'vlan.id', 'networkinfo.vlanid' )
                ->where( 'vlan.infrastructureid', $infra->id )
                ->get()->toArray();

            $vlanentry = [];
            foreach( $result as $ni )
            {
                $id = $ni['id'];
                $vlanentry[$id]['id']                                   = $ni['id'];
                $vlanentry[$id]['name']                                 = $ni['name'];
                $vlanentry[$id][ 'ipv'.$ni['protocol'] ]['prefix']      = $ni[ 'network' ];
                $vlanentry[$id][ 'ipv'.$ni['protocol'] ]['mask_length'] = $ni[ 'masklen' ];
            }

            $data = [];
            foreach( array_keys( $vlanentry ) as $id ) {
                $data[] = $vlanentry[ $id ];
            }

            $i['vlan'] = $data;

            if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
                if( !config( 'ixp_fe.frontend.disabled.lg' ) ) {
                    foreach( $i[ 'vlan' ] as $idx => $vlan ) {
                        if( isset( $i[ 'vlan' ][ $idx ][ 'ipv4' ] ) ) {
                            $i[ 'vlan' ][ $idx ][ 'ipv4' ][ 'looking_glass_urls' ][] = url( '/lg' );
                        }
                        if( isset( $i[ 'vlan' ][ $idx ][ 'ipv6' ] ) ) {
                            $i[ 'vlan' ][ $idx ][ 'ipv6' ][ 'looking_glass_urls' ][] = url( '/lg' );
                        }
                    }
                }
            }

            $i['switch'] = $this->getSwitchInfo( $version, $infra );

            $ixpinfo[] = $i;
        }

        return $ixpinfo;
    }

    /**
     * Collate the IXP's switch information for the JSON schema export
     *
     * @param string            $version
     * @param Infrastructure    $infra
     *
     * @return array
     */
    private function getSwitchInfo( string $version, Infrastructure $infra ): array
    {
        $data = [];

        foreach( $infra->switchers as $switch ) {
            if( !$switch->active ) {
                continue;
            }

            $switchentry = [];
            $switchentry['id']      = $switch->id;
            $switchentry['name']    = $switch->name;
            $switchentry['colo']    = $switch->cabinet->location->name;
            $switchentry['city']    = $switch->cabinet->location->city ?? config( 'identity.location.city'    );
            $switchentry['country'] = $switch->cabinet->location->country ?? config( 'identity.location.country' );

            if( $switch->cabinet->location->pdb_facility_id ) {
                $switchentry['pdb_facility_id'] = (int)$switch->cabinet->location->pdb_facility_id;
            }

            if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
                $switchentry['manufacturer'] = $switch->vendor->name;
                $switchentry['model']        = $switch->model;
            }

            if( $version >= self::EUROIX_JSON_VERSION_1_0 ) {
                $switchentry['software'] = trim( ( $switch->os ?? '' ) . ' ' . ( $switch->osVersion ?? '' ) );
            }

            $data[] = $switchentry;
        }

        return $data;
    }

    /**
     * Collate the IXP's member information for the JSON schema export
     *
     * @param string $version The version to collate the detail for
     * @param bool $detailed
     * @param bool $tags
     *
     * @return array
     */
    private function getMemberInfo( string $version, bool $detailed, bool $tags ): array
    {
        $memberinfo = [];

        if( $version === self::EUROIX_JSON_VERSION_0_7 ) {
            $routeServerIPs = [];
            Router::where( 'type', Router::TYPE_ROUTE_SERVER )
                ->get()->map( function($item) use(&$routeServerIPs) {
                    $routeServerIPs[$item->vlan_id][] = $item->peering_ip;
                });

            $routeCollectorIPs = [];
            Router::select( [ 'vlan_id', 'peering_ip' ] )
                ->where( 'type', Router::TYPE_ROUTE_COLLECTOR )
                ->get()->map( function($item) use(&$routeCollectorIPs) {
                    $routeCollectorIPs[$item->vlan_id][] = $item->peering_ip;
                });
        }

        if( $version === self::EUROIX_JSON_VERSION_1_0 ) {
            $routeServersByIps = [];
            Router::where( 'type', Router::TYPE_ROUTE_SERVER )
                ->get()->map( function($item) use(&$routeServersByIps) {
                    $routeServersByIps[ $item->vlan_id ][ $item->peering_ip ] = $item;
                });

            $routeCollectorsByIps = [];
            Router::where( 'type', Router::TYPE_ROUTE_COLLECTOR )
                ->get()->map( function($item) use(&$routeCollectorsByIps) {
                    $routeCollectorsByIps[ $item->vlan_id ][ $item->peering_ip ] = $item;
                });
        }

        $customers =  Customer::getConnected( false, false, 'autsys' )
            ->keyBy( 'id' );

        $cnt = 0;
        foreach( $customers as $c ) {
            /** @var Customer  $c */
            $connlist = [];

            foreach( $c->virtualInterfaces as $vi ) {
                $iflist = [];
                $atLeastOnePiIsPeering   = false;
                $atLeastOnePiIsConnected = false;

                foreach( $vi->physicalInterfaces as $pi ) {
                    // FIXME: hack for LONAP as they do peering on reseller ports :-(
                    if( !$pi->switchPort->typePeering() && !$pi->switchPort->typeReseller() ) {
                        continue;
                    }
                    
                    $atLeastOnePiIsPeering = true;
                    
                    if( $pi->statusConnected() ) {
                        $ifl = [
                            'switch_id'	=> $pi->switchPort->switchid,
                            'if_speed'	=> $pi->configuredSpeed(),
                        ];

                        if( $pi->isRateLimited() ) {
                            $ifl['if_phys_speed'] = $pi->speed;
                        }

                        $iflist[] = $ifl;

                        $atLeastOnePiIsConnected = true;
                    }
                }
                
                if( !$atLeastOnePiIsPeering || !$atLeastOnePiIsConnected ) {
                    continue;
                }

                $vlanentries = [];
                foreach( $vi->vlanInterfaces as $vli ) {
                    if( $vli->vlan->private ) {
                        continue;
                    }

                    $vlanentry = [];
                    $vlanentry[ 'vlan_id' ] = $vli->vlanid;


                    foreach( [ 4,6 ] as $protocol ) {
                        $enabledfn = "ipv{$protocol}enabled";
                        $ipvaddressfn = "ipv{$protocol}address";
                        $ipv = "ipv{$protocol}";

                        if( $vli->$enabledfn ) {
                            $vlanentry[ $ipv ][ 'address' ] = $vli->$ipvaddressfn->address;
                            if( ( $asmacro = $c->asMacro( $protocol, "AS", true ) ) !== null ) {
                                $vlanentry[ $ipv ][ 'as_macro' ] = $asmacro;
                            }
                            $vlanentry[ $ipv ][ 'routeserver' ] = (bool)$vli->rsclient;

                            $macAddresses = [];
                            foreach( $vli->layer2addresses as $l2a ) {
                                $macAddresses[] = wordwrap( $l2a->mac, 2, ':',true);
                            };

                            $vlanentry[ $ipv ][ 'mac_addresses' ] = $macAddresses;

                            if( !is_null ( $c->maxprefixes ) ) {
                                $vlanentry[ $ipv ][ 'max_prefix' ] = $c->maxprefixes;
                            }


                            if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
                                $services = [];
                                if( isset( $routeServerIPs[ $vli->vlan->id ] ) && in_array( $vli->$ipvaddressfn->address, $routeServerIPs[ $vli->vlanid ], true ) ) {
                                    $services[] = 'ixrouteserver';
                                }
                                if( isset( $routeCollectorIPs[ $vli->vlanid ] ) && in_array( $vli->$ipvaddressfn->address, $routeCollectorIPs[ $vli->vlanid ], true ) ) {
                                    $services[] = 'ixroutecollector';
                                }

                                if( count( $services ) ) {
                                    $vlanentry[ $ipv ][ 'service_type' ] = $services;
                                }
                            }

                            if( $version >= self::EUROIX_JSON_VERSION_1_0 ) {
                                $services = [];

                                if( isset( $routeServersByIps[ $vli->vlanid ][ $vli->$ipvaddressfn->address ] ) ) {
                                    /** @var Router $r */
                                    $r = $routeServersByIps[ $vli->vlanid ][ $vli->$ipvaddressfn->address ];
                                    $service = new stdClass;
                                    $service->type           = 'ixrouteserver';
                                    $service->daemon         = $r->software();
                                    if( $r->software_version        ) { $service->daemon_version = $r->software_version; }
                                    if( $r->operating_system       ) { $service->os             = $r->operating_system; }
                                    if( $r->operating_system_version ) { $service->os_version     = $r->operating_system_version; }
                                    $services[] = $service;
                                }

                                if( isset( $routeCollectorsByIps[ $vli->vlanid ][ $vli->$ipvaddressfn->address ] ) ) {
                                    $r = $routeCollectorsByIps[ $vli->vlanid ][ $vli->$ipvaddressfn->address ];
                                    /** @var Router $r */
                                    $service = new stdClass;
                                    $service->type           = 'ixroutecollector';
                                    $service->daemon         = $r->software();
                                    if( $r->software_version        ) { $service->daemon_version = $r->software_version; }
                                    if( $r->operating_system        ) { $service->os             = $r->operating_system; }
                                    if( $r->operating_system_version ) { $service->os_version     = $r->operating_system_version; }
                                    $services[] = $service;
                                }

                                if( count( $services ) ) {
                                    $vlanentry[ $ipv ][ 'services' ] = $services;
                                }
                            }
                        }
                    }

                    $vlanentries[] = $vlanentry;
                }
                
                if( !count( $vlanentries ) ) {
                    continue;
                }

                $conn = [];

                $conn['ixp_id']      = $vli->vlan->infrastructureid;
                $conn['state']       = 'active';
                $conn['if_list']     = $iflist;
                $conn['vlan_list']   = $vlanentries;

                $connlist[] = $conn;
            }

            $memberinfo[ $cnt ] = [
                'asnum'          => $c->autsys,
                'member_since'   => $c->datejoin->format( 'Y-m-d' ).'T00:00:00Z',
                'url'            => $c->corpwww,
                'name'           => $c->name,
                'peering_policy' => $c->peeringpolicy,
                'member_type'    => $this->xlateMemberType( $c->type ),
            ];

            if( $detailed ) {
                $memberinfo[ $cnt ] = array_merge( $memberinfo[ $cnt ], [
                    'contact_email' => [ $c->peeringemail ],
                    'contact_phone' => [ $c->nocphone ],
                ]);

                if( filter_var($c->nocwww, FILTER_VALIDATE_URL) !== false ) {
                    $memberinfo[ $cnt ][ 'peering_policy_url' ] = $c->nocwww;
                }

                if( $c->nochours && strlen( $c->nochours ) ) {
                    $memberinfo[ $cnt ][ 'contact_hours' ] = $c->nochours;
                }
            }

            if( $tags ) {
                $memberinfo[ $cnt ][ 'ixp_manager' ][ 'tags' ] = [];
                foreach( $c->tags as $tag ) {
                    if( !$tag->internal_only || ( Auth::check() && Auth::user()->isSuperUser() ) ) {
                        $memberinfo[ $cnt ][ 'ixp_manager' ][ 'tags' ][ $tag->tag ] = $tag->display_as;
                    }
                }
                $memberinfo[ $cnt ][ 'ixp_manager' ][ 'in_manrs' ]    = (bool)$c->in_manrs;
                $memberinfo[ $cnt ][ 'ixp_manager' ][ 'is_reseller' ] = (bool)$c->isReseller;
                $memberinfo[ $cnt ][ 'ixp_manager' ][ 'is_resold' ]   = $c->reseller ? true : false;
                if( $c->reseller ) {
                    $memberinfo[ $cnt ][ 'ixp_manager' ][ 'resold_via_asn' ]   = $c->resellerObject->autsys;
                }
            }

            $memberinfo[ $cnt ][ 'connection_list' ] = $connlist;

            $cnt++;
        }

        return $memberinfo;
    }

    /**
     * Translate IXP Manager member types to JSON Export schema types
     *
     * @param int $ixpmType
     *
     * @return string
     */
    private function xlateMemberType( int $ixpmType ) : string
    {
        switch( $ixpmType ) {
            case Customer::TYPE_FULL:
                return 'peering';
            case Customer::TYPE_INTERNAL:
                return 'ixp';
            case Customer::TYPE_PROBONO:
                return 'peering';
            default:
                return 'other';
        }
    }

    /**
     * Filter details if requested by the config
     * @param array $output
     * @return array
     */
    private function filter( array $output ): array
    {
        // switch filters
        if( $s = config( 'ixp_api.json_export_schema.excludes.switch' ) ) {
            foreach( explode( '|', $s ) as $exc ) {
                foreach( $output[ 'ixp_list' ] as $ixid => $ix ) {
                    foreach( $ix[ 'switch' ] as $sid => $sw ) {
                        if( isset( $output[ 'ixp_list' ][ $ixid ][ 'switch' ][ $sid ][ $exc ] ) ) {
                            unset( $output[ 'ixp_list' ][ $ixid ][ 'switch' ][ $sid ][ $exc ] );
                        }
                    }
                }
            }
        }

        // ixp filters
        if( $i = config( 'ixp_api.json_export_schema.excludes.ixp' ) ) {
            foreach( explode( '|', $i ) as $exc ) {
                foreach( $output[ 'ixp_list' ] as $ixid => $ix ) {
                    if( isset( $output[ 'ixp_list' ][ $ixid ][ $exc ] ) ) {
                        unset( $output[ 'ixp_list' ][ $ixid ][ $exc ] );
                    }
                }
            }
        }

        // member filters
        if( $m = config( 'ixp_api.json_export_schema.excludes.member' ) ) {
            foreach( explode( '|', $m ) as $exc ) {
                foreach( $output[ 'member_list' ] as $membid => $memb ) {
                    if( isset( $output[ 'member_list' ][ $membid ][ $exc ] ) ) {
                        unset( $output[ 'member_list' ][ $membid ][ $exc ] );
                    }
                }
            }
        }

        // intinfo filters
        if( $i = config( 'ixp_api.json_export_schema.excludes.intinfo' ) ) {
            foreach( explode( '|', $i ) as $exc ) {
                foreach( $output[ 'ixp_list' ] as $intid => $int ) {
                    foreach( $int[ 'vlan' ] as $vid => $v ) {

                        if( isset( $output[ 'ixp_list' ][ $intid ][ 'vlan' ][ $vid ][ 'ipv4' ][ $exc ] ) ) {
                            unset( $output[ 'ixp_list' ][ $intid ][ 'vlan' ][ $vid ][ 'ipv4' ][ $exc ] );
                        }

                        if( isset( $output[ 'ixp_list' ][ $intid ][ 'vlan' ][ $vid ][ 'ipv6' ][ $exc ] ) ) {
                            unset( $output[ 'ixp_list' ][ $intid ][ 'vlan' ][ $vid ][ 'ipv6' ][ $exc ] );
                        }
                    }
                }
            }
        }

        return $output;
    }

}