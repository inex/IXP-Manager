<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class Ipv6AddressController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Ipv6address';
        $this->frontend['name']            = 'IPv6 Address';
        $this->frontend['pageTitle']       = 'IPv6 Addresses';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'address', 'vlanid' ),

            'viewPanelRows'  => array( 'address', 'vlanid' ),
            'viewPanelTitle' => 'address',

            'sortDefaults' => array(
                'column' => 'address',
                'order'  => 'desc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'address' => array(
                'label' => 'Ipv6 Address',
                'sortable' => 'true',
            ),

            'vlanid' => array(
                'type' => 'hasOne',
                'model' => 'Vlan',
                'controller' => 'vlan',
                'field' => 'name',
                'label' => 'Vlan',
                'sortable' => true
            )

        );

        parent::feInit();
    }


    public function listAction()
    {
        $this->view->vlans = Doctrine_Query::create()
            ->from( 'Vlan v' )
            ->orderBy( 'v.number ASC' )
            ->fetchArray();
        
        if( count( $this->view->vlans ) == 0 )
        {
            $this->session->message = new INEX_Message(  'You must first create a VLAN', "error" );
            $this->_redirect( 'index/index' );
        }
            
        $vlanid = $this->_getParam( 'vlanid', null );
        
        if( $vlanid === null )
        {
            $vlanid = $this->view->vlans[0]['id'];
            $this->view->vlan = $this->view->vlans[0];
        }
        else
            $this->view->vlan = Doctrine_Core::getTable( 'Vlan' )->find( $vlanid, Doctrine_Core::HYDRATE_ARRAY );
        
        $this->view->ips = Doctrine_Query::create()
            ->from( 'Ipv6address ip' )
            ->leftJoin( 'ip.Vlaninterface vi' )
            ->leftJoin( 'vi.Virtualinterface virt' )
            ->leftJoin( 'virt.Cust c' )
            ->leftJoin( 'ip.Vlan v' )
            ->where( 'v.id = ?', $vlanid )
            ->orderBy( 'ip.id ASC' )
            ->fetchArray();

        $this->view->display( 'ipv6-address/list.tpl' );
    }

}

?>