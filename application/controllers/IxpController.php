<?php

use Entities\Switcher;
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
 * Controller: Manage IXPs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IxpController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\IXP',
            'form'          => 'IXP_Form_IXP',
            'pagetitle'     => 'IXPs',

            'titleSingular' => 'IXP',
            'nameSingular'  => 'a IXP',

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC'
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ],
                    'name'      => 'Name',
                    'shortname' => 'Shortname',
                ];

                // display the same information in the view as the list
                $this->_feParams->viewColumns = $this->_feParams->listColumns;

                $this->_feParams->defaultAction = 'list';
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }

        if( !$this->multiIXP() )
        {
            $this->AddMessage( 'Multi IXP mode is not enabled.' );
            $this->redirectAndEnsureDie();
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
            ->select( 'i.id AS id, i.name AS name,
                i.shortname AS shortname, i.address1 AS address1, i.address2 AS address2,
                i.address3 AS address3, i.address4 AS address4, i.country AS country'
            )
            ->from( '\\Entities\\IXP', 'i' );

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'i.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }


    public function assignCustomerAction()
    {
        
        $ixp = $this->loadObject( $this->getParam( 'id' ) );
        $customer = $this->getD2R( '\\Entities\\Customer' )->find( $this->getParam( "cid", false ) );

        if( !$customer )
        {
            $this->addMessage( "Could not load requested object", OSS_Message::ERROR );
            $this->redirectAndEnsureDie( "/customer/list/ixp/" .  $ixp->getId() );
        }

        $ixp->addCustomer( $customer );
        $customer->addIXP( $ixp );
        $this->getD2EM()->flush();

        $this->addMessage( "Customer was assigned to IXP successfully.", OSS_Message::SUCCESS );
        if( $this->getParam( 'overview', false ) )
            $this->redirectAndEnsureDie( "/customer/overview/tab/ixps/id/" .  $customer->getId() );
        else
            $this->redirectAndEnsureDie( "/customer/list/ixp/" .  $ixp->getId() );
    }

    public function unassignCustomerAction()
    {
        
        $ixp = $this->loadObject( $this->getParam( 'id' ) );
        $customer = $this->getD2R( '\\Entities\\Customer' )->find( $this->getParam( "cid", false ) );

        if( !$customer )
        {
            $this->addMessage( "Could not load requested object", OSS_Message::ERROR );
            $this->redirectAndEnsureDie( "/customer/list/ixp/" .  $ixp->getId() );
        }

        $ixp->removeCustomer( $customer );
        $customer->removeIXP( $ixp );
        $this->getD2EM()->flush();

        $this->addMessage( "Customer was unassigned to IXP successfully.", OSS_Message::SUCCESS );
        $this->redirectAndEnsureDie( "/customer/list/ixp/" .  $ixp->getId() );
    }      

}

