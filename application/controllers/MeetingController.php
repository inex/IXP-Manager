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

class MeetingController extends INEX_Controller_FrontEnd
{
    public function init()
    {
        $this->frontend['defaultOrdering'] = 'date';
        $this->frontend['model']           = 'Meeting';
        $this->frontend['name']            = 'Meeting';
        $this->frontend['pageTitle']       = 'Meetings';

        $this->frontend['columns'] = array(

            'displayColumns' => array(
                'id', 'title', 'date', 'time', 'created_by', 'created_at'
            ),

            'viewPanelRows'  => array( 'title', 'before_text', 'after_text', 'date', 'time', 'venue', 'venue_url', 'created_by', 'created_at', 'updated_by', 'updated_at' ),

            'viewPanelTitle' => 'title',

            'sortDefaults' => array(
                'column' => 'date', 'order' => 'desc'
            ),

            'id' => array(
                'label' => 'ID',
                'hidden' => true
            ),

            'title' => array(
                'label' => 'Title',
                'sortable' => false
            ),

            'date' => array(
                'label' => 'Date',
                'sortable' => true
            ),

            'time' => array(
                'label' => 'Time',
                'sortable' => false
            ),

            'venue' => array(
                'label' => 'Venue',
                'sortable' => true
            ),

            'venue_url' => array(
                'label' => 'Venue URL',
                'sortable' => false
            ),

            'before_text' => array(
                'label' => 'Preamble'
            ),

            'after_text' => array(
                'label' => 'Postamble'
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
            'read'   => User::AUTH_CUSTUSER,
            'rsvp'   => User::AUTH_CUSTUSER,
            'simple' => User::AUTH_PUBLIC
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
    protected function addEditPreSave( $row, $isEdit, $form )
    {
        $row['updated_by'] = $this->user;

        if( !$isEdit )
            $row['created_by'] = $this->user;
    }


    public function readAction()
    {
        $entries = Doctrine_Query::create()
            ->from( 'Meeting m' )
            ->leftJoin( 'm.MeetingItem mi' )
            ->orderBy( 'm.date DESC, mi.other_content ASC' )
            ->execute( null, Doctrine_Core::HYDRATE_ARRAY );

        $this->view->entries = $entries;
        $this->view->simple  = false;
        $this->view->display( 'meeting/meetings.tpl' );
    }

    /**
     * A simple HTML snippet for display on other websites
     */
    public function simpleAction()
    {
        $entries = Doctrine_Query::create()
            ->from( 'Meeting m' )
            ->leftJoin( 'm.MeetingItem mi' )
            ->orderBy( 'm.date DESC, mi.other_content ASC' )
            ->execute( null, Doctrine_Core::HYDRATE_ARRAY );

        $this->view->entries = $entries;
        $this->view->simple  = true;

        if( $this->_request->getParam( 'nostyle', false ) )
            $this->view->display( 'meeting/simple2.tpl' );
        else
            $this->view->display( 'meeting/simple.tpl' );
    }


    public function rsvpAction()
    {
        $meeting = Doctrine_Core::getTable( 'Meeting' )->find( $this->_request->getParam( 'id', null ) );

        if( !$meeting )
            exit;

        $answer = $this->_getParam( 'answer', null );

        if( !in_array( $answer, array( 'attend', 'noattend', 'skip', 'dontask' ) ) )
            exit;

        $response = 1;
        $msg = false;

        switch( $answer )
        {
            case 'skip':
                $this->getLogger()->debug( 'User skipped meeting RSVP request' );
                $this->session->dashboard_skip_meeting = true;
                break;

            case 'dontask':
                $this->getLogger()->debug( 'User asked not to be asked to RSVP again' );
                $this->getUser()->setPreference( 'meeting.attending.' . $meeting['id'], 'DONT_ASK' );
                break;

            case 'attend':
                $msg = 'ATTEND';
                $this->getLogger()->debug( 'User will be attending this meeting' );
                $this->getUser()->setPreference( 'meeting.attending.' . $meeting['id'], 'ATTENDING' );
                break;

            case 'noattend':
                $msg = 'NOT ATTEND';
                $this->getLogger()->debug( 'User will not be attending this meeting' );
                $this->getUser()->setPreference( 'meeting.attending.' . $meeting['id'], 'NOT_ATTENDING' );
                break;

        }

        if( $msg !== false )
        {
            $mail = new Zend_Mail();
            $mail->addTo( $this->config['meeting']['rsvp_to_email'], $this->config['meeting']['rsvp_to_name'] )
                 ->setSubject( '[Meeting RSVP] ' . $msg . ': ' . $this->getUser()->email . '/' . $this->customer['name'] )
                 ->setBodyText( "\nThis is an automated message from the IXP Manager.\n\n"
                        . "The following person has indicated that they will $msg the meeting scheduled for {$meeting['date']}\n\n"
                        . "{$this->getUser()->username} / {$this->getUser()->email} / {$this->customer['name']}\n\n"
                 );
            $mail->setFrom( $this->_config['identity']['autobot']['email'] );

            try {
                $mail->send();
            } catch( Zend_Mail_Exception $e ) {
                $response = 0;
                $this->getLogger()->err( $e->getMessage() );
            }
        }

        $this->getResponse()
            ->setHeader( 'Content-Type', 'text/html' )
            ->setBody( Zend_Json::encode( array( 'response' => $response ) ) )
            ->sendResponse();

        exit;
    }


    public function composeAction()
    {
        $meeting = Doctrine_Query::create()
            ->from( 'Meeting m' )
            ->where( 'm.id = ?', $this->_request->getParam( 'id', null ) )
            ->leftJoin( 'm.MeetingItem mi' )
            ->orderBy( 'm.date DESC, mi.other_content ASC' )
            ->fetchOne( null, Doctrine_Core::HYDRATE_ARRAY );

        if( !$meeting )
        {
            $this->session->message = new INEX_Message(
                "Invalid meeting selected", INEX_Message::MESSAGE_TYPE_ERROR
            );
            return( $this->_redirect( 'meeting/list' ) );
        }

        $this->view->meeting = $meeting;

        do
        {

	        if( $this->_getParam( 'send', 0 ) == 1 )
	        {
                $this->view->to      = $this->_getParam( 'to' );
                $this->view->from    = $this->_getParam( 'from' );
                $this->view->bcc     = $this->_getParam( 'bcc' );
                $this->view->subject = trim( stripslashes( $this->_getParam( 'subject' ) ) );
                $this->view->body    = trim( stripslashes( $this->_getParam( 'body' ) ) );

	            foreach( array( 'to', 'from', 'bcc' ) as $p )
	            {
	                $v = trim( $this->_getParam( $p ) );
	                $$p = $v;

	                if( $p == 'bcc' && $v == '' ) continue;

	                if( !Zend_Validate::is( $v, 'EmailAddress' ) )
	                {
	                    $this->view->message = new INEX_Message( "Invalid email address in the '$p' field",
	                           INEX_Message::MESSAGE_TYPE_ERROR
	                    );
	                    break 2;
	                }
	            }

                $mail = new Zend_Mail();
                $mail->addTo( $to );
                $mail->setFrom( $from );
                if( $bcc != '' ) $mail->addBcc( $bcc );
                $mail->setSubject( trim( stripslashes( $this->_getParam( 'subject' ) ) ) );
                $mail->setBodyHtml( $this->view->render( 'meeting/email/meeting.tpl' ), 'utf8' );

	            try {
	                $mail->send();
	                $this->view->message = new INEX_Message( "Email sent successfully", INEX_Message::MESSAGE_TYPE_SUCCESS );
	            } catch( Zend_Mail_Exception $e ) {
                    $this->view->message = new INEX_Message( "Error: Could not send email.", INEX_Message::MESSAGE_TYPE_ERROR );
	                $this->getLogger()->err( $e->getMessage() );
	            }

	        }

        }while( false );

        $this->view->display( 'meeting/compose.tpl' );
    }

}

?>