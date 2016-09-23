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
 * Controller: Manage cabinets (racks)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CabinetController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );

        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Cabinet',
            'form'          => 'IXP_Form_Cabinet',
            'pagetitle'     => 'Cabinets',

            'titleSingular' => 'Cabinet',
            'nameSingular'  => 'a cabinet',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],

                'location'  => [
                    'title'      => 'Location',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'location',
                    'action'     => 'view',
                    'idField'    => 'locationid'
                ],

                'name'         => 'Name',
                'cololocation' => 'Colo Location',
                'height'       => 'Height'
            ]
        ];

        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'type'       => 'Type',
                'notes'      => 'Notes'
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
            ->select( 'c.id as id, c.name as name, c.cololocation as cololocation, c.height AS height,
                c.type AS type, c.notes AS notes, l.id AS locationid, l.name AS location'
            )
        ->from( '\\Entities\\Cabinet', 'c' )
        ->leftJoin( 'c.Location', 'l' );

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'c.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }


    /**
     *
     * @param IXP_Form_Cabinet $form The form object
     * @param \Entities\Cabinet $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
            $form->getElement( 'locationid' )->setValue( $object->getLocation()->getId() );
    }


    /**
     *
     * @param IXP_Form_Cabinet $form The form object
     * @param \Entities\Cabinet $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setLocation(
            $this->getD2EM()->getRepository( '\\Entities\\Location' )->find( $form->getElement( 'locationid' )->getValue() )
        );

        return true;
    }

    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why.
     *
     * @param object $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( $object )
    {
        if( count( $object->getCustomerEquipment() ) )
        {
            $this->addMessage(
                "Could not delete the cabinet as at least one piece of customer equipment is located here. Reassign or delete that kit first.",
                OSS_Message::ERROR
            );
            return false;
        }

        if( count( $object->getSwitches() ) )
        {
            $this->addMessage(
                "Could not delete the cabinet as at least one switch is located here. Reassign or delete the switch first.",
                OSS_Message::ERROR
            );
            return false;
        }

        return true;
    }

}
