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

class SwitchController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'SwitchTable';
        $this->frontend['name']            = 'Switch';
        $this->frontend['pageTitle']       = 'Switches';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'name', 'switchtype', 'cabinet', 'infrastructure', 'ipv4addr', 'vendorid', 'model', 'active' ),

            'viewPanelRows'  => array( 'name', 'cabinet', 'ipv4addr', 'ipv6addr', 'snmppasswd', 'switchtype', 'vendorid', 'model', 'active', 'notes' ),
            'viewPanelTitle' => 'name',

            'sortDefaults' => array(
                'column' => 'name',
                'order'  => 'desc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),


            'name' => array(
                'label' => 'Name',
                'sortable' => 'true',
            ),

            'cabinet' => array(
                'type' => 'hasOne',
                'model' => 'Cabinet',
                'controller' => 'cabinet',
                'field' => 'name',
                'label' => 'Cabinet',
                'sortable' => true
            ),

            'infrastructure' => array(
                'label' => 'Infrastructure',
                'sortable' => true
            ),

            'ipv4addr' => array(
                'label' => 'IPv4 Address',
                'sortable' => true
            ),

            'ipv6addr' => array(
                'label' => 'IPv6 Address',
                'sortable' => true
            ),

            'snmppasswd' => array(
                'label' => 'SNMP Password',
                'sortable' => false
            ),

            'switchtype' => array(
                'label' => 'Switch Type',
                'sortable' => true,
                'type' => 'xlate',
                'xlator' => SwitchTable::$SWITCHTYPES_TEXT
            ),

            'vendorid' => array(
                'type' => 'hasOne',
                'model' => 'Vendor',
                'controller' => 'vendor',
                'field' => 'name',
                'label' => 'Vendor',
                'sortable' => true
            ),

            'model' => array(
                'label' => 'Model',
                'sortable' => true
            ),

            'active' => array(
                'label' => 'Active',
                'sortable' => true
            ),

            'notes' => array(
                'label' => 'Notes',
                'sortable' => false
            )
        );

        parent::feInit();
    }


    function portReportAction()
    {
        $switch = Doctrine_Core::getTable( 'SwitchTable' )->find( $this->_getParam( 'id' ) );

        if( !$switch )
        {
            $this->view->message = new INEX_Message( 'Invalid switch', INEX_Message::MESSAGE_TYPE_ERROR );
            return( $this->_forward( 'list' ) );
        }

        // load switch ports
        $ports = Doctrine_Query::create()
            ->from( 'Switchport sp' )
            ->where( 'sp.switchid = ?', $switch['id'] )
            ->orderBy( 'sp.id ASC' )
            ->execute( null, Doctrine_Core::HYDRATE_ARRAY );

        // add in customer details.
        // FIXME: there a better way of doing this
        foreach( $ports as $i => $p )
        {
            $ports[$i]['connection'] = Doctrine_Query::create()
                ->from( 'Physicalinterface p' )
                ->leftJoin( 'p.Virtualinterface v' )
                ->leftJoin( 'v.Cust c' )
                ->where( 'p.switchportid = ?', $p['id'] )
                ->fetchOne( null, Doctrine_Core::HYDRATE_ARRAY );

            $ports[$i]['type'] = Switchport::$TYPE_TEXT[ $p['type'] ];
        }

        //echo '<pre>'; print_r( $ports );die();

        $this->view->ports = $ports;
        $this->view->display( 'switch/port-report.tpl' );
    }
}

?>