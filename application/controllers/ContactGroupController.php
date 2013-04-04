<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Manage contact groups
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactGroupController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\ContactGroup',
            'form'          => 'IXP_Form_ContactGroup',
            'pagetitle'     => 'Contact Groups',
        
            'titleSingular' => 'Contact Group',
            'nameSingular'  => 'a contact group',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'name'      => 'Name',
                'type'        => [
                    'title'          => 'Type',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => \Entities\ContactGroup::$TYPES
                ],
                'created'        => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'active'      => 'Active',
                'limit'       => 'Limit',
                'description' => 'Description'
            ]
        );
        
    }
    
    
    /**
     * Provide array of objects for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'o.id AS id, o.name AS name, o.type AS type,
                    o.created AS created, o.description AS description,
                    o.active AS active, o.limited_to AS limit'
            )
            ->from( '\\Entities\\ContactGroup', 'o' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'o.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }
    
    /**
     * Postvalidation hook for add / edit
     *
     * Adding values for new announcement.
     *
     * @param OSS_Form $form The Send form object
     * @param \Entities\User $user The Doctrine2 User entity
     * @param bool $isEdit True if we are editing, otherwise false
     * @return bool If false, the form is not processed
     */
    protected function addPostValidate( $form, $group, $isEdit )
    {
        if( !$isEdit )
            $group->setCreated( new DateTime() );

        return true;
    }
    
 }

