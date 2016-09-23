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
 * Controller: Manage contact groups
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
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
                    'xlator'         => $this->_options['contact']['group']['types']
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
        $types = isset( $this->_options['contact']['group']['types'] ) ? $this->_options['contact']['group']['types'] : [];
    
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'o.id AS id, o.name AS name, o.type AS type,
                    o.created AS created, o.description AS description,
                    o.active AS active, o.limited_to AS limit'
            )
            ->from( '\\Entities\\ContactGroup', 'o' )
            ->where( 'o.type IN( ?1 )' )
            ->setParameter( 1, array_keys( $types ) );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'o.id = ?2' )->setParameter( 2, $id );

        return $qb->getQuery()->getResult();
    }
    
     /**
     * Post process hook that can be overridden by subclasses for add and edit actions.
     *
     * This is called immediately after the initstantiation of the form object and, if
     * editing, includes the Doctrine2 entity `$object`.
     *
     * If you need to have, for example, edit values set in the form, then use the
     * `addPrepare()` hook rather than this one.
     *
     * @see addPrepare()
     * @param OSS_Form $form The form object
     * @param \Entities\ContactGroup $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        $form->getElement( 'type' )->setMultiOptions( $this->_options['contact']['group']['types'] );
    }
    
    
    /**
     * Postvalidation hook for add / edit
     *
     * @param OSS_Form $form The Send form object
     * @param \Entities\ContactGroup $group The Doctrine2 contact group entity
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

