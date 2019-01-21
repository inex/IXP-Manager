<?php

namespace IXP\Utils\Export;

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

use Entities\{Customer, Infrastructure, IXP, Router, Switcher, VirtualInterface};

use IXP\Exceptions\Utils\ExportException;

use IXP\Utils\ArrayUtilities;

/**
 * JSON Schema Exporter
 *
 * Usage:
 *
 *     $jexport = new \IXP\Utils\Export\JsonSchema;
 *     $json_schema = $jexport->get( \IXP\Utils\Export\JsonSchema::EUROIX_JSON_LATEST );
 *
 * @see        https://github.com/euro-ix/json-schemas
 * @author     Nick Hilliard <nick@foobar.org>
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class JsonSchema
{
    // Supported versions:
    // ended 201705: const EUROIX_JSON_VERSION_0_3 = "0.3";
    // ended 201705: const EUROIX_JSON_VERSION_0_4 = "0.4";
    // ended 201705: const EUROIX_JSON_VERSION_0_5 = "0.5";
    const EUROIX_JSON_VERSION_0_6 = "0.6";
    const EUROIX_JSON_VERSION_0_7 = "0.7";
    // adding a new version? update sanitiseVersion() below also!

    const EUROIX_JSON_LATEST = self::EUROIX_JSON_VERSION_0_7;

    const EUROIX_JSON_VERSIONS = [
        self::EUROIX_JSON_VERSION_0_6,
        self::EUROIX_JSON_VERSION_0_7,
    ];

    /**
     * Get the JSON schema (for a given version or for the latest version)
     *
     * @param string $version The version to get (or, if null / not present then the latest)
     * @param bool   $asArray Do not convert to JSON but rather return the PHP array
     * @param bool   $detailed Create the very detailed version (usually for logged in users)
     * @param bool   $tags     Include customer tags
     * @return string|array
     */
    public function get( $version = null, $asArray = false, $detailed = true, $tags = false )
    {
        if( $version === null ) {
            $version = self::EUROIX_JSON_LATEST;
        } else {
            $version = $this->sanitiseVersion($version);
        }

        $output = [ 'version' => $version ];

        // normalise times to UTC for exports
        date_default_timezone_set('UTC');
        $output['timestamp'] = date( 'Y-m-d', time() ) . 'T' . date( 'H:i:s', time() ) . 'Z';

        $output['ixp_list']    = $this->getIXPInfo( $version );
        $output['member_list'] = $this->getMemberInfo( $version, $detailed, $tags );

        if( $asArray ) {
            return $output;
        }

        return json_encode( $output, JSON_PRETTY_PRINT )."\n";
    }

    /**
     * Ensure a given version exists or default to latest
     * @param string $version Version string to sanitise
     * @return string Sanitised version
     */
    public function sanitiseVersion( $version )
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
     * @return array
     * @throws ExportException
     */
    private function getIXPInfo( $version )
    {
        $ixpinfo = [];

        /** @var IXP $ixp */
        $ixp = d2r( 'IXP' )->getDefault();

        foreach( $ixp->getInfrastructures() as $infra ) {

            $i = [];
            /** @var Infrastructure $infra */
            $i['shortname'] = $infra->getName();
            $i['name'] = config('identity.legalname');
            $i['country'] = config('identity.location.country');
            $i['url'] = config('identity.corporate_url');

            if( $infra->getPeeringdbIxId() ) {
                $i[ 'peeringdb_id' ] = intval( $infra->getPeeringdbIxId() );
            }

            if( $infra->getIxfIxId() ) {
                $i[ 'ixf_id' ] = intval( $infra->getIxfIxId() );
            } else if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
                throw new ExportException( "IX-F ID is required for IX-F Export Schema >=v0.7. Set this under Infrastructures." );
            }

            $i['ixp_id'] = $infra->getId();    // referenced in member's connections section

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

            $i['peering_policy_list'] = array_values(\Entities\Customer::$PEERING_POLICIES);

            $i['vlan'] = d2r('NetworkInfo')->asVlanEuroIXExportArray( $infra );

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
     * @return array
     */
    private function getSwitchInfo( $version, Infrastructure $infra )
    {
        $data = [];

        /** @var Switcher $switch */
        foreach( $infra->getSwitchers() as $switch ) {
            if( !$switch->getActive() ) {
                continue;
            }

            $switchentry = [];
            $switchentry['id']      = $switch->getId();
            $switchentry['name']    = $switch->getName();
            $switchentry['colo']    = $switch->getCabinet()->getLocation()->getName();
            $switchentry['city']    = config( 'identity.location.city'    );
            $switchentry['country'] = config( 'identity.location.country' );

            if( $switch->getCabinet()->getLocation()->getPdbFacilityId() ) {
                $switchentry['pdb_facility_id'] = intval($switch->getCabinet()->getLocation()->getPdbFacilityId());
            }

            if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
                $switchentry['manufacturer'] = $switch->getVendor()->getName();
                $switchentry['model']        = $switch->getModel();
            }

            $data[] = $switchentry;
        }

        return $data;
    }

    /**
     * Collate the IXP's member information for the JSON schema export
     *
     * @param string $version The version to collate the detail for
     * @return array
     */
    private function getMemberInfo( string $version, bool $detailed, bool $tags )
    {
        $memberinfo = [];

        if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
            $routeServerIPs = d2r( 'Router' )->getAllPeeringIPs( Router::TYPE_ROUTE_SERVER );
            $routeCollectorIPs = d2r( 'Router' )->getAllPeeringIPs( Router::TYPE_ROUTE_COLLECTOR );
        }

        $customers = ArrayUtilities::reindexObjects(
            ArrayUtilities::reorderObjects( d2r( 'Customer' )->getConnected( false, false ),
                'getAutsys', SORT_NUMERIC
            ),
            'getId'
        );

        $cnt = 0;

        /** @var Customer $c */
        foreach( $customers as $c ) {
            $connlist = [];
            /** @var VirtualInterface $vi */
            foreach( $c->getVirtualInterfaces() as $vi ) {

                $iflist = [];
                $atLeastOnePiIsPeering   = false;
                $atLeastOnePiIsConnected = false;

                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                    // FIXME: hack for LONAP as they do peering on reseller ports :-(
                    if( !$pi->getSwitchPort()->isTypePeering() && !$pi->getSwitchPort()->isTypeReseller() ) {
                        continue;
                    }
                    
                    $atLeastOnePiIsPeering = true;
                    
                    if( $pi->getStatus() == \Entities\PhysicalInterface::STATUS_CONNECTED ) {
                        $iflist[] = array (
                            'switch_id'	=> $pi->getSwitchPort()->getSwitcher()->getId(),
                            'if_speed'	=> $pi->getSpeed(),
                        );
                        $atLeastOnePiIsConnected = true;
                    }
                }
                
                if( !$atLeastOnePiIsPeering || !$atLeastOnePiIsConnected ) {
                    continue;
                }

                $vlanentries = [];

                foreach( $vi->getVlanInterfaces() as $vli ) {
                    if( $vli->getVlan()->getPrivate() ) {
                        continue;
                    }

                    $vlanentry = [];

                    $vlanentry['vlan_id'] = $vli->getVlan()->getId();

                    if ($vli->getIpv4enabled()) {
                        $vlanentry['ipv4']['address'] = $vli->getIPv4Address()->getAddress();
                        if( ( $asmacro = $vi->getCustomer()->resolveAsMacro( 4, "AS", true ) ) !== null ) {
                            $vlanentry[ 'ipv4' ][ 'as_macro' ] = $asmacro;
                        }
                        $vlanentry['ipv4']['routeserver'] = $vli->getRsclient();
                        $vlanentry['ipv4']['mac_addresses'] = $vli->getLayer2AddressesAsArray();
                        if( $detailed && !is_null ($vi->getCustomer()->getMaxprefixes()) ) {
                            $vlanentry['ipv4']['max_prefix'] = $vi->getCustomer()->getMaxprefixes();
                        }

                        if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
                            $services = [];
                            if( isset( $routeServerIPs[ $vli->getVlan()->getId() ] ) && in_array( $vli->getIPv4Address()->getAddress(), $routeServerIPs[ $vli->getVlan()->getId() ] ) ) {
                                $services[] = 'ixrouteserver';
                            }
                            if( isset( $routeCollectorIPs[ $vli->getVlan()->getId() ] ) && in_array( $vli->getIPv4Address()->getAddress(), $routeCollectorIPs[ $vli->getVlan()->getId() ] ) ) {
                                $services[] = 'ixroutecollector';
                            }

                            if( count( $services ) ) {
                                $vlanentry[ 'ipv4' ][ 'service_type' ] = $services;
                            }
                        }
                    }

                    if ($vli->getIpv6enabled()) {
                        $vlanentry['ipv6']['address'] = $vli->getIPv6Address()->getAddress();
                        if( ( $asmacro = $vi->getCustomer()->resolveAsMacro( 6, "AS", true ) ) !== null ) {
                            $vlanentry[ 'ipv6' ][ 'as_macro' ] = $asmacro;
                        }
                        $vlanentry['ipv6']['routeserver'] = $vli->getRsclient();
                        $vlanentry['ipv6']['mac_addresses'] = $vli->getLayer2AddressesAsArray();
                        if( $detailed && !is_null ($vi->getCustomer()->getMaxprefixes()) ) {
                            $vlanentry['ipv6']['max_prefix'] = $vi->getCustomer()->getMaxprefixes();
                        }

                        if( $version >= self::EUROIX_JSON_VERSION_0_7 ) {
                            $services = [];
                            if( isset( $routeServerIPs[ $vli->getVlan()->getId() ] ) && in_array( $vli->getIPv6Address()->getAddress(), $routeServerIPs[ $vli->getVlan()->getId() ] ) ) {
                                $services[] = 'ixrouteserver';
                            }
                            if( isset( $routeCollectorIPs[ $vli->getVlan()->getId() ] ) && in_array( $vli->getIPv6Address()->getAddress(), $routeCollectorIPs[ $vli->getVlan()->getId() ] ) ) {
                                $services[] = 'ixroutecollector';
                            }

                            if( count( $services ) ) {
                                $vlanentry[ 'ipv6' ][ 'service_type' ] = $services;
                            }
                        }
                    }

                    $vlanentries[] = $vlanentry;
                }
                
                if( !count( $vlanentries ) ) {
                    continue;
                }

                $conn = [];

                $conn['ixp_id']      = $vli->getVlan()->getInfrastructure()->getId();
                $conn['state']       = 'active';
                $conn['if_list']     = $iflist;
                $conn['vlan_list']   = $vlanentries;

                $connlist[] = $conn;
            }

            $memberinfo[ $cnt ] = [
                'asnum'          => $c->getAutsys(),
                'member_since'   => $c->getDatejoin()->format( 'Y-m-d' ).'T00:00:00Z',
                'url'            => $c->getCorpwww(),
                'name'           => $c->getName(),
                'peering_policy' => $c->getPeeringpolicy(),
                'member_type'    => $this->xlateMemberType( $c->getType() ),
            ];

            if( $detailed ) {
                $memberinfo[$cnt] = array_merge($memberinfo[$cnt], [
                    'contact_email' => [ $c->getPeeringemail() ],
                    'contact_phone' => [ $c->getNocphone() ],
                ]);

                if( filter_var($c->getNocwww(), FILTER_VALIDATE_URL) !== false ) {
                    $memberinfo[$cnt]['peering_policy_url'] = $c->getNocwww();
                }

                if( $c->getNochours() && strlen($c->getNochours()) ) {
                    $memberinfo[$cnt]['contact_hours'] = $c->getNochours();
                }
            }


            if( $tags ) {
                $memberinfo[$cnt]['ixp_manager']['tags'] = [];
                foreach( $c->getTags() as $tag ) {
                    if( !$tag->isInternalOnly() || $detailed ) {
                        $memberinfo[$cnt]['ixp_manager']['tags'][ $tag->getTag() ] = $tag->getDisplayAs();
                    }
                }
            }

            $memberinfo[$cnt]['connection_list'] = $connlist;

            $cnt++;
        }

        return $memberinfo;
    }

    /**
     * Translate IXP Manager member types to JSON Export schema types
     *
     * @param int $ixpmType
     * @return string
     */
    private function xlateMemberType( $ixpmType )
    {
        switch( $ixpmType ) {
            case Customer::TYPE_FULL:
                return 'peering';

            case Customer::TYPE_INTERNAL:
                return 'ixp';

            case Customer::TYPE_PROBONO:
                return 'peering';

            case Customer::TYPE_ROUTESERVER:
                return 'ixp';

            default:
                return 'other';
        }
    }

}
