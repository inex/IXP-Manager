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

class MeetingItemController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'date';
        $this->frontend['model']           = 'MeetingItem';
        $this->frontend['name']            = 'MeetingItem';
        $this->frontend['pageTitle']       = 'Meeting Item';

        $this->frontend['columns'] = array(

            'displayColumns' => array(
                'id', 'meeting_id', 'title', 'name', 'company'
            ),

            'viewPanelRows'  => array( 'meeting_id', 'title', 'name', 'role', 'email',
                'company', 'company_url', 'summary', 'presentation', 'video_url', 'other_content',
                'created_by', 'created_at', 'updated_by', 'updated_at'
            ),

            'viewPanelTitle' => 'title',

	        'sortDefaults' => array(
	            'column' => 'meeting_id', 'order' => 'desc'
	        ),
            
            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'meeting_id' => array(
                'type' => 'hasOne',
                'model' => 'Meeting',
                'controller' => 'meeting',
                'field' => 'date',
                'label' => 'Meeting&nbsp;Date',
                'sortable' => true
            ),

            'title' => array(
                'label' => 'Title',
                'sortable' => false
            ),

            'name' => array(
                'label' => 'Name',
                'sortable' => false
            ),

            'role' => array(
                'label' => 'Role',
                'sortable' => false
            ),

            'email' => array(
                'label' => 'E-Mail',
                'sortable' => false
            ),

            'company' => array(
                'label' => 'Company',
                'sortable' => false
            ),

            'company' => array(
                'label' => 'Company URL',
                'sortable' => false
            ),

            'summary' => array(
                'label' => 'Summary',
                'sortable' => false
            ),

            'presentation' => array(
                'label' => 'Presentation',
                'sortable' => false
            ),

            'video_url' => array(
                'label' => 'Video',
                'sortable' => false
            ),

            'other_content' => array(
                'label' => 'Other Content?',
                'sortable' => true
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
            ),

            'updated_by' => array(
                'type' => 'hasOne',
                'model' => 'User',
                'controller' => 'user',
                'field' => 'username',
                'label' => 'Updated By',
                'sortable' => true
            ),

            'updated_at' => array(
                'label' => 'Created At'
            )


        );

        // Override global auth level requirement for specific actions
        $this->frontend['authLevels'] = array(
            'get-presentation' => User::AUTH_CUSTUSER
        );

        parent::feInit();

    }

    /**
     * Return the presentation file
     */
    protected function getPresentationAction()
    {
        if( !( $pres = Doctrine_Core::getTable( 'MeetingItem' )->find( $this->_request->getParam( 'id', null ) ) ) )
        {
            $this->session->message = new INEX_Message(
                'The request presentation does not exist or does not have an associated file attached to it.',
                INEX_Message::MESSAGE_TYPE_ERROR
            );
            $this->_redirect( 'meeting/read' );
        }

        $fn = "INEX_Members_Meeting_" . $pres['Meeting']['date'] . "_(" . $pres['id'] . ').';

        // What kind of file do we have?
        if( preg_match( '/pdf$/i', $pres['filename'] ) ) {
            header('Content-type: application/pdf');
            $fn .= 'pdf';
        }
        else if( preg_match( '/ppt$/i', $pres['filename'] ) ) {
            header('Content-type: application/vnd.ms-powerpoint');
            $fn .= 'ppt';
        }
        else if( preg_match( '/pps$/i', $pres['filename'] ) ) {
            header('Content-type: application/vnd.ms-powerpoint');
            $fn .= 'pps';
        }
        else if( preg_match( '/pptx$/i', $pres['filename'] ) ) {
            header( 'Content-type: application/vnd.ms-powerpoint' );
            $fn .= 'pptx';
        }
        else {
            header( 'Content-type: application/octet-stream' );
            $fn .= substr( $pres['filename'], strrpos( $pres['filename'], '.' ) );
        }


        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="' . $fn . '"');

        $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'var' . DIRECTORY_SEPARATOR . 'meetings' . DIRECTORY_SEPARATOR
                    . $pres['meeting_id'] . DIRECTORY_SEPARATOR . $pres['presentation'];

        echo @file_get_contents( $file );
    }

    /**
     * Before we save, set the created by field.
     *
     * @param unknown_type $log The Change Log object
     * @param unknown_type $isEdit True if the log is being edited, flase if it's being added
     * @param unknown_type $form The submitted form object
     */
    protected function addEditPreSave( $row, $isEdit, $form )
    {
        $row['updated_by'] = $this->user;

        if( !$isEdit )
            $row['created_by'] = $this->user;

        // is there a file upload?
        if( $form->getValue( 'presentation' ) != '' )
        {
            // lets make more memory available for large files
            ini_set( 'memory_limit', '128M' );

            $this->logger->debug( 'Received upload of file: ' . $form->getValue( 'presentation' ) );

            // Zend sticks the original filename in the form variable
            $row['filename'] = $form->getValue( 'presentation' );

            // We're going to store presentations in the var directory under meetings.

            $root = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'var' . DIRECTORY_SEPARATOR . 'meetings';

            // make sure meetings exists
            if( !is_dir( $root ) && !@mkdir( $root ) )
            {
                $this->logger->crit( 'Could not create presentations directory.' );
                die( 'ERROR: Could not create the presentations root directory.' );
            }

            // now, create a directory for this meeting if it does not already exists
            $meeting_dir = $root . DIRECTORY_SEPARATOR . $row['meeting_id'];

            if( !is_dir( $meeting_dir ) && !@mkdir( $meeting_dir ) )
            {
                $this->logger->crit( 'Could not create meetings directory.' );
                die( 'ERROR: Could not create presentations directory.' );
            }

            // get the extension for this presentation

            if( strrpos( $row['filename'], '.' ) === false )
                $exten = '';
            else
                $exten = substr( $row['filename'], strrpos( $row['filename'], '.' ) );

            // we need the row ID so we'll do a save
            $row->save();

            $row['presentation'] = $row['id'] . $exten;
            $this->logger->debug( 'Uploaded file will be saved as: ' . $row['presentation'] );

            // delete an existing file in case we're updating
            if( file_exists( $meeting_dir . DIRECTORY_SEPARATOR . $row['presentation'] ) )
            {
                $this->logger->debug( 'Pre-existing file exists so deleteing' );
                @unlink( $meeting_dir . DIRECTORY_SEPARATOR . $row['presentation'] );
            }

            @rename( $form->presentation->getFilename(), $meeting_dir . DIRECTORY_SEPARATOR . $row['presentation'] );
        }
    }

    /**
     * Before deleting a meeting, delete meeting items.
     */
    protected function preDelete( $object = null )
    {
        Doctrine_Core::getTable( 'MeetingItem' )->findByMeetingId(
                $object['id']
        )->delete();
    }


    /**
     * Add a filter to the list
     */
    protected function _preList( $dataQuery )
    {
        // assign the filterable items to the view
        $this->view->entries  = Doctrine_Query::create()
            ->select( 'm.id, m.title, m.date' )
            ->from( 'Meeting m' )
            ->orderBy( 'm.date DESC' )
            ->execute( null, Doctrine_Core::HYDRATE_ARRAY );

        // did the user specify a specific meeting?
        if( ( $id = $this->_request->getParam( 'meeting_id', 0 ) ) )
        {
            $dataQuery->leftJoin( 'x.Meeting m' )
                ->andWhere( 'm.id = ?', $id );

            $this->view->filter_id = $id;
        }

        return $dataQuery;
    }
}

?>