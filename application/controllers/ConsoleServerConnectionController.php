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
 * Controller: Manage connections to console servers
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConsoleServerConnectionController extends IXP_Controller_FrontEnd
{
    
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\ConsoleServerConnection',
            'form'          => 'IXP_Form_ConsoleServerConnection',
            'pagetitle'     => 'Console Server Connections',
        
            'titleSingular' => 'Console Server Connection',
            'nameSingular'  => 'a console server connection',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'description',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
        
                'id'        => [ 'title' => 'UID', 'display' => false ],
            
                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'view',
                    'idField'    => 'customerid'
                ],
            
                'description'  => 'Description',
                
                'switch'  => [
                    'title'      => 'Console Server',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'switch',
                    'action'     => 'view',
                    'idField'    => 'switchid'
                ],
                
                'port'    => 'Port'
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'speed'       => 'Speed',
                'parity'      => 'Parity',
                'stopbits'    => 'Stopbits',
                'flowcontrol' => 'Flow Control',
                'autobaud'    => 'Autobaud',
                'notes'       => 'Notes'
            ]
        );
    }
    
    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'csc.id AS id, csc.description AS description, csc.port AS port,
                csc.speed AS speed, csc.parity AS parity, csc.stopbits AS stopbits,
                csc.flowcontrol AS flowcontrol, csc.autobaud AS autobaud,
                csc.notes AS notes, c.name AS customer, c.id AS customerid,
                s.name AS switch, s.id AS switchid'
            )
            ->from( '\\Entities\\ConsoleServerConnection', 'csc' )
            ->leftJoin( 'csc.Customer', 'c' )
            ->leftJoin( 'csc.Switcher', 's' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'csc.id = ?1' )->setParameter( 1, $id );
        
        return $qb->getQuery()->getResult();
    }
    
    
    /**
     *
     * @param IXP_Form_ConsoleServerConnection $form The form object
     * @param \Entities\ConsoleServerConnection $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            $form->getElement( 'custid' )->setValue( $object->getCustomer()->getId() );
            $form->getElement( 'switchid' )->setValue( $object->getSwitcher()->getId() );
        }
    }
    
    
    /**
     *
     * @param IXP_Form_ConsoleServerConnection $form The form object
     * @param \Entities\ConsoleServerConnection $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setCustomer(
            $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $form->getElement( 'custid' )->getValue() )
        );
    
        $object->setSwitcher(
            $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->find( $form->getElement( 'switchid' )->getValue() )
        );
    
        return true;
    }
    
}

