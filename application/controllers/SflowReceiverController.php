<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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
 * Controller: Sflow Receivers
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SflowReceiverController extends IXP_Controller_FrontEnd
{

    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );

        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\SflowReceiver',
            'form'          => 'IXP_Form_SflowReceiver',
            'pagetitle'     => 'Sflow Receivers',

            'titleSingular' => 'Sflow Receiver',
            'nameSingular'  => 'a sflow receiver',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'customer',
            'listOrderByDir' => 'ASC',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],

                'customer'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'custid'
                ],

                'dst_ip'    => 'Destination IP',
                'dst_port'  => 'Destination Port'
            ]
        ];

        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'customer'     => 'Customer',
                'dst_ip'       => 'Destination IP',
                'dst_port'     => 'Destination Port'
            ]
        );
    }


    /**
     * Provide array of sflow receivers for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select(
                    'sr.id as id, sr.dst_ip as dst_ip, sr.dst_port as dst_port,
                        c.name AS customer, c.id AS custid'
                )
            ->from( '\\Entities\\SflowReceiver', 'sr' )
            ->leftJoin( 'sr.VirtualInterface', 'vi' )
            ->leftJoin( 'vi.Customer', 'c' );

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'sr.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }



    /**
     * @param IXP_Form_Interface_Vlan $form The form object
     * @param \Entities\VlanInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            $form->getElement( 'virtualinterfaceid' )->setValue( $object->getVirtualInterface()->getId() );
            $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'edit', false, [ 'id' => $object->getVirtualInterface()->getId() ] ) );
        }
        else // not editing
        {
            if( $this->getRequest()->isPost() && ( $vintid = ( isset( $_POST['virtualinterfaceid'] ) && $_POST['virtualinterfaceid'] ) ) )
                $vint = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $_POST['virtualinterfaceid'] );
            else if( ( $vintid = $this->getRequest()->getParam( 'vintid' ) ) !== null )
                $vint = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $vintid );

            if( !isset( $vint ) || !$vint )
            {
                $this->addMessage( 'You need a containing virtual interface before you add a sflow receiver interface', OSS_Message::ERROR );
                $this->redirect( 'virtual-interface/add' );
            }
            $form->getElement( 'virtualinterfaceid' )->setValue( $vint->getId() );

            $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'edit', false, [ 'id' => $vint->getId() ] ) );
        }
    }


    /**
     * @param IXP_Form_SflowReceiver $form The form object
     * @param \Entities\SflowReceiver $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setVirtualInterface(
            $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $form->getElement( 'virtualinterfaceid' )->getValue() )
        );

        return true;
    }

    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful add / edit operation.
     *
     * @param IXP_Form_Interface_Vlan $form The form object
     * @param \Entities\VlanInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function addDestinationOnSuccess( $form, $object, $isEdit  )
    {
        if( $this->getParam( 'rtn', false ) == 'vli' )
            return false;

        $this->addMessage(
            'Sflow receiver successfuly ' . ( $isEdit ? 'edited.' : 'added.' ), OSS_Message::SUCCESS
        );

        $this->redirectAndEnsureDie( 'virtual-interface/edit/id/' . $object->getVirtualInterface()->getId() );
    }

    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful deletion operation.
     *
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function deleteDestinationOnSuccess()
    {
        if( $this->getParam( 'rtn', false ) == 'vli' )
            return false;

        $this->addMessage(
            'Sflow receiver deleted successfuly.', OSS_Message::SUCCESS
        );

        $this->redirectAndEnsureDie( 'virtual-interface/edit/id/' . $this->getParam( 'vintid' ) );
    }


}
