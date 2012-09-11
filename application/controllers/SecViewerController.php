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


/**
 * Controller: View SEC logs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SecViewerController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'timestamp';
        $this->frontend['model']           = 'SecEvent';
        $this->frontend['name']            = 'SEC Event Viewer';
        $this->frontend['pageTitle']       = 'SEC Events';

        $this->frontend['columns'] = array(

            'displayColumns' => array( 'id', 'custid', 'switchid', 'switchportid', 'type', 'timestamp' ),

            'viewPanelRows'  => array(  'custid', 'switchid', 'switchportid', 'type', 'message', 'timestamp', 'recorded_date' ),

            'viewPanelTitle' => 'custid',

            'sortDefaults' => array(
                'column' => 'timestamp',
                'order'  => 'desc'
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

            'switchid' => array(
                'type' => 'hasOne',
                'model' => 'SwitchTable',
                'controller' => 'switch',
                'field' => 'name',
                'label' => 'Switch',
                'sortable' => true
            ),

            'switchportid' => array(
                'type' => 'hasOne',
                'model' => 'Switchport',
                'controller' => 'switchport',
                'field' => 'name',
                'label' => 'Port',
                'sortable' => false
            ),

            'type' => array(
                'label' => 'Type',
                'sortable' => true
            ),

            'message' => array(
                'label' => 'Message',
            ),

            'timestamp' => array(
                'label' => 'Timestamp',
                'sortable' => true
            ),

            'recorded_time' => array(
                'label' => 'Recorded Time',
            )

        );

        // Override global auth level requirement for specific actions
        $this->frontend['authLevels'] = array(
            'read' => User::AUTH_CUSTUSER
        );

        parent::feInit();
    }




    /**
     * Show each member their own SEC event logs
     */
    public function readAction()
    {
        $page    = $this->_request->getParam( 'p', 1 );
        $perPage = 20;

        $logs = new Doctrine_Pager(
            Doctrine_Query::create()
                ->select( 'e.message, e.recorded_date' )
                ->from( 'SecEvent e' )
                ->where( 'e.custid = ?' )
                ->orderby( 'e.recorded_date ASC' ),
            $page,
            $perPage
        );

        $this->view->assign( 'logs', $logs->execute( $this->customer['id'], Doctrine_Core::HYDRATE_ARRAY ) );
        $this->view->assign( 'pager', $logs );

        $this->view->display( 'sec-viewer/read.tpl' );
    }


}

