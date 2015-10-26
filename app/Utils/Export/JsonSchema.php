<?php namespace IXP\Utils\Export;

use D2R;
use OSS_Array;

/*
 * Copyright (C) 2009-2015 Internet Neutral Exchange Association Limited.
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
 * @copyright  Copyright (c) 2009 - 2015, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class JsonSchema
{
    // Supported versions:
    const EUROIX_JSON_VERSION_0_3 = "0.3";
    const EUROIX_JSON_VERSION_0_4 = "0.4";
    const EUROIX_JSON_VERSION_0_5 = "0.5";
    // adding a new version? update sanitiseVersion() below also!
    const EUROIX_JSON_LATEST = self::EUROIX_JSON_VERSION_0_5;

    /**
     * Get the JSON schema (for a given version or for the latest version)
     *
     * @param string $version The version to get (or, if null / not present then the latest)
     * @param bool   $asArray Do not convert to JSON but rather return the PHP array
     * @return string|array
     */
    public function get( $version = null, $asArray = false )
    {
        if( $version === null )
            $version = self::EUROIX_JSON_LATEST;
        else
            $version = $this->sanitiseVersion( $version );

        // slightly awkward as v0.3 used a date type versioning. internally we use 0.3
        // for numeric comparisons
        if( $version == self::EUROIX_JSON_VERSION_0_3 )
            $output = [ 'version' => '2014110401' ];
        else
            $output = [ 'version' => $version ];

        // normalise times to UTC for exports
        date_default_timezone_set('UTC');
        $output['timestamp'] = date( 'Y-m-d', time() ) . 'T' . date( 'H:i:s', time() ) . 'Z';

        // from v0.4 onwards, this was renamed ixp_list and allows for multiple ixps
        // (IXP Manager only supports one IXP per IXP Manager instance)
        if( $version == self::EUROIX_JSON_VERSION_0_3 )
            $output['ixp_info'] = $this->getIXPInfo( $version );
        else
            $output['ixp_list'][] = $this->getIXPInfo( $version );

        $output['member_list'] = $this->getMemberInfo( $version );

        if( $asArray )
            return $output;

        return json_encode( $output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )."\n";
    }

    /**
     * Ensure a given version exists or default to latest
     * @param string $version Version string to sanitise
     * @return string Sanitised version
     */
    public function sanitiseVersion( $version )
    {
        switch ( $version )
        {
            // alias for v0.3
            case '2014110401':
                return self::EUROIX_JSON_VERSION_0_3;

            case self::EUROIX_JSON_VERSION_0_3:
            case self::EUROIX_JSON_VERSION_0_4:
            case self::EUROIX_JSON_VERSION_0_5:
                return $version;

            default:
                return self::EUROIX_JSON_LATEST;
        }
    }

    /**
     * Collate the IXP specific information for the JSON schema export
     *
     * @param string $version The version to collate the detail for
     * @return array
     */
    private function getIXPInfo( $version )
    {
        $ixpinfo = [];

        $ixpinfo['shortname'] = config( 'identity.orgname'          );
        $ixpinfo['name']      = config( 'identity.legalname'        );
        $ixpinfo['country']   = config( 'identity.location.country' );
        $ixpinfo['url']       = config( 'identity.corporate_url'    );

        # oops, messy.  ixp_id was renamed as ixf_id in v0.4
        if( $version == self::EUROIX_JSON_VERSION_0_3 ) {
            $ixpinfo['ixp_id'] = intval( config( 'identity.ixfid' ) );
        } else {
            $ixpinfo['ixf_id'] = intval( config( 'identity.ixfid' ) );
            $ixpinfo['ixp_id'] = 1;                            // referenced in member's connections section
        }

        $ixp_info['support_email']             = config( 'identity.support_email' );
        $ixp_info['support_phone']             = config( 'identity.support_phone' );
        $ixp_info['support_contact_hours']     = config( 'identity.support_hours' );

        if( $version > self::EUROIX_JSON_VERSION_0_3 ) {
            // $ixpinfo['stats_api'] = FIXME;
            $ixp_info['emergency_email']           = config( 'identity.support_email' );
            $ixp_info['emergency_phone']           = config( 'identity.support_phone' );
            $ixp_info['emergency_contact_hours']   = config( 'identity.support_hours' );
            $ixp_info['billing_contact_hours']     = config( 'identity.billing_hours' );
        }

        $ixp_info['billing_email']             = config( 'identity.billing_email' );
        $ixp_info['billing_phone']             = config( 'identity.billing_phone' );

        $ixp_info['peering_policy_list'] = array_values( \Entities\Customer::$PEERING_POLICIES );

        $ixpinfo['vlan']   = D2R::r( 'NetworkInfo' )->asVlanEuroIXExportArray();
        $ixpinfo['switch'] = $this->getSwitchInfo( $version );

        return $ixpinfo;
    }

    /**
     * Collate the IXP's switch information for the JSON schema export
     *
     * @return array
     */
    private function getSwitchInfo( $version )
    {
        $data = [];

        $ixp = D2R::r( 'IXP' )->getDefault();

        foreach( $ixp->getInfrastructures() as $infra ) {
            foreach( $infra->getSwitchers() as $switch ) {
                if( $switch->getSwitchtype() != \Entities\Switcher::TYPE_SWITCH || !$switch->getActive() )
                    continue;

                $switchentry = [];
                $switchentry['id']      = $switch->getId();
                $switchentry['name']    = $switch->getName();
                $switchentry['colo']    = $switch->getCabinet()->getLocation()->getName();
                $switchentry['city']    = config( 'identity.location.city'    );
                $switchentry['country'] = config( 'identity.location.country' );

                if( $version >= self::EUROIX_JSON_VERSION_0_5 && $switch->getCabinet()->getLocation()->getPdbFacilityId() )
                    $switchentry['pdb_facility_id'] = intval( $switch->getCabinet()->getLocation()->getPdbFacilityId() );

                $data[] = $switchentry;
            }
        }

        return $data;
    }

    /**
     * Collate the IXP's member information for the JSON schema export
     *
     * @param string $version The version to collate the detail for
     * @return array
     */
    private function getMemberInfo( $version )
    {
        $memberinfo = [];
        $ixp = D2R::r( '\\Entities\\IXP' )->getDefault();

        $customers = OSS_Array::reindexObjects(
            OSS_Array::reorderObjects( D2R::r( '\\Entities\\Customer' )->getConnected( false, false, true ),
                'getAutsys', SORT_NUMERIC
            ),
            'getId'
        );

        $cnt = 0;
        foreach( $customers as $c )
        {
            $connlist = [];
            foreach( $c->getVirtualInterfaces() as $vi )
            {
                $iflist = [];
                foreach( $vi->getPhysicalInterfaces() as $pi )
                {
                    if( $pi->getStatus() == \Entities\PhysicalInterface::STATUS_CONNECTED ) {
                        $iflist[] = array (
                            'switch_id'	=> $pi->getSwitchPort()->getSwitcher()->getId(),
                            'if_speed'	=> $pi->getSpeed(),
                        );
                    }
                }

                $vlanentry = [];

                // MAC addresses added in 0.4
                if( $version > self::EUROIX_JSON_VERSION_0_3 ) {
                    $macaddrs = $vi->getMACAddresses();
                    if( $macaddrs[0] )
                        $vlanentry['mac_address'] = implode( ":", str_split( $macaddrs[0]->getMac(), 2 ) );
                }

                foreach( $vi->getVlanInterfaces() as $vli )
                {
                    $vlanentry['vlan_id'] = $vli->getVlan()->getId();
                    if ($vli->getIpv4enabled()) {
                        $vlanentry['ipv4']['address'] = $vli->getIPv4Address()->getAddress();
                        $vlanentry['ipv4']['routeserver'] = $vli->getRsclient();
                        $vlanentry['ipv4']['max_prefix'] = $vi->getCustomer()->getMaxprefixes();
                        $vlanentry['ipv4']['as_macro'] = $vi->getCustomer()->resolveAsMacro( 4, "AS");
                    }
                    if ($vli->getIpv6enabled()) {
                        $vlanentry['ipv6']['address'] = $vli->getIPv6Address()->getAddress();
                        $vlanentry['ipv6']['routeserver'] = $vli->getRsclient();
                        $vlanentry['ipv6']['max_prefix'] = $vi->getCustomer()->getMaxprefixes();
                        $vlanentry['ipv6']['as_macro'] = $vi->getCustomer()->resolveAsMacro( 6, "AS" );
                    }
                }

                $conn = [];

                if( $version > self::EUROIX_JSON_VERSION_0_3 )
                    $conn['ixp_id'] = 1;

                $conn['state']       = 'active';
                $conn['if_list']     = $iflist;
                $conn['vlan_list'][] = $vlanentry;

                $connlist[] = $conn;
            }

            $memberinfo[ $cnt ] = [
                'asnum'		       	 => $c->getAutsys(),
                'name'			     => $c->getName(),
                'url'			     => $c->getCorpwww(),
                'contact_email'		 => [ $c->getPeeringemail() ],
                'contact_phone'		 => [ $c->getNocphone() ],
                'contact_hours'		 => $c->getNochours(),
                'peering_policy'	 => $c->getPeeringpolicy(),
                'peering_policy_url' => $c->getNocwww(),
                'member_since'		 => $c->getDatejoin()->format( 'Y-m-d' ).'T00:00:00Z'
            ];

            if( $version > self::EUROIX_JSON_VERSION_0_3 ) {
                $memberinfo[$cnt][ 'type' ] = $this->xlateMemberType( $c->getType() );
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
            case \Entities\Customer::TYPE_FULL:
                return 'peering';

            case \Entities\Customer::TYPE_INTERNAL:
                return 'ixp';

            case \Entities\Customer::TYPE_PROBONO:
                return 'probono';

            case \Entities\Customer::TYPE_ROUTESERVER:
                return 'routeserver';

            default:
                return 'other';
        }
    }

}
