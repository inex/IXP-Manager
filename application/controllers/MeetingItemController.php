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
 * Controller: Manage meeting presentations
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MeetingItemController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\MeetingItem',
            'form'          => 'IXP_Form_Meeting_Item',
            'pagetitle'     => 'Presentations',
        
            'titleSingular' => 'Presentation',
            'nameSingular'  => 'a presentation',
        
            'listOrderBy'    => 'name',
            'listOrderByDir' => 'DESC'
        ];
    
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ],
                    'title'     => 'Title',
                    'name'      => 'Name',
                    'company'   => 'Company',
                    
                    'mtitle'  => [
                        'title'      => 'Meeting',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'meeting',
                        'action'     => 'view',
                        'idField'    => 'mid'
                    ]
                ];
                $this->_feParams->defaultAction = 'list';
                break;
    
            case \Entities\User::AUTH_CUSTUSER:
                $this->_feParams->allowedActions = [ 'get-presentation' ];
                break;
    
            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
                break;
        }
    }


    /**
     * Provide array of presentations
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $this->view->meetings = $meetings = $this->getD2EM()->getRepository( '\\Entities\\Meeting' )->getTitles();
        
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'mi.id AS id, mi.title AS title, mi.name AS name, mi.role AS role,
                        mi.email AS email, mi.company AS company, mi.company_url AS company_url,
                        mi.summary AS summary, mi.presentation AS presentation, mi.filename AS filename,
                        mi.created_by AS created_by, mi.created_at AS created_at,
                        mi.updated_by AS updated_by, mi.updated_at AS updated_at,
                        m.id AS mid, m.title AS mtitle'
            )
            ->from( '\\Entities\\MeetingItem', 'mi' )
            ->leftJoin( 'mi.Meeting', 'm' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'mi.id = ?1' )->setParameter( 1, $id );
    
        if( ( $mid = $this->getParam( 'mid', false ) ) && isset( $meetings[$mid] ) )
        {
            $this->view->mid = $mid;
            $qb->where( 'm.id = ?2' )->setParameter( 2, $mid );
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Return the presentation file
     */
    protected function getPresentationAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
        
        if( !( $pres = $this->getD2EM()->getRepository( '\\Entities\\MeetingItem' )->find( $this->getParam( 'id', null ) ) ) )
        {
            $this->addMessage(
                'The requested presentation does not exist or does not have an associated file attached to it.',
                OSS_Message::ERROR
            );
            $this->redirect( 'meeting/read' );
        }

        $fn = "IXP_Members_Meeting_{$pres->getMeeting()->getDate()->format( 'Y-m-d' )}_({$pres->getId()}).";

        // What kind of file do we have?
        if( preg_match( '/pdf$/i', $pres->getFilename() ) ) {
            header('Content-type: application/pdf');
            $fn .= 'pdf';
        }
        else if( preg_match( '/ppt$/i', $pres->getFilename() ) ) {
            header('Content-type: application/vnd.ms-powerpoint');
            $fn .= 'ppt';
        }
        else if( preg_match( '/pps$/i', $pres->getFilename() ) ) {
            header('Content-type: application/vnd.ms-powerpoint');
            $fn .= 'pps';
        }
        else if( preg_match( '/pptx$/i', $pres->getFilename() ) ) {
            header( 'Content-type: application/vnd.ms-powerpoint' );
            $fn .= 'pptx';
        }
        else {
            header( 'Content-type: application/octet-stream' );
            $fn .= substr( $pres->getFilename(), strrpos( $pres->getFilename(), '.' ) );
        }


        header( 'Content-Disposition: attachment; filename="' . $fn . '"' );

        echo @file_get_contents( self::getMeetingsDirectory() . DIRECTORY_SEPARATOR
                . $pres->getMeeting()->getId() . DIRECTORY_SEPARATOR . $pres->getPresentation()
        );
    }


    /**
     *
     * @param IXP_Form_Meeting_Item $form
     * @param \Entities\MeetingItem $object
     * @param bool $isEdit
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
            $form->getElement( 'meeting_id' )->setValue( $object->getMeeting()->getId() );
    }
    
    
    /**
     *
     * @param IXP_Form_Meeting_Item $form
     * @param \Entities\MeetingItem $object
     * @param bool $isEdit
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setMeeting(
            $this->getD2EM()->getRepository( '\\Entities\\Meeting' )->find( $form->getElement( 'meeting_id' )->getValue() )
        );
    
        return true;
    }
    
    /**
     *
     * @param IXP_Form_Meeting_Item $form
     * @param \Entities\MeetingItem $object
     * @param bool $isEdit
     */
    protected function addPreFlush( $form, $object, $isEdit )
    {
        $object->setUpdatedBy( $this->getUser()->getId() );
        $object->setUpdatedAt( new DateTime() );
        
        if( !$isEdit )
        {
            $object->setCreatedBy( $this->getUser()->getId() );
            $object->setCreatedAt( new DateTime() );
        }

        // is there a file upload?
        if( $form->getValue( 'presentation' ) != '' )
        {
            // lets make more memory available for large files
            ini_set( 'memory_limit', '512M' );

            $this->getLogger()->debug( 'Received upload of file: ' . $form->getValue( 'presentation' ) );

            // Zend sticks the original filename in the form variable
            $object->setFilename( $form->getValue( 'presentation' ) );

            // make sure meetings exists
            if( !is_dir( self::getMeetingsDirectory() ) && !@mkdir( self::getMeetingsDirectory() ) )
            {
                $this->getLogger()->crit( 'Could not create presentations directory.' );
                throw new IXP_Exception( 'Presentations directory does not exist and could not be created.' );
            }

            // now, create a directory for this meeting if it does not already exists
            $meeting_dir = self::getMeetingsDirectory() . DIRECTORY_SEPARATOR . $object->getMeeting()->getId();

            if( !is_dir( $meeting_dir ) && !@mkdir( $meeting_dir ) )
            {
                $this->getLogger()->crit( 'Could not create meeting directory.' );
                throw new IXP_Exception( 'Meeting directory does not exist and could not be created.' );
            }

            // get the extension for this presentation

            if( strrpos( $object->getFilename(), '.' ) === false )
                $exten = '';
            else
                $exten = substr( $object->getFilename(), strrpos( $object->getFilename(), '.' ) );

            // we need the row ID so we'll do a save
            if( !$isEdit )
                $this->getD2EM()->persist( $object );
            $this->getD2EM()->flush();
            
            $object->setPresentation( $object->getId() . $exten );
            $this->getLogger()->debug( 'Uploaded file will be saved as: ' . $object->getPresentation() );

            // delete an existing file in case we're updating
            $ePres = $meeting_dir . DIRECTORY_SEPARATOR . $object->getPresentation();
            if( @file_exists( $ePres ) )
            {
                $this->getLogger()->debug( 'Pre-existing file exists so deleting' );
                @unlink( $ePres );
            }

            @rename( $form->getElement( 'presentation' )->getFilename(), $ePres );
        }
        
        return true;
    }

    /**
     * Before deleting a meeting, delete meeting items.
     *
     * @param \Entities\MeetingItem $object
     */
    protected function preDelete( $object )
    {
        // if a presentation exists, remove it
        $dir = self::getMeetingsDirectory() . DIRECTORY_SEPARATOR . $object->getMeeting()->getId();
        
        $file = $dir . DIRECTORY_SEPARATOR . $object->getPresentation();
        
        if( file_exists( $file ) )
            @unlink( $file );
        
        // remove the directory also if it is empty
        if( count( @scandir( $dir ) ) == 2 )
            @rmdir( $dir );
        
        return true;
    }
    
    
    /**
     * Return the path where meeting presentations are stored
     * @return string The path where meeting presentations are stored
     */
    public static function getMeetingsDirectory()
    {
        // We're going to store presentations in the var directory under meetings.
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                . 'var' . DIRECTORY_SEPARATOR . 'meetings';
    }
}
