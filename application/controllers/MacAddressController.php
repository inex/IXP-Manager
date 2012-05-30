<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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

class MacAddressController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Macaddress';
        $this->frontend['name']            = 'MAC Address';
        $this->frontend['pageTitle']       = 'MAC Addresses';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'firstseen', 'lastseen', 'virtualinterfaceid', 'mac' ),

            'viewPanelRows'  => array( 'firstseen', 'lastseen', 'virtualinterfaceid', 'mac' ),
            'viewPanelTitle' => 'MAC Address',

            'sortDefaults' => array(
                'column' => 'mac',
                'order'  => 'desc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'firstseen' => array(
                'label' => 'First Seen',
                'sortable' => 'true'
            ),

            'lastseen' => array(
                'label' => 'First Seen',
                'sortable' => 'true'
            ),

            'virtualinterfaceid' => array(
                'type' => 'hasOne',
                'model' => 'Virtualinterface',
                'controller' => 'virtual-interface',
                'field' => 'custid',
                'label' => 'Virtual Interface',
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
        
        $this->view->macs = Doctrine_Query::create()
            ->select( 'c.name, c.id, m.*, vi.id, ip4.address, ip6.address, m.mac, vli.id, ip4.id, ip6.id' )
            ->addSelect( 'pi.id, sp.id, s.id, sp.name, s.name' )
            ->from( 'Macaddress m' )
            ->leftJoin( 'm.Virtualinterface vi' )
            ->leftJoin( 'vi.Cust c' )
            ->leftJoin( 'vi.Vlaninterface vli' )
            ->leftJoin( 'vi.Physicalinterface pi' )
            ->leftJoin( 'pi.Switchport sp' )
            ->leftJoin( 'sp.SwitchTable s' )
            ->leftJoin( 'vli.Ipv4address ip4' )
            ->leftJoin( 'vli.Ipv6address ip6' )
            ->leftJoin( 'vli.Vlan v' )
            ->orderBy( 'c.name ASC' )
            ->fetchArray();

/*
        echo '<pre>';
        echo "COUNT: " . count( $this->view->macs ) . "\n\n\n";
        print_r( $this->view->macs ); die();
  */
        $this->view->display( 'mac-address/list.tpl' );
    }

}

