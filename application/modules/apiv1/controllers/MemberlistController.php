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
class Apiv1_MemberlistController extends IXP_Controller_API_V1Action
{

    public function listAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        $this->getResponse()->setHeader( 'Content-Type', 'text/plain' );

        $jsonoutput = array('version' => '2014110301');

        $jsonoutput['ixp_info'] = $this->getListIXPInfo();

        $jsonoutput['member_list'] = $this->getListMemberInfo();

        print json_encode($jsonoutput, JSON_PRETTY_PRINT)."\n";
    }  

    private function getListIXPInfo() {
        $ixpinfo = array();
        
        $ixpinfo['shortname'] = $this->_options['identity']['orgname'];

        // FIXME: need extra term in application.ini
        $ixpinfo['country'] = 'IE';

#        $ixpinfo['vlan'] = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanEuroIXExportArray();
 
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
            $memberinfo[] = [
                'asnum'			=> $c->getAutsys(),
                'name'			=> $c->getName(),
                'url'			=> $c->getCorpwww(),
                'contact_email'		=> $c->getPeeringemail(),
                'contact_phone'		=> $c->getNocphone(),
                'contact_hours'		=> $c->getNochours(),
                'peering_policy'	=> $c->getPeeringpolicy(),
                'peering_policy_url'	=> $c->getNocwww(),
                'member_since'		=> $c->getDatejoin()->format( 'Y-m-d' ),
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

                 $data[ $switch->getId() ]['name'] = $switch->getName();
                 $data[ $switch->getId() ]['colo'] = $switch->getCabinet()->getLocation()->getName();
            }
        }
        return $data;
    }

}
