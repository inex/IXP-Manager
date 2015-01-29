<?php

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
 * Controller: Manage switch ports
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchPortController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );

        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\SwitchPort',
            'form'          => 'IXP_Form_Switch_Port',
            'pagetitle'     => 'Switch Ports',

            'titleSingular' => 'Switch Port',
            'nameSingular'  => 'a switch port',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC',

            'listColumns'    => [

                'id'        => [ 'title' => 'UID', 'display' => false ],

                'switch'  => [
                    'title'      => 'Switch',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'switch',
                    'action'     => 'view',
                    'idField'    => 'switchid'
                ],

                'name'           => 'Description',
                'ifName'         => 'Name',
                'ifAlias'        => 'Alias',
                'active'       => [
                        'title'    => 'Active',
                        'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'script'   => 'frontend/list-column-active.phtml',
                        'colname'  => 'active'
                ],

                'type'  => [
                    'title'    => 'Type',
                    'type'     => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'   => \Entities\SwitchPort::$TYPES
                ]
            ]
        ];

        // display the same information in the view as the list
        $this->_feParams->viewColumns = $this->_feParams->listColumns;
    }


    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $this->view->switches = $switches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getNames();

        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'sp.id AS id, sp.name AS name, sp.type AS type, s.name AS switch,
                sp.ifName AS ifName, sp.ifAlias AS ifAlias, sp.ifHighSpeed AS ifHighSpeed,
                sp.ifMtu AS ifMtu, sp.ifPhysAddress AS ifPhysAddress, sp.active AS active,
                sp.ifAdminStatus AS ifAdminStatus, sp.ifOperStatus AS ifOperStatus,
                sp.ifLastChange AS ifLastChange, sp.lastSnmpPoll AS lastSnmpPoll,
                sp.lagIfIndex AS lagIfIndex, sp.ifIndex AS ifIndex,
                s.id AS switchid'
            )
            ->from( '\\Entities\\SwitchPort', 'sp' )
            ->leftJoin( 'sp.Switcher', 's' );

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'sp.id = ?1' )->setParameter( 1, $id );

        if( ( $sid = $this->getParam( 'switch', false ) ) && isset( $switches[$sid] ) )
        {
            $this->view->sid = $sid;
            $qb->where( 's.id = ?2' )->setParameter( 2, $sid );
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listMauGetData( $id = null )
    {
        $switches = [];
        $s = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->findBy( [ 'mauSupported' => true ] );
        foreach( $s as $switch )
            $switches[ $switch->getId() ] = $switch;

        $this->view->switches = $switches;

        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'sp.id AS id, sp.name AS name, sp.type AS type, s.name AS switch,
                sp.ifName AS ifName, sp.ifAlias AS ifAlias, sp.lastSnmpPoll AS lastSnmpPoll,
                sp.ifAdminStatus AS ifAdminStatus, sp.ifOperStatus AS ifOperStatus,
                sp.mauType AS mauType, sp.mauState AS mauState,
                sp.mauAvailability AS mauAvailability, sp.mauJacktype AS mauJacktype,
                sp.mauAutoNegSupported AS mauAutoNegSupported, sp.mauAutoNegAdminState AS mauAutoNegAdminState,
                sp.ifIndex AS ifIndex, s.id AS switchid'
            )
            ->from( '\\Entities\\SwitchPort', 'sp' )
            ->orderBy( 'sp.ifIndex', 'ASC' )
            ->leftJoin( 'sp.Switcher', 's' );

        if( $id !== null )
            $qb->andWhere( 'sp.id = ?1' )->setParameter( 1, $id );

        if( ( $sid = $this->getParam( 'switch', false ) ) && isset( $switches[$sid] ) && $switches[$sid]->getMauSupported() ) {
            $this->view->sid = $sid;
            $qb->where( 's.id = ?2' )->setParameter( 2, $sid );
        } else {
            $this->redirect( 'switch/list' );
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * List the MAU details of a switches interfaces
     */
    public function listMauAction()
    {
        $this->view->data = $this->listMauGetData();
        $this->_display( 'list-mau.phtml' );
    }

    public function unusedOpticsAction()
    {
        $this->view->data = $this->getD2EM()->createQueryBuilder()
            ->select( 'sp' )
            ->from( '\\Entities\\SwitchPort', 'sp' )
            ->leftJoin( 'sp.Switcher', 's' )
            ->orderBy( 's.name', 'ASC' )
            ->where( 's.mauSupported = 1 AND sp.ifOperStatus != 1 AND sp.mauType != :mauType AND sp.type != :type' )
            ->setParameter( 'mauType', '(empty)' )
            ->setParameter( 'type',    \Entities\SwitchPort::TYPE_MANAGEMENT )
            ->getQuery()
            ->getResult();

        $this->_display( 'unused-optics.phtml' );
    }


    public function opStatusAction()
    {
        if( $this->getParam( 'switch' ) && ( $switch = $this->getD2R( '\\Entities\\Switcher' )->find( $this->getParam( 'switch' ) ) ) )
        {
            try // to refresh switch and switch port details via SNMP
            {
                if( $switch->getSwitchtype() != \Entities\Switcher::TYPE_SWITCH || !$switch->getActive() )
                    throw new \OSS_SNMP\Exception();

                $host = new \OSS_SNMP\SNMP( $switch->getHostname(), $switch->getSnmppasswd() );
                $switch->snmpPoll( $host, $this->getLogger() );
                if( $switch->getSwitchtype() == \Entities\Switcher::TYPE_SWITCH )
                    $switch->snmpPollSwitchPorts( $host, $this->getLogger() );
                $this->getD2EM()->flush();
                $this->addMessage( "The below is <strong>live information</strong> gathered via SNMP", OSS_Message::INFO );
            }
            catch( \OSS_SNMP\Exception $e )
            {
                $this->addMessage( "<strong>Stale data gathered on " . $switch->getLastPolled()->format( 'Y-m-d H:i:s' ) . "</strong>. "
                        . "Could not update switch and switch port details via SNMP poll.", OSS_Message::ALERT );
            }
        }

        $this->_feParams->listColumns = [
            'id'            => [ 'title' => 'UID', 'display' => false ],
            'ifIndex'       => 'Index',
            'name'          => 'Description',
            'ifName'        => 'Name',
            'ifAlias'       => 'Alias',
            'lagIfIndex'    => 'LAG',
            'ifHighSpeed'   => 'Speed',
            'ifMtu'         => 'MTU',
            // 'ifPhysAddress' => 'Physical Address',

            'ifAdminStatus' => [
                'title'    => 'Admin State',
                'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                'script'   => 'switch-port/list-column-port-status.phtml',
                'colname'  => 'ifAdminStatus'
            ],

            'ifOperStatus' => [
                'title'    => 'Operational State',
                'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                'script'   => 'switch-port/list-column-port-status.phtml',
                'colname'  => 'ifOperStatus'
            ],
            'active'       => [
                    'title'    => 'Active',
                    'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                    'script'   => 'frontend/list-column-active.phtml',
                    'colname'  => 'active'
            ],

        ];

        $this->_feParams->listOrderBy = 'ifIndex';


        $this->view->portStates = \OSS_SNMP\MIBS\Iface::$IF_OPER_STATES;

        return $this->listAction();
    }


    /**
     *
     * @param IXP_Form_SwitchPort $form The form object
     * @param \Entities\SwitchPort $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
            $form->getElement( 'switchid' )->setValue( $object->getSwitcher()->getId() );
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
    protected function addPreValidate( $form, $object, $isEdit )
    {
        // ensure the port name is unique for a given switch
        // FIXME - for add and edit

        return true;
    }

    /**
     *
     * @param IXP_Form_SwitchPort $form The form object
     * @param \Entities\SwitchPort $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setSwitcher(
            $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->find( $form->getElement( 'switchid' )->getValue() )
        );

        return true;
    }


    public function addAction()
    {
        $this->addMessage(
            "<h4>Use of this method is discouraged!</h4>

                Switch ports are best added using the
                <a href=\"https://github.com/inex/IXP-Manager/wiki/Updating-Switches-and-Ports-via-SNMP\">CLI scripts</a>
                or the <em>View / Edit Ports (with SNMP poll)</em> option from the <a href=\""
                . OSS_Utils::genUrl( 'switch', 'list' ) . "\">switch list page</a>. See <a href=\""
                . "https://github.com/inex/IXP-Manager/wiki/Switch-and-Switch-Port-Management\">the documentation
                for more information</a>.",
            OSS_Message::INFO, OSS_Message::TYPE_BLOCK
        );

        $this->view->form = $form = new IXP_Form_Switch_AddPorts();

        if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
        {
            if( !( $switch = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->find( $form->getValue( 'switchid' ) ) ) )
                throw new IXP_Exception( 'Unknown switch in request' );

            for( $i = 0; $i < $form->getValue( 'numports' ); $i++ )
            {
                $sp = new \Entities\SwitchPort();
                $sp->setSwitcher( $switch );
                $sp->setType( intval( $_POST[ 'np_type' . $i ] ) );
                $sp->setName( trim( stripslashes( $_POST[ 'np_name' . $i ] ) ) );
                $sp->setActive( true );
                $this->getD2EM()->persist( $sp );
            }

            $this->getD2EM()->flush();


            $msg = "{$form->getValue( 'numports' )} new ports created for switch {$switch->getName()}.";
            $this->getLogger()->info( $msg );
            $this->addMessage( $msg, OSS_Message::SUCCESS );

            $this->redirect( 'switch-port/list/switch/' . $switch->getId() );
        }

        $this->render( 'add-ports' );
    }


    // we have overridden the standard addAction() and so we need a dedicated editAction():
    public function editAction()
    {
        $this->view->isEdit = $isEdit = true;

        $eid = $this->editResolveId();

        if( !$eid || !is_numeric( $eid ) )
            throw new IXP_Exception( 'Bad switch port id for switch-port/edit' );

        $this->view->object = $object = $this->loadObject( $eid );
        $this->view->form = $form = $this->getForm( $isEdit, $object );
        $form->assignEntityToForm( $object, $this );
        if( $form->getElement( 'submit' ) )
            $form->getElement( 'submit' )->setLabel( 'Save Changes' );

        $this->addPrepare( $form, $object, $isEdit );

        if( $this->getRequest()->isPost() && $this->addPreValidate( $form, $object, $isEdit ) && $form->isValid( $_POST ) )
        {
            if( $this->addProcessForm( $form, $object, $isEdit ) )
            {
                if( $this->addDestinationOnSuccess( $form, $object, $isEdit ) === false )
                {
                    $this->addMessage( $this->feGetParam( 'titleSingular' ) . ( $isEdit ? ' edited.' : ' added.' ), OSS_Message::SUCCESS );
                    $this->redirectAndEnsureDie( $this->_getBaseUrl() . "/index" );
                }
            }
        }

        $this->view->addPreamble  = $this->_resolveTemplate( 'add-preamble.phtml'  );
        $this->view->addPostamble = $this->_resolveTemplate( 'add-postamble.phtml' );
        $this->view->addToolbar   = $this->_resolveTemplate( 'add-toolbar.phtml' );
        $this->view->addScript    = $this->_resolveTemplate( 'js/add.js' );

        $this->_display( 'add.phtml' );
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
        $this->addMessage( $this->feGetParam( 'titleSingular' ) . ( $isEdit ? ' edited.' : ' added.' ), OSS_Message::SUCCESS );
        $this->redirectAndEnsureDie( 'switch-port/list/switch/' . $object->getSwitcher()->getId() );
    }

    public function ajaxGetAction()
    {
        $dql = "SELECT sp.name AS name, sp.type AS type, sp.id AS id
                    FROM \\Entities\\SwitchPort sp
                        LEFT JOIN sp.Switcher s
                        LEFT JOIN sp.PhysicalInterface pi
                    WHERE
                        s.id = ?1 ";

        if( $this->getParam( 'id', null ) !== null )
            $dql .= 'AND ( pi.id IS NULL OR pi.id = ?2 )';
        else
            $dql .= 'AND pi.id IS NULL';

        // limit to ports suitable for peering?
        if( $this->getParam( 'type', null ) == 'peering' )
            $dql .= 'AND ( sp.type IN ( ' . \Entities\SwitchPort::TYPE_PEERING . ', ' . \Entities\SwitchPort::TYPE_UNSET . ') )';
        if( $this->getParam( 'type', null ) == 'fanout' )
            $dql .= 'AND ( sp.type IN ( ' . \Entities\SwitchPort::TYPE_FANOUT . ', ' . \Entities\SwitchPort::TYPE_UNSET . ' ) )';

        $dql .= " ORDER BY sp.id ASC";

        $query = $this->getD2EM()->createQuery( $dql );
        $query->setParameter( 1, $this->getParam( 'switchid', 0 ) );

        if( $this->getParam( 'id', null ) !== null )
            $query->setParameter( 2, $this->getParam( 'id' ) );

        $ports = $query->getArrayResult();

        foreach( $ports as $id => $port )
            $ports[$id]['type'] = \Entities\SwitchPort::$TYPES[ $port['type'] ];

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody( Zend_Json::encode( $ports ) )
            ->sendResponse();

        die(); //FIXME I shouldn't have to die() here...
    }



    /**
     * This action will find all ports on a switch, match them (where possible) to existing
     * ports of that switch in the database and allow the user to:
     *
     *  - view name (ifDescr), ifName and ifAlias
     *  - set the switchport type in bulk
     *  - remove port(s)
     *  - manage these actions in bulk (e.g. phpMyAdmin type row management)
     *
     *  Should this be in the SwitchController? Possibly...
     *
     */
    public function snmpPollAction()
    {
        $this->view->switches = $switches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getNames();

        if( $this->getParam( 'switch' ) == null || !( $switch = $this->getD2R( '\\Entities\\Switcher' )->find( $this->getParam( 'switch' ) ) ) )
        {
            $this->addMessage( 'Unknown switch', OSS_Message::ERROR );
            $this->redirect( 'switch/list' );
        }
        else
            $this->view->sid = $switch->getId();

        if( $switch->getSwitchtype() != \Entities\Switcher::TYPE_SWITCH || !$switch->getActive() )
        {
            $this->addMessage( 'SNMP Polling of ports is only valid for switches (e.g. not console servers) that are active', OSS_Message::ERROR );
            $this->redirect( 'switch/list' );
        }

        if( isset( $_POST ) && isset( $_POST['poll-action'] ) )
        {
            foreach( $_POST['switch-port'] as $id )
            {
                if( ( $port = $this->getD2R( "\\Entities\\SwitchPort" )->find( $id ) ) )
                {
                    switch( $_POST['poll-action'] )
                    {
                        case 'delete':
                            if( $port->getPhysicalInterface() )
                            {
                                $cust = $port->getPhysicalInterface()->getVirtualInterface()->getCustomer();
                                $this->addMessage(
                                    "Could not delete switch port {$port->getName()} as it is assigned to a physical interface for <a href=\""
                                        . OSS_Utils::genUrl( 'customer', 'overview', false, [ 'tab' => 'ports', 'id' => $cust->getId() ] )
                                        . "\">{$cust->getName()}</a>."
                                );
                            }
                            else
                                $this->getD2EM()->remove( $port );
                            break;

                        case 'type':
                            $port->setType( $_POST['shared-type'] );
                            break;

                        case 'active':
                            $port->setActive( true );
                            break;

                        case 'inactive':
                            $port->setActive( false );
                            break;
                    }
                }
            }

            // helpful message for people trying to delete ports:
            if( $_POST['poll-action'] == "delete" )
            {
                $this->addMessage(
                    "<strong>Please Note:</strong> It is not possible to delete real physical Ethernet switch ports as "
                        . "the switch is re-polled and these ports are added back into the system as new ports automatically. "
                        . "The purpose of delete is to remove ports that were manually added to the database that do not match "
                        . "up with phsyical ports on the switch. You can deactivate switchports however.",
                    OSS_Message::INFO
                );
            }

            $this->getD2EM()->flush();
            $this->addMessage( "Switch ports updated", OSS_Message::SUCCESS );
            $this->redirect( "switch-port/snmp-poll/switch/{$switch->getId()}" );
        }

        $results = [];

        try
        {
            $host = new \OSS_SNMP\SNMP( $switch->getHostname(), $switch->getSnmppasswd() );
            $switch->snmpPoll( $host, $this->getLogger() );
            $switch->snmpPollSwitchPorts( $host, $this->getLogger(), $results );
            $this->view->switch    = $switch;
            $this->view->portTypes = \Entities\SwitchPort::$TYPES;
            $this->view->portsData = $results;
            $this->getD2EM()->flush();
        }
        catch( \OSS_SNMP\Exception $e )
        {
            $this->addMessage( 'Error polling switch via SNMP.', OSS_Message::ERROR );
            $this->redirect( 'switch/list' );
        }
    }

    /**
     * Sets port type for port loaded by id form url.
     *
     * If type is not sent by post and it is not valid function returns ko else
     * it sets type to port flush changes and returns ko
     */
    public function ajaxSetTypeAction()
    {
        $port = $this->loadObject( $this->getParam( 'id', false ), false );

        if( $port && isset( $_POST['type'] ) && array_key_exists( $_POST['type'], \Entities\SwitchPort::$TYPES ) )
        {
            $port->setType( $_POST['type'] );
            $this->getD2EM()->flush();
            echo "ok";
        }
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
        if( $object->getPhysicalInterface() )
        {
            $c = $object->getPhysicalInterface()->getVirtualInterface()->getCustomer();

            $this->addMessage(
                "Could not delete switch port {$object->getName()} as it is assigned to a physical interface for <a href=\""
                    . OSS_Utils::genUrl( 'customer', 'overview', false, [ 'tab' => 'ports', 'id' => $c->getId() ] )
                    . "\">{$c->getName()}</a>.",
                OSS_Message::ERROR
            );
            return false;
        }

        return true;
    }

}
