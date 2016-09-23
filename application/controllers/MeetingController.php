<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Controller: Manage meetings
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MeetingController extends IXP_Controller_FrontEnd
{
    
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Meeting',
            'form'          => 'IXP_Form_Meeting',
            'pagetitle'     => 'Meetings',
        
            'titleSingular' => 'Meeting',
            'nameSingular'  => 'a meeting',
        
            'listOrderBy'    => 'date',
            'listOrderByDir' => 'DESC'
        ];
    
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ],
                    
                    'title'     => 'Title',
        
                    'date'      => [
                        'title'     => 'Date',
                        'type'      => self::$FE_COL_TYPES[ 'DATE' ]
                    ],
                    
                    'time'      => [
                        'title'     => 'Time',
                        'type'      => self::$FE_COL_TYPES[ 'TIME' ]
                    ],
                    
                    'created_by'  => [
                        'title'      => 'Created By',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'userid'
                    ]
                ];
    
                $this->_feParams->defaultAction = 'list';
                break;
    
            case \Entities\User::AUTH_CUSTUSER:
                $this->_feParams->allowedActions = [ 'read', 'rsvp', 'simple' ];
                $this->_feParams->defaultAction = 'read';
                break;
    
            default:
                $this->_feParams->allowedActions = [ 'simple' ];
                $this->_feParams->defaultAction = 'simple';
                break;
        }
    }
    
    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'm.id AS id, m.title AS title, m.date AS date, m.time AS time,
                        m.before_text AS before_text, m.after_text AS after_text,
                        m.venue AS venue, m.venue_url AS venue_url, m.created_at AS created_at,
                        m.updated_at AS updated_at,
                        u.id AS userid, u.username AS created_by'
            )
            ->from( '\\Entities\\Meeting', 'm' )
            ->leftJoin( 'm.CreatedBy', 'u' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'm.id = ?1' )->setParameter( 1, $id );
    
        return $qb->getQuery()->getResult();
    }
    

    /**
     * Preparation hook that can be overridden by subclasses for add and edit.
     *
     * This is called just before we process a possible POST / submission and
     * will allow us to change / alter the form or object.
     *
     * @param IXP_Form_Meeting $form The Send form object
     * @param \Entities\Meeting $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     */
    protected function addPrepare( $form, $object, $isEdit )
    {
        if( $isEdit )
        {
            $form->getElement( 'date' )->setValue( $object->getDate()->format( 'Y-m-d' ) );
            $form->getElement( 'time' )->setValue( $object->getTime()->format( 'H:i' ) );
        }
        
        return true;
    }
    
    /**
     *
     * @param IXP_Form_Meeting $form The form object
     * @param \Entities\Meeting $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setUpdatedBy( $this->getUser()->getId() );
        $object->setUpdatedAt( new DateTime() );
        
        if( !$isEdit )
        {
            $object->setCreatedBy( $this->getUser() );
            $object->setCreatedAt( new DateTime() );
        }
            
        return true;
    }
    
    
    /**
     *
     * @param IXP_Form_Meeting $form The form object
     * @param \Entities\Meeting $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     * @return bool If false, the form is not processed
     */
    protected function addPreFlush( $form, $object, $isEdit )
    {
    
        if( !( $object->getDate() instanceof DateTime ) )
            $object->setDate( new DateTime( $form->getValue( 'date' ) ) );
    
        if( !( $object->getTime() instanceof DateTime ) )
            $object->setTime( new DateTime( $form->getValue( 'time' ) ) );
    
        return true;
    }
    
    
    public function readAction()
    {
        $this->view->entries = $this->getD2EM()->createQuery(
                'SELECT m, mi FROM \\Entities\\Meeting m LEFT JOIN m.MeetingItems mi ORDER BY m.date DESC, mi.other_content ASC'
            )
            ->execute();

        $this->view->simple  = false;
    }

    /**
     * A simple HTML snippet for display on other websites
     */
    public function simpleAction()
    {
        $this->view->entries = $this->getD2EM()->createQuery(
                'SELECT m, mi FROM \\Entities\\Meeting m LEFT JOIN m.MeetingItems mi ORDER BY m.date DESC, mi.other_content ASC'
            )
            ->execute();
        
        $this->view->simple  = true;

        if( $this->getParam( 'nostyle', false ) )
        {
            Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
            $this->view->display( 'meeting/simple2.phtml' );
        }
        else
            $this->view->display( 'meeting/simple.phtml' );
    }


    public function rsvpAction()
    {
        die( "Needs update to IXP V2 Rewrite with Doctrine2" );
        /*
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
        */
    }


    public function composeAction()
    {
        $this->view->meeting = $meeting = $this->getD2EM()->getRepository( '\\Entities\\Meeting' )->find( $this->getParam( 'id' ) );
        
        if( !$meeting )
        {
            $this->addMessage( "Invalid meeting selected", OSS_Message::ERROR );
            $this->redirectAndEnsureDie( 'meeting/list' );
        }

        do
        {

	        if( $this->getParam( 'send', false ) )
	        {
                $this->view->to      = $this->getParam( 'to' );
                $this->view->from    = $this->getParam( 'from' );
                $this->view->bcc     = $this->getParam( 'bcc' );
                $this->view->subject = trim( stripslashes( $this->getParam( 'subject' ) ) );
                $this->view->body    = trim( stripslashes( $this->getParam( 'body' ) ) );

	            foreach( array( 'to', 'from', 'bcc' ) as $p )
	            {
	                $v = trim( $this->_getParam( $p ) );
	                $$p = $v;

	                if( $p == 'bcc' && $v == '' ) continue;

	                if( !Zend_Validate::is( $v, 'EmailAddress' ) )
	                {
	                    $this->addMessage( "Invalid email address in the '$p' field", OSS_Message::ERROR );
	                    break 2;
	                }
	            }

                $mail = $this->getMailer();
                $mail->addTo( $to );
                $mail->setFrom( $from );
                if( $bcc != '' ) $mail->addBcc( $bcc );
                $mail->setSubject( trim( stripslashes( $this->getParam( 'subject' ) ) ) );
                $mail->setBodyHtml( $this->view->render( 'meeting/email/meeting.phtml' ), 'utf8' );

	            try {
	                $mail->send();
	                $this->addMessage( "Email sent successfully", OSS_Message::SUCCESS );
	            } catch( Zend_Mail_Exception $e ) {
                    $thisaddMessage( "Error: Could not send email.", OSS_Message::ERROR );
	                $this->getLogger()->err( $e->getMessage() );
	            }

	        }

        }while( false );
    }

}

