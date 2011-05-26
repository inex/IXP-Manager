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

class Ipv4AddressController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Ipv4address';
        $this->frontend['name']            = 'IPv4 Address';
        $this->frontend['pageTitle']       = 'IPv4 Addresses';

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
                'label' => 'Ipv4 Address',
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
        $vlan = $this->_getParam( 'vlan', 10 );

        $this->view->ips = Doctrine_Query::create()
            ->from( 'Ipv4address ip' )
            ->leftJoin( 'ip.Vlaninterface vi' )
            ->leftJoin( 'vi.Virtualinterface virt' )
            ->leftJoin( 'virt.Cust c' )
            ->leftJoin( 'ip.Vlan v' )
            ->where( 'v.number = ?', $vlan )
            ->orderBy( 'ip.id ASC' )
            ->fetchArray();

        //INEX_Debug::dd( $this->view->ips, true );
        $this->view->display( 'ipv4-address/list.tpl' );
    }

}

?>