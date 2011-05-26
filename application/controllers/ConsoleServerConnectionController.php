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

class ConsoleServerConnectionController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'name';
        $this->frontend['model']           = 'Consoleserverconnection';
        $this->frontend['name']            = 'ConsoleServerConnection';
        $this->frontend['pageTitle']       = 'Console Server Connections';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'custid', 'description', 'switchid', 'port' ),

            'viewPanelRows'  => array( 'custid', 'description', 'switchid', 'port', 'speed', 'parity', 'stopbits', 'flowcontrol', 'autobaud', 'notes' ),

            'sortDefaults' => array(
                'column' => 'port',
                'order'  => 'asc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),


            'custid' => array(
                'type' => 'hasOne',
                'model' => 'Cust',
                'controller' => 'customer',
                'field' => 'name',
                'label' => 'Customer',
                'sortable' => true
            ),

            'description' => array(
                'label' => 'Description',
                'sortable' => 'true',
            ),

            'switchid' => array(
                'type' => 'hasOne',
                'model' => 'SwitchTable',
                'controller' => 'switch',
                'field' => 'name',
                'label' => 'Console Server',
                'sortable' => true
            ),

            'port' => array(
                'label' => 'Port',
                'sortable' => 'true',
            ),

            'speed' => array(
                'label' => 'Speed'
            ),

            'parity' => array(
                'label' => 'Parity'
            ),

            'stopbits' => array(
                'label' => 'Stopbits'
            ),

            'flowcontrol' => array(
                'label' => 'Flow Control'
            ),

            'autobaud' => array(
                'label' => 'Autobaud'
            ),

            'notes' => array(
                'label' => 'Notes'
            )

        );

        parent::feInit();
    }

}

?>