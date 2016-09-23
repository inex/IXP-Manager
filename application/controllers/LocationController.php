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
 * Controller: Manage locations (data centres)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LocationController extends IXP_Controller_FrontEnd
{

    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );

        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Location',
            'form'          => 'IXP_Form_Location',
            'pagetitle'     => 'Locations',

            'titleSingular' => 'Location',
            'nameSingular'  => 'a location',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],
                'name'      => 'Name',
                'shortname' => 'Shortname',
                'tag'       => 'Tag',
                'nocphone'  => 'NOC Phone',
                'nocemail'  => 'NOC Email'
            ]
        ];

        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'address'     => 'Address',
                'nocfax'      => 'NOC Fax',
                'officephone' => 'Office Phone',
                'officefax'   => 'Office Fax',
                'officeemail' => 'Office Email',
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
        ->select(
                'l.id AS id, l.name AS name, l.shortname AS shortname, l.tag AS tag,
                l.nocphone AS nocphone, l.nocemail AS nocemail, l.address AS address,
                l.nocfax AS nocfax, l.officephone AS officephone, l.officefax AS officefax,
                l.officeemail AS officeemail, l.notes AS notes'
            )
        ->from( '\\Entities\\Location', 'l' );

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'l.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
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
        if( count( $object->getCabinets() ) )
        {
            $this->addMessage(
                "Could not delete the location as at least one cabinet is located here. Reassign or delete the cabinet first.",
                OSS_Message::ERROR
            );
            return false;
        }

        return true;
    }

}
