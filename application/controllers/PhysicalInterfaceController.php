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
 * Controller: Physical Interfaces
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PhysicalInterfaceController extends IXP_Controller_FrontEnd
{
    use IXP_Controller_Trait_Interfaces;

    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\PhysicalInterface',
            'form'          => 'IXP_Form_Interface_Physical',
            'pagetitle'     => 'Physical Interfaces',

            'titleSingular' => 'Physical Interface',
            'nameSingular'  => 'a physical interface',

            'defaultAction' => 'list',

            'listOrderBy'    => 'customer',
            'listOrderByDir' => 'ASC',
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],

                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],

                    'location'  => [
                        'title'      => 'Location',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'location',
                        'action'     => 'view',
                        'idField'    => 'locid'
                    ],

                    'switch'  => [
                        'title'      => 'Switch',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'switch',
                        'action'     => 'view',
                        'idField'    => 'switchid'
                    ],

                    'port'          => 'Port',

                    'status'        => [
                        'title'          => 'Status',
                        'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'         => \Entities\PhysicalInterface::$STATES
                    ],


                    //'location'      => 'Location',
                    //'switch'        => 'Switch',
                    'speed'         => 'Speed',
                    'duplex'        => 'Duplex'
                ];

                $this->_feParams->viewColumns = array_merge(
                    $this->_feParams->listColumns,
                    [ 'monitorindex' => 'Monitor Index', 'notes' => 'Notes' ]
                );
                break;

            case \Entities\User::AUTH_CUSTADMIN:
            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }

    }



    /**
     * Provide array of virtual interfaces for the listAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select(
                    'pi.id AS id, pi.speed AS speed, pi.duplex AS duplex, pi.status AS status,
                    pi.monitorindex AS monitorindex, pi.notes AS notes,
                    c.name AS customer, c.id AS custid,
                    s.name AS switch, s.id AS switchid,
                    vi.id AS vintid,
                    sp.type as type, ppi.id as ppid, fpi.id as fpid,
                    sp.name AS port, l.id AS locid, l.name AS location'
                )
            ->from( '\\Entities\\PhysicalInterface', 'pi' )
            ->leftJoin( 'pi.PeeringPhysicalInterface', 'ppi' )
            ->leftJoin( 'pi.FanoutPhysicalInterface', 'fpi' )
            ->leftJoin( 'pi.VirtualInterface', 'vi' )
            ->leftJoin( 'vi.Customer', 'c' )
            ->leftJoin( 'pi.SwitchPort', 'sp' )
            ->leftJoin( 'sp.Switcher', 's' )
            ->leftJoin( 's.Cabinet', 'cab' )
            ->leftJoin( 'cab.Location', 'l' );


        if( $id !== null )
            $qb->where( 'pi.id = ' . intval( $id ) );

        return $qb->getQuery()->getArrayResult();
    }


    /**
     * @param IXP_Form_Interface_Physical $form The form object
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            if( $object->getRelatedInterface() && $object->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_FANOUT )
                $object = $object->getRelatedInterface();

            $form->enableFanoutPort( $this->resellerMode() && $object->getVirtualInterface()->getCustomer()->isResoldCustomer() );

            $form->getElement( 'switchid' )->setValue( $object->getSwitchPort()->getSwitcher()->getId() );
            $form->getElement( 'switchportid' )->setValue( $object->getSwitchPort()->getId() );
            $form->getElement( 'preselectSwitchPort' )->setValue( $object->getSwitchPort()->getId() );
            $form->getElement( 'preselectPhysicalInterface' )->setValue( $object->getId() );
            $form->getElement( 'virtualinterfaceid' )->setValue( $object->getVirtualInterface()->getId() );

            if( $form->getElement( 'fanout' ) )
            {
                if( $object->getFanoutPhysicalInterface() )
                {
                    $form->getElement( 'fanout' )->setValue( true );
                    $form->getElement( 'fn_switchid' )->setValue( $object->getFanoutPhysicalInterface()->getSwitchPort()->getSwitcher()->getId() );
                    $form->getElement( 'fn_switchportid' )->setValue( $object->getFanoutPhysicalInterface()->getSwitchPort()->getId() );
                    $form->getElement( 'fn_monitorindex' )->setValue( $object->getFanoutPhysicalInterface()->getMonitorindex() );

                    $form->getElement( 'fn_preselectSwitchPort' )->setValue( $object->getFanoutPhysicalInterface()->getSwitchPort()->getId() );
                    $form->getElement( 'fn_preselectPhysicalInterface' )->setValue( $object->getFanoutPhysicalInterface()->getId() );
                }
                else
                {
                    $form->getElement( 'fn_monitorindex' )->setValue(
                        $this->getD2EM()->getRepository( '\\Entities\\PhysicalInterface' )->getNextMonitorIndex( $object->getVirtualInterface()->getCustomer()->getReseller() )
                    );
                }
            }

            if( $this->getParam( 'rtn', false ) == 'pi' )
                $form->setAction( OSS_Utils::genUrl( 'physical-interface', 'edit', false, [ 'id' => $object->getId(), 'rtn' => 'pi' ] ) );
            else
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
                $this->addMessage( 'You need a containing virtual interface before you add a physical interface', OSS_Message::ERROR );
                $this->redirect( 'virtual-interface/add' );
            }

            $form->enableFanoutPort( $this->resellerMode() && $vint->getCustomer()->isResoldCustomer() );

            $form->getElement( 'virtualinterfaceid' )->setValue( $vint->getId() );
            $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'edit', false, [ 'id' => $vint->getId() ] ) );

            if( !$object->getMonitorindex() )
            {
                $form->getElement( 'monitorindex' )->setValue(
                    $this->getD2EM()->getRepository( '\\Entities\\PhysicalInterface' )->getNextMonitorIndex( $vint->getCustomer() )
                );
            }

            if( $form->getElement( 'fanout' ) )
            {
                $form->getElement( 'fn_monitorindex' )->setValue(
                    $this->getD2EM()->getRepository( '\\Entities\\PhysicalInterface' )->getNextMonitorIndex( $vint->getCustomer()->getReseller() )
                );
            }
        }

    }


    /**
     * @param IXP_Form_Interface_Physical $form The form object
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $sp = $this->getD2R( '\\Entities\\SwitchPort' )->find( $form->getElement( 'switchportid' )->getValue() );

        // set the switch port type to peering
        $sp->setType( \Entities\SwitchPort::TYPE_PEERING );

        $object->setSwitchPort( $sp );

        $vi =  $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $form->getElement( 'virtualinterfaceid' )->getValue() );
        $object->setVirtualInterface( $vi );

        // FIXME We should do more than just ensure it's not an edit here. You could edit to a non-unique value...
        if( !$isEdit && !$vi->getCustomer()->isUniqueMonitorIndex( $form->getValue( 'monitorindex' ) ) ) {
            $this->addMessage( 'The monitor index must be unique. It has been reset below to a unique value.', OSS_Message::ERROR );
            $form->getElement( 'monitorindex' )->setValue( $this->getD2R( '\\Entities\\PhysicalInterface' )->getNextMonitorIndex( $vi->getCustomer() ) );
            return false;
        }

        if( $form->getElement( 'fanout' ) )
        {
            if( !$this->processFanoutPhysicalInterface( $form, $object, $vi ) )
                return false;
        }

        if( $object->getRelatedInterface() )
        {
            $object->getRelatedInterface()->setSpeed( $form->getValue( "speed" ) );
            $object->getRelatedInterface()->setStatus( $form->getValue( "status" ) );
            $object->getRelatedInterface()->setDuplex( $form->getValue( "duplex" ) );
        }
        return true;
    }

    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful add / edit operation.
     *
     * @param IXP_Form_Interface_Physical $form The form object
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function addDestinationOnSuccess( $form, $object, $isEdit  )
    {
        if( $this->getParam( 'rtn', false ) == 'pi' )
            return false;

        $this->addMessage(
            'Physical interface successfully ' . ( $isEdit ? 'edited.' : 'added.' ), OSS_Message::SUCCESS
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
        if( $this->getParam( 'rtn', false ) == 'pi' )
            return false;

        $this->addMessage(
            'Physical interface deleted successfully.', OSS_Message::SUCCESS
        );

        $this->redirectAndEnsureDie( 'virtual-interface/edit/id/' . $this->getParam( 'vintid' ) );
    }

    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why.
     *
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( $object )
    {
        if( $object->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_PEERING && $object->getFanoutPhysicalInterface() )
        {
            $object->getSwitchPort()->setPhysicalInterface( null );
            $object->getFanoutPhysicalInterface()->getSwitchPort()->setType( \Entities\SwitchPort::TYPE_PEERING );
        }
        else if( $object->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_FANOUT && $object->getPeeringPhysicalInterface() )
        {
            if( $this->getParam( 'related', false ) )
                $this->removeRelatedInterface( $object );

            $object->getPeeringPhysicalInterface()->setFanoutPhysicalInterface( null );
        }

        return true;
    }

    /**
     * Function which can be over-ridden to perform any post-deletion tasks
     *
     * Database `flush()` has been successfully completed at this stage
     *
     * If you return with true, then the standard log message and OSS_Message
     * will be performed. If you want to override these, return false.
     *
     * NB: also calls `postFlush()`
     *
     * @param \Entities\PhysicalInterface $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel standard log and OSS_Message
     */
    protected function postDelete( $object )
    {
        if( $this->getParam( 'related', false ) && $object->getRelatedInterface() )
        {
            $this->removeRelatedInterface( $object );
            $this->getD2EM()->flush();
        }

        return $this->postFlush( $object );
    }

}
