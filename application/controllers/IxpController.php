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
 * Controller: Manage IXPs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
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
                    'id'                   => [ 'title' => 'UID', 'display' => false ],
                    'name'                 => 'Name',
                    'shortname'            => 'Shortname',
                    'aggregate_graph_name' => 'Aggregate Graph Name'
                ];

                // display the same information in the view as the list
                $this->_feParams->viewColumns = $this->_feParams->listColumns;

                $this->_feParams->defaultAction = 'list';
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
    }

    
    protected function listPreamble()
    {
        if( !$this->multiIXP() )
        {
            $this->addMessage(
                    'Multi-IXP mode has not been enabled. '
                    . 'Please see <a href="https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality">this page</a> '
                    . 'for more information and documentation. <strong>DO NOT ADD MORE THAN ONE IXP WITHOUT ENABLING MULTI-IXP MODE!</strong>',
                    OSS_Message::WARNING
            );
        }
    }
    
    /**
     * Preparation hook that can be overridden by subclasses for add and edit.
     *
     * This is called just before we process a possible POST / submission and
     * will allow us to change / alter the form or object.
     *
     * @param OSS_Form $form The Send form object
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     */
    protected function addPrepare( $form, $object, $isEdit )
    {
        if( !$this->multiIXP() && !$isEdit )
        {
            $this->addMessage(
                'Seriously dude - Multi-IXP mode has not been enabled. <strong>DO NOT ADD MORE THAN ONE IXP WITHOUT ENABLING MULTI-IXP MODE!</strong>',
                OSS_Message::ERROR
            );
            $this->redirect( 'ixp/list' );
        }
    }
        
    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful add / edit operation.
     *
     * By default it returns `false`.
     *
     * On `false`, the default action (`index`) is called and a standard success message is displayed.
     *
     *
     * @param OSS_Form $form The form object
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function addDestinationOnSuccess( $form, $object, $isEdit  )
    {
        if( !$this->multiIXP() )
        {
            $this->addMessage( 'IXP details updated successfully.', OSS_Message::SUCCESS );
            $this->redirect( 'infrastructure/list' );
        }
        
        return false;
    }
    
    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'i.id AS id, i.name AS name, i.aggregate_graph_name AS aggregate_graph_name,
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
        $ixp      = $this->loadObject( $this->getParam( 'id' ) );
        $customer = $this->loadCustomerById( $this->getParam( "cid", false ) );
        
        $ixp->addCustomer( $customer );
        $customer->addIXP( $ixp );
        $this->getD2EM()->flush();

        $this->addMessage(
            "The customer <em>{$customer->getName()}</em> was successfully assigned to the IXP {$ixp->getName()}.",
            OSS_Message::SUCCESS
        );
        
        if( $this->getParam( 'overview', false ) )
            $this->redirectAndEnsureDie( "/customer/overview/tab/ixps/id/" .  $customer->getId() );
        else
            $this->redirect( "/customer/list/ixp/" .  $ixp->getId() );
    }

    public function unassignCustomerAction()
    {
        $ixp      = $this->loadObject( $this->getParam( 'id' ) );
        $customer = $this->loadCustomerById( $this->getParam( "cid", false ) );
        
        // does this customer have any connections in this IXP?
        foreach( $customer->getVirtualInterfaces() as $vi )
        {
            foreach( $vi->getPhysicalInterfaces() as $pi )
            {
                if( $pi->getSwitchport()->getSwitcher()->getInfrastructure()->getIXP() == $ixp )
                {
                    $this->addMessage(
                            "The customer <em>{$customer->getName()}</em> has assigned interfaces in this IXP."
                                . " Please deprovision all interfaces before unsassigning this customer from this IXP.",
                            OSS_Message::ERROR
                    );
                    $this->redirect( "/customer/list/ixp/" .  $ixp->getId() );
                }
            }
        }
        
        $ixp->removeCustomer( $customer );
        $customer->removeIXP( $ixp );
        $this->getD2EM()->flush();

        $this->addMessage(
            "The customer <em>{$customer->getName()}</em> was unassigned from the IXP {$ixp->getName()}.",
            OSS_Message::SUCCESS
        );
        $this->redirect( "/customer/list/ixp/" .  $ixp->getId() );
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
        if( ( $cnt = count( $object->getInfrastructures() ) ) )
        {
            $this->addMessage(
                    "Could not delete this IXP as {$cnt} infrastructures(es) are associated with it.",
                    OSS_Message::ERROR
            );
            return false;
        }
    
        if( ( $cnt = count( $object->getCustomers() ) ) )
        {
            $this->addMessage(
                    "Could not delete this IXP as {$cnt} customer(s) are associated with it.",
                    OSS_Message::ERROR
            );
            return false;
        }
    
        return true;
    }
    
    /**
     * Post database flush hook that can be overridden by subclasses and is called by
     * default for a successful add / edit / delete.
     *
     * Called by `addPostFlush()` and `postDelete()` - if overriding these, ensure to
     * call this if you have overridden it.
     *
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @return bool
     */
    protected function postFlush( $object )
    {
        // wipe cached entries
        if( $object->getId() == 1 )
            $this->getD2Cache()->delete( \Repositories\IXP::CACHE_KEY_DEFAULT_IXP );
        
        $this->getD2Cache()->delete( "ixp_{$object->getId()}" );
        
        return true;
    }
    
}

