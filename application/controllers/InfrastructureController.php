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
 * Controller: Manage Interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class InfrastructureController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Infrastructure',
            'form'          => 'IXP_Form_Infrastructure',
            'pagetitle'     => 'Infrastructures',

            'titleSingular' => 'Infrastructure',
            'nameSingular'  => 'an infrastructure',

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC'
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ]
                ];
                
                if( $this->multiIXP() )
                    $this->_feParams->listColumns = array_merge( $this->_feParams->listColumns, [ 'ixp_name' => 'IXP' ] );
                
                $this->_feParams->listColumns = array_merge( $this->_feParams->listColumns, [
                        'name'      => 'Name',
                        'shortname' => 'Shortname',
                        'isPrimary'   => [ 'title' => 'Primary', 'type' => self::$FE_COL_TYPES[ 'YES_NO' ] ],
                        'aggregate_graph_name' => 'Aggregate Graph Name'
                    ]
                );
                
                // display the same information in the view as the list
                $this->_feParams->viewColumns = $this->_feParams->listColumns;

                $this->_feParams->defaultAction = 'list';
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
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
            ->select( 'i.id AS id, i.name AS name, i.isPrimary AS isPrimary,
                i.shortname AS shortname, ix.shortname AS ixp_name,
                ix.id AS ixp_id, i.aggregate_graph_name AS aggregate_graph_name'
            )
            ->from( '\\Entities\\Infrastructure', 'i' )
            ->leftJoin( 'i.IXP', 'ix' );

        if( $this->getParam( 'ixp', false ) && $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $this->getParam( 'ixp' ) ) )
        {
            $qb->andWhere( 'ix = :ixp' )->setParameter( 'ixp', $ixp );
            $this->view->ixp = $ixp;
        }
        
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'i.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }

    
    /**
     * Prevalidation hook that can be overridden by subclasses for add and edit.
     *
     * This is called if the user POSTs a form just before the form is validated by Zend
     *
     * @param OSS_Form $form The Send form object
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     * @return bool If false, the form is not validated or processed
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        // at least one infrastructure must be primary
        if( !$form->getElement( 'isPrimary' )->getValue() )
        {
            // is any other infrastructure primary?
            
            $primaryInfra = $this->getD2R( '\\Entities\\Infrastructure' )->getPrimary(
                    $this->loadIxpById( $this->getParam( 'ixp', false ) ), false
            );
            
            if( !$primaryInfra || $primaryInfra->getId() == $object->getId() )
            {
                $this->addMessage(
                    '<h4>At least one infrastructure must be the primary infrastructure '
                            . ( $this->multiIXP() ? 'in every IXP' : '' ) . '</h4>'
                        . '<p>To change the primary infrastructure, do not unset the current one but rather '
                        . 'set another to be the primary.</p>',
                    OSS_Message::ERROR, OSS_Message::TYPE_BLOCK
                );
                $form->getElement( 'isPrimary' )->setValue( true );
                return false;
            }
        }
        
        
        return true;
    }
    
    
    /**
     * Post process hook for add and edit actions.
     *
     * This is called immediately after the initstantiation of the form object and, if
     * editing, includes the Doctrine2 entity `$object`.
     *
     * If you need to have, for example, edit values set in the form, then use the
     * `addPrepare()` hook rather than this one.
     *
     * @see addPrepare()
     * @param OSS_Form $form The form object
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        $form->setMultiIXP( $this->multiIXP() );

        if( $isEdit )
            $form->getElement( 'ixp' )->setValue( $object->getIXP()->getId() );
    }

    /**
     * Pre db flush hook that can be overridden by subclasses for add and edit.
     *
     * This is called if the user POSTs a valid form after the posted
     * data has been assigned to the object and just before it is (persisted
     * if adding) and the database is flushed.
     *
     * This hook can prevent flushing by returning false.
     *
     * **NB: You should not `flush()` here unless you know what you are doing**
     *
     * A call to `flush()` is made after this method returns true ensuring a
     * transactional `flush()` for all.
     *
     * @param OSS_Form $form The Send form object
     * @param \Entities\Infrastructure $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     * @return bool If false, the form is not persisted
     */
    protected function addPreFlush( $form, $object, $isEdit )
    {
        $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $form->getValue( 'ixp' ) );
        
        if( !$ixp )
        {
            $this->addMessage(
                    'There is an issue with your infrastructures as we could not load an infrastructure with ID ' . $form->getValue( 'ixp' ),
                    OSS_Message::ERROR
            );
            return false;
        }
        
        $object->setIXP( $ixp );
        
        // if we're setting this infra as the primary, ensure the rest in this IXP are not primary
        if( $object->getIsPrimary() )
        {
            foreach( $ixp->getInfrastructures() as $i )
                if( $i->getId() != $object->getId() )
                    $i->setIsPrimary( false );
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
        $this->getD2Cache()->delete( \Repositories\Infrastructure::CACHE_KEY_PRIMARY . $object->getId() );
        $this->getD2Cache()->delete( \Repositories\Infrastructure::CACHE_KEY_ALL     . $object->getId() );
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
        if( ( $cnt = count( $object->getSwitchers() ) ) )
        {
            $this->addMessage(
                    "Could not delete this infrastructure as {$cnt} switch(es) are assigned to it",
                    OSS_Message::ERROR
            );
            return false;
        }
    
        return true;
    }
    
}

