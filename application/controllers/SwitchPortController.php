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
 * Controller: Manage switch ports
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchPortController extends INEX_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\SwitchPort',
            'form'          => 'INEX_Form_Switch_Port',
            'pagetitle'     => 'Switch Ports',
        
            'titleSingular' => 'Switch Port',
            'nameSingular'  => 'a switch port',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
        
                'id'        => [ 'title' => 'UID', 'display' => false ],
                
                'switch'  => [
                    'title'      => 'Switch',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'switch',
                    'action'     => 'view',
                    'idField'    => 'switchid'
                ],
            
                'name'           => 'Name',
                
                'type'  => [
                    'title'    => 'Type',
                    'type'     => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'   => \Entities\SwitchPort::$TYPES
                ]
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = $this->_feParams->listColumns;
    }
    
    
    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $this->view->switches = $switches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getNames();
        
        $qb = $this->getD2EM()->createQueryBuilder()
        ->select( 'sp.id AS id, sp.name AS name,
            sp.type AS type, s.name AS switch, s.id AS switchid'
        )
        ->from( '\\Entities\\SwitchPort', 'sp' )
        ->leftJoin( 'sp.Switcher', 's' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'sp.id = ?1' )->setParameter( 1, $id );
    
        if( ( $sid = $this->getParam( 'switch', false ) ) && isset( $switches[$sid] ) )
        {
            $this->view->sid = $sid;
            $qb->where( 's.id = ?2' )->setParameter( 2, $sid );
        }
        
        return $qb->getQuery()->getResult();
    }
    
    
    /**
     *
     * @param INEX_Form_SwitchPort $form The form object
     * @param \Entities\SwitchPort $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            $form->getElement( 'switchid' )->setValue( $object->getSwitcher()->getId() );
            $form->getElement( 'name' )->setAttrib( 'readonly', 'readonly' );
        }
    }
    
    
    /**
     *
     * @param INEX_Form_SwitchPort $form The form object
     * @param \Entities\SwitchPort $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setSwitcher(
            $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->find( $form->getElement( 'switchid' )->getValue() )
        );
    
        return true;
    }
    
        
    public function addAction()
    {
        $this->view->form = $form = new INEX_Form_Switch_AddPorts();
    
        if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
        {
            if( !( $switch = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->find( $form->getValue( 'switchid' ) ) ) )
                throw new INEX_Exception( 'Unknown switch in request' );
            
            for( $i = 0; $i < $form->getValue( 'numports' ); $i++ )
            {
                $sp = new \Entities\SwitchPort();
                $sp->setSwitcher( $switch );
                $sp->setType( intval( $_POST[ 'np_type' . $i ] ) );
                $sp->setName( trim( stripslashes( $_POST[ 'np_name' . $i ] ) ) );
                $this->getD2EM()->persist( $sp );
            }

            $this->getD2EM()->flush();

            
            $msg = "{$form->getValue( 'numports' )} new ports created for switch {$switch->getName()}.";
            $this->getLogger()->info( $msg );
            $this->addMessage( $msg, OSS_Message::SUCCESS );
            
            $this->redirect( 'switch-port/list/switch/' . $switch->getId() );
        }
    }
    
    
    
    public function ajaxGetAction()
    {
        $dql = "SELECT sp.name AS name, sp.type AS type, sp.id AS id
                    FROM \\Entities\\SwitchPort sp
                        LEFT JOIN sp.Switcher s
                        LEFT JOIN sp.PhysicalInterface pi
                    WHERE
                        s.id = ?1 ";
        
        if( $this->getParam( 'id', null ) !== null )
            $dql .= 'AND ( pi.id IS NULL OR pi.id = ?2 )';
        else
            $dql .= 'AND pi.id IS NULL';

        $dql .= " ORDER BY sp.id ASC";
        
        $query = $this->getD2EM()->createQuery( $dql );
        $query->setParameter( 1, $this->getParam( 'switchid', 0 ) );
        
        if( $this->getParam( 'id', null ) !== null )
            $query->setParameter( 2, $this->getParam( 'id' ) );
        
        $ports = $query->getArrayResult();
    
        foreach( $ports as $id => $port )
            $ports[$id]['type'] = \Entities\SwitchPort::$TYPES[ $port['type'] ];
    
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody( Zend_Json::encode( $ports ) )
            ->sendResponse();
        
        die(); //FIXME I shouldn't have to die() here...
    }
    
    
}



