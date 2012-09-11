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
 * Controller: Manage Changelogs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ChangeLogController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'livedate';
        $this->frontend['model']           = 'ChangeLog';
        $this->frontend['name']            = 'ChangeLog';
        $this->frontend['pageTitle']       = 'Change Log';

        $this->frontend['columns'] = array(

            'displayColumns' => array(
                'id', 'title', 'visibility', 'livedate', 'created_by', 'created_at'
            ),

            'viewPanelRows'  => array( 'title', 'visibility', 'details', 'livedate', 'created_by', 'created_at' ),

            'viewPanelTitle' => 'title',

            'sortDefaults' => array(
                'column' => 'livedate', 'order' => 'desc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'title' => array(
                'label' => 'Summary',
                'sortable' => false
            ),

            'details' => array(
                'label' => 'Details'
            ),

            'visibility' => array(
                'label' => 'Visibility',
                'sortable' => true,
                'type' => 'xlate',
                'xlator' => User::$PRIVILEGES_TEXT
            ),

            'livedate' => array(
                'label' => 'Live Date'
            ),

            'created_by' => array(
                'type' => 'hasOne',
                'model' => 'User',
                'controller' => 'user',
                'field' => 'username',
                'label' => 'Created By',
                'sortable' => true
            ),

            'created_at' => array(
                'label' => 'Created At'
            )


        );

        // Override global auth level requirement for specific actions
        $this->frontend['authLevels'] = array(
            'read' => User::AUTH_CUSTUSER
        );

        parent::feInit();

    }


    /**
     * Before we save, set the created by field.
     *
     * @param unknown_type $log The Change Log object
     * @param unknown_type $isEdit True if the log is being edited, flase if it's being added
     * @param unknown_type $form The submitted form object
     */
    protected function addEditPreSave( $log, $isEdit, $form )
    {
        $log['created_by'] = $this->user;
    }



    protected function readAction()
    {
        if( $this->_request->getParam( 'items', false ) == 'new' )
            $dateFrom = $this->user->getPreference( 'change_log.last_seen' );
        else
            $dateFrom = false;

        // get change log records for this user
        $entries = ChangeLogTable::getUpdates( $this->user['privs'],
                $dateFrom, Doctrine_Core::HYDRATE_ARRAY
        );

        // update last read preference
        $this->user->setPreference( 'change_log.last_seen', date( 'Y-m-d H:i:s' ) );
        $this->session->change_log_has_updates = 0;
        $this->view->change_log_has_updates = 0;

        $this->view->newOnly = $dateFrom; // dateFrom is false if we are displaying all
        $this->view->entries = $entries;
        $this->view->display( 'change-log/entries.tpl' );
    }
}

