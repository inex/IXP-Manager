<?php

/*
 * Copyright (C) 2009-2014 Internet Neutral Exchange Association Limited.
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
 * Controller: API V1 Memberlist controller
 *
 * @author     Nick Hilliard <nick@foobar.org>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2014, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_MemberListController extends IXP_Controller_API_V1Action
{
    public function preDispatch()
    {
        $this->assertUserPriv( \Entities\User::AUTH_CUSTUSER );
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
    }


    public function listAction()
    {
        $this->preflight();

        $this->getResponse()->setHeader( 'Content-Type', 'application/json' );

        $jsonoutput = array('version' => '2014110301');

        date_default_timezone_set('UTC');
        $jsonoutput['timestamp'] = date('Y-m-d', time()).'T'.date('H:i:s', time()).'Z';
        
        $jsonoutput['ixp_info'] = $this->getListIXPInfo();

        $jsonoutput['member_list'] = $this->getListMemberInfo();

        print json_encode($jsonoutput, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
    }

    private function preflight() {
        if( !isset( $this->_options['identity']['orgname'] )
            || !isset( $this->_options['identity']['legalname'] )
            || !isset( $this->_options['identity']['ixfid'] )
            || !isset( $this->_options['identity']['location']['country'] )
            || !isset( $this->_options['identity']['location']['city'] ) )
        {
            die( 'ERROR: Please ensure you have completed the [identity] section in application.ini. '
                . 'There may be new values that you need to copy over from application.ini.dist.'
            );
        }

        return;
    }

    private function getListIXPInfo() {
        $ixpinfo = array();

        $ixpinfo['shortname'] = $this->_options['identity']['orgname'];
        $ixpinfo['name']      = $this->_options['identity']['legalname'];
        $ixpinfo['ixp_id']    = $this->_options['identity']['ixfid'];
        $ixpinfo['country']   = $this->_options['identity']['location']['country'];

        $ixpinfo['vlan']   = $this->getD2R( '\\Entities\\NetworkInfo' )->asVlanEuroIXExportArray();
        $ixpinfo['switch'] = $this->getListSwitchInfo();

        return $ixpinfo;
    }

    private function getListMemberInfo() {
        $memberinfo = array();

        $customers = OSS_Array::reindexObjects(
                OSS_Array::reorderObjects( $this->getD2R( '\\Entities\\Customer' )->getConnected( false, false, true ), 'getAutsys', SORT_NUMERIC ),
                'getId'
        );

        foreach( $customers as $c ) {
            $ixp = $this->getD2R( '\\Entities\\IXP' )->getDefault();

            $conn = array();
            $connlist = array();
            foreach( $c->getVirtualInterfaces() as $vi ) {
                $iflist = array();
                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                    if( $pi->getStatus() == \Entities\PhysicalInterface::STATUS_CONNECTED ) {
                        $iflist[] = array (
                            'switch_id'	=> $pi->getSwitchPort()->getSwitcher()->getID(),
                            'if_speed'	=> $pi->getSpeed(),
                        );
                    }
                }

                $vlanentry = array();
                foreach( $vi->getVlanInterfaces() as $vli ) {
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

                $conn = array();
                $conn['state'] = 'active';
                $conn['if_list'] = $iflist;
                $conn['vlan_list'][] = $vlanentry;
                $connlist[] = $conn;
            }
            $memberinfo[] = [
                'asnum'			=> $c->getAutsys(),
                'name'			=> $c->getName(),
                'url'			=> $c->getCorpwww(),
                'contact_email'		=> array( $c->getPeeringemail() ),
                'contact_phone'		=> array( $c->getNocphone() ),
                'contact_hours'		=> $c->getNochours(),
                'peering_policy'	=> $c->getPeeringpolicy(),
                'peering_policy_url'	=> $c->getNocwww(),
                'member_since'		=> $c->getDatejoin()->format( 'Y-m-d' ).'T00:00:00Z',
                'connection_list'	=> $connlist,
            ];
        }

        return $memberinfo;
    }

    private function getListSwitchInfo() {
        $data = array();

        $ixp = $this->getD2R( '\\Entities\\IXP' )->getDefault();
        foreach( $ixp->getInfrastructures() as $infra ) {
            foreach( $infra->getSwitchers() as $switch ) {
                if( $switch->getSwitchtype() != \Entities\Switcher::TYPE_SWITCH || !$switch->getActive() )
                    continue;

                 $switchentry = array();
                 $switchentry['id']      = $switch->getId();
                 $switchentry['name']    = $switch->getName();
                 $switchentry['colo']    = $switch->getCabinet()->getLocation()->getName();
                 $switchentry['city']    = $this->_options['identity']['location']['city'];
                 $switchentry['country'] = $this->_options['identity']['location']['country'];
                 $data[] = $switchentry;
            }
        }
        return $data;
    }

}
