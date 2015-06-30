<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * Controller: Customers
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Customer',
            'form'          => 'IXP_Form_Customer',
            'pagetitle'     => 'Customers',

            'titleSingular' => 'Customer',
            'nameSingular'  => 'a customer',

            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'

            'listOrderBy'    => 'c.name',
            'listOrderByDir' => 'ASC',
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],

                    'name'        => [
                        'title'      => 'Name',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'id'
                    ],

                    'autsys'      => [
                        'title'      => 'AS',
                        'type'       => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'script'     => 'customer/list-autsys.phtml'
                    ],

                    'shortname'     => [
                        'title'      => 'Shortname',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'id'
                    ],

                    'peeringpolicy'   => 'Peering Policy',

                    'isReseller'    => [
                        'title'         => 'Reseller',
                        'type'          => self::$FE_COL_TYPES[ 'YES_NO' ],
                    ],

                    'type'          => [
                        'title'         => 'Type',
                        'type'          => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'xlator'        => \Entities\Customer::$CUST_TYPES_TEXT,
                        'script'        => 'customer/list-type.phtml'
                    ],

                    'status'        => [
                        'title'         => 'Status',
                        'type'          => self::$FE_COL_TYPES[ 'SCRIPT' ],
                        'mapper'        => \Entities\Customer::$CUST_STATUS_TEXT,
                        'script'        => 'customer/list-status.phtml'
                    ],

                    'datejoin'       => [
                        'title'     => 'Joined',
                        'type'      => self::$FE_COL_TYPES[ 'DATE' ]
                    ]
                ];

                if( !$this->resellerMode() )
                    unset( $this->_feParams->listColumns[ 'isReseller' ] );

                break;

            case \Entities\User::AUTH_CUSTUSER:
                $this->_feParams->listColumns = [];
                $this->_feParams->allowedActions = [ 'details', 'detail' ];
                $this->_feParams->defaultAction = 'details';
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }

        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'maxprefixes'     => 'Max Prefixes',
                'nocphone'        => 'NOC Phone',
                'nocfax'          => 'NOC Fax',
                'nochours'        => 'NOC Hours',
                'nocemail'        => 'NOC Email',
                'nocwww'          => 'NOC WWW',
                'noc24hphone'     => 'NOC 24h Phone',
                'status'          => [
                    'title'         => 'Status',
                    'type'          => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'        => \Entities\Customer::$CUST_STATUS_TEXT
                ],
                'activepeeringmatrix' => 'Active Peering Matrix',
                'peeringemail'   => 'Peering Email',
                'peeringmacro'    => 'IPv4 Peering Macro',
                'peeringmacrov6'  => 'IPv6 Peering Macro',
                'billingContact'  => 'Billing Contact',
                'billingAddress1' => 'Billing Address1',
                'billingAddress2' => 'Billing Address2',
                'billingCity'     => 'Billing City',
                'billingCountry'  => 'Billing Country',
                'corpwww'         => 'Corporate WWW',
                'dateleave'       => [
                        'title'     => 'Left',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'lastupdated'     => 'Last Updated',
                'lastupdatedby'   => 'Last Updated By',
                'creator'         => 'Created By',
                'created'         => 'Created'
            ]
        );
    }

    /**
     * Provide array of customers for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
                ->select( 'c.id AS id, c.name AS name, c.shortname AS shortname, c.type AS type,
                            c.autsys AS autsys, c.maxprefixes AS maxprefixes, c.peeringemail AS peeringemail,
                            c.nocphone AS nocphone, c.noc24hphone AS noc24hphone, c.nocfax AS nocfax,
                            c.nochours AS nochours, c.nocemail AS nocemail, c.nocwww AS nocwww,
                            c.status AS status, c.activepeeringmatrix AS activepeeringmatrix,
                            c.peeringmacro AS peeringmacro, c.peeringmacrov6 AS peeringmacrov6,
                            c.isReseller AS isReseller, c.peeringpolicy AS peeringpolicy,
                            bd.billingContactName AS billingContact, bd.billingAddress1 AS billingAddress1,
                            bd.billingAddress2 AS billingAddress2, bd.billingTownCity AS billingCity, bd.billingCountry AS billingCountry,
                            c.corpwww AS corpwww, c.datejoin AS datejoin, c.dateleave AS dateleave,
                            c.lastupdated AS lastupdated, c.lastupdatedby AS lastupdatedby,
                            c.creator AS creator, c.created AS created'
                        )
                ->from( '\\Entities\\Customer', 'c' )
                ->join( 'c.BillingDetails', 'bd' );


        $this->view->customerTypes = $customerTypes = \Entities\Customer::$CUST_TYPES_TEXT;
        $this->view->ctype = $ctype = $this->getSessionNamespace()->cust_list_ctype
            = $this->getParam( 'ctype', ( $this->getSessionNamespace()->cust_list_ctype !== null ? $this->getSessionNamespace()->cust_list_ctype : false ) );
        if( $ctype && isset( $customerTypes[$ctype] ) )
            $qb->andWhere( 'c.type = :ctype' )->setParameter( 'ctype', $ctype );

        $this->view->customerStates = $customerStates = \Entities\Customer::$CUST_STATUS_TEXT;
        $this->view->cstate = $cstate = $this->getSessionNamespace()->cust_list_cstate
            = $this->getParam( 'cstate', ( $this->getSessionNamespace()->cust_list_cstate !== null ? $this->getSessionNamespace()->cust_list_cstate : false ) );
        if( $cstate && isset( $customerStates[$cstate] ) )
            $qb->andWhere( 'c.status = :cstate' )->setParameter( 'cstate', $cstate );


        $this->view->currentCustomersOnly = $currentCustomersOnly = $this->getSessionNamespace()->cust_list_current
            = $this->getParam( 'currentonly', ( $this->getSessionNamespace()->cust_list_current !== null ? $this->getSessionNamespace()->cust_list_current : true ) );
        if( $currentCustomersOnly )
            $qb->andWhere( \Repositories\Customer::DQL_CUST_CURRENT );


        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $this->multiIXP() && $this->getParam( 'ixp', false ) )
        {
            $this->view->ixp = $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $this->getParam( 'ixp' ) );
            if( !$ixp )
            {
                $this->addMessage( "Could not load the requested IXP object", OSS_Message::ERROR );
                $this->redirectAndEnsureDie( "/ixp" );
            }

            $qb->leftJoin( 'c.IXPs', 'ixp' )
                ->andWhere( 'ixp.id = :ixpid' )
                ->setParameter( 'ixpid', $ixp->getId() );

            $this->view->validCustomers = $this->getD2R( '\\Entities\\Customer' )->getNamesNotAssignedToIXP( $ixp );
            $this->_feParams->addWhenEmpty = false;
        }

        if( $this->multiIXP() )
            $this->view->ixpNames = $this->getD2R( '\\Entities\\IXP' )->getNames( $this->getUser() );

        if( $id !== null )
            $qb->andWhere( 'c.id = ?3' )->setParameter( 3, $id );

        return $qb->getQuery()->getResult();
    }


    public function viewAction()
    {
        $this->forward( 'overview' );
    }


    /**
     * The Customer Overview
     */
    public function overviewAction()
    {
        $this->view->netinfo   = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();
        $this->view->cust      = $cust = $this->_loadCustomer();
        $this->view->tab       = $this->getParam( 'tab', false );

        $this->view->registerClass( 'Countries', 'OSS_Countries' );
        $this->view->registerClass( 'BillingDetails', '\\Entities\\CompanyBillingDetail' );
        $this->view->registerClass( 'SWITCHPORT', '\\Entities\\SwitchPort' );

        // is this user watching all notes for this customer?
        if( $this->getUser()->getPreference( "customer-notes.{$cust->getId()}.notify" ) )
            $this->view->co_notify_all = true;

        // what specific notes is this cusomer watching?
        if( $this->getUser()->getAssocPreference( "customer-notes.watching" ) )
            $this->view->co_notify = $this->getUser()->getAssocPreference( "customer-notes.watching" )[0];
        else
            $this->view->co_notify = [];

        // load customer notes and the amount of unread notes for this user and customer
        $this->_fetchCustomerNotes( $cust->getId() );

        if( $cust->isRouteServerClient() )
            $this->view->rsRoutes = $this->getD2EM()->getRepository( '\\Entities\\RSPrefix' )->aggregateRouteSummariesForCustomer( $cust->getId() );

        if( $this->multiIXP() )
            $this->view->validIXPs = $this->getD2R( "\\Entities\\IXP" )->getNamesNotAssignedToCustomer( $cust->getId() );

        // does the customer have any graphs?
        $this->view->hasAggregateGraph = false;
        if( $cust->getType() != \Entities\Customer::TYPE_ASSOCIATE && !$cust->hasLeft() )
        {
            foreach( $cust->getVirtualInterfaces() as $vi )
            {
                foreach( $vi->getPhysicalInterfaces() as $pi )
                {
                    if( $pi->getStatus() == \Entities\PhysicalInterface::STATUS_CONNECTED )
                    {
                        $this->view->hasAggregateGraph = true;
                        break;
                    }
                }
            }
        }

        //is customer RS or AS112 client
        $this->view->rsclient = false;
        $this->view->as112client   = false;
        foreach( $cust->getVirtualInterfaces() as $vi )
        {
            foreach( $vi->getVlanInterfaces() as $vli )
            {
                if( $vli->getRsclient() )
                    $this->view->rsclient = true;

                if( $vli->getAs112client() )
                    $this->view->as112client = true;
            }
        }
    }


    /**
     *
     * @param IXP_Form_Customer $form The Send form object
     * @param \Entities\Customer $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     * @return bool If false, the form is not processed
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        if( !$isEdit && $this->getD2EM()->getRepository( '\\Entities\\Customer' )->findOneBy( [ 'shortname' => $form->getValue( 'shortname' ) ] ) )
        {
            $form->getElement( 'shortname' )->addError( 'This shortname is not available' );
            return false;
        }

        if( $isEdit )
        {
            $object->setLastupdated( new DateTime() );
            $object->setLastupdatedby( $this->getUser()->getId() );

            $bdetail = $object->getBillingDetails();
            $rdetail = $object->getRegistrationDetails();
        }
        else
        {
            $object->setCreated( new DateTime() );
            $object->setCreator( $this->getUser()->getUsername() );

            $bdetail = new \Entities\CompanyBillingDetail();
            $this->getD2EM()->persist( $bdetail );
            $object->setBillingDetails( $bdetail );
            $bdetail->setPurchaseOrderRequired( 0 );

            $rdetail = new \Entities\CompanyRegisteredDetail();
            $this->getD2EM()->persist( $rdetail );
            $object->setRegistrationDetails( $rdetail );

            $object->setIsReseller( 0 );
        }

        if( ( $form->getValue( 'type' ) == \Entities\Customer::TYPE_FULL || $form->getValue( 'type' ) == \Entities\Customer::TYPE_PROBONO )
                && !$form->getElement( 'irrdb' )->getValue() )
        {
            $form->getElement( 'irrdb' )->markAsError();
            return false;
        }
        else if( $form->getElement( 'irrdb' )->getValue() )
        {
            if( !( $irrdb = $this->getD2EM()->getRepository( '\\Entities\\IRRDBConfig' )->find( $form->getElement( 'irrdb' )->getValue() ) ) )
            {
                // should never be executed as it should be caught by registerInArray validator on element.
                $form->getElement( 'irrdb' )->setErrorMessages( [ 'Invalid IRRDB source' ] )->markAsError();
                return false;
            }

            $object->setIRRDB( $irrdb );
        }
        else
        {
            $object->setIRRDB( null );
        }

        if( !$isEdit )
        {
            $object->addIXP(
                $this->loadIxpById( $form->getValue( "ixp" ) )
            );
        }

        return $this->_setReseller( $form, $object );
    }


    /**
     * Sets reseller to customer from form
     *
     * @param IXP_Form_Customer $form The Send form object
     * @param \Entities\Customer $object The Doctrine2 entity (being edited or blank for add)
     * @return bool If false, the form is not processed
     */
    private function _setReseller( $form, $object )
    {
        if( !$this->resellerMode() )
            return true;

        if( $form->getValue( 'isResold' ) )
        {
            $reseller = $this->getD2R( "\\Entities\\Customer" )->find( $form->getValue( "reseller" ) );

            if( !$reseller )
            {
                $form->getElement( "resller" )->setErrorMessages( ['Select Reseller'] )->markAsError();
                return false;
            }

            if( $object->getReseller() && $object->getReseller()->getId() != $form->getValue( 'reseller' ) )
            {
                foreach( $object->getVirtualInterfaces() as $viInt )
                {
                    foreach( $viInt->getPhysicalInterfaces() as $phInt )
                    {
                        if( $phInt->getFanoutPhysicalInterface()
                                && $phInt->getFanoutPhysicalInterface()->getVirtualInterface()->getCustomer()->getId() == $object->getReseller()->getId() )
                        {
                            $form->getElement( 'isResold' )->setErrorMessages( [''] )->markAsError();
                            $this->addMessage( 'You can not change the reseller because there are still fanout ports from the current reseller linked to this customer\'s physical interfaces. You need to reassign these first.', OSS_Message::INFO );
                            return false;
                        }
                    }
                }
            }

            $object->setReseller( $reseller );
        }
        else if( $object->getReseller() )
        {
            foreach( $object->getVirtualInterfaces() as $viInt )
            {
                foreach( $viInt->getPhysicalInterfaces() as $phInt )
                {
                    if( $phInt->getFanoutPhysicalInterface()
                            && $phInt->getFanoutPhysicalInterface()->getVirtualInterface()->getCustomer()->getId() == $object->getReseller()->getId() )
                    {
                        $form->getElement( 'isResold' )->setValue(1);
                        $form->getElement( 'isResold' )->setErrorMessages( [''] )->markAsError();
                        $this->addMessage( 'You can not change this resold customer state because there are still physical interface(s) of this customer linked to fanout ports or the current reseller. You need to reassign these first.', OSS_Message::INFO );
                        return false;
                    }
                }
            }
            $object->setReseller( null );
        }

        if( !$form->getValue( 'isReseller' ) && $object->getIsReseller() && count( $object->getResoldCustomers() ) )
        {
            $form->getElement( 'isReseller' )->setErrorMessages( [''] )->markAsError();
            $this->addMessage( 'You can not change the reseller state because this customer still has resold customers. You need to reassign these first.', OSS_Message::INFO );
            return false;
        }

        return true;
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
        $this->addMessage( 'Customer successfully ' . ( $isEdit ? ' edited.' : ' added.' ), OSS_Message::SUCCESS );
        if( $isEdit )
            $this->redirect( 'customer/overview/id/' . $object->getId() );
        else
            $this->redirect( 'customer/billing-registration/id/' . $object->getId() );
    }

    /**
     *
     * @param IXP_Form_Customer $form The Send form object
     * @param \Entities\Customer $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True if we are editing, otherwise false
     * @return bool If false, the form is not processed
     */
    protected function addPreFlush( $form, $object, $isEdit )
    {

        if( !( $object->getDatejoin() instanceof DateTime ) )
            $object->setDatejoin( new DateTime( $form->getValue( 'datejoin' ) ) );

        if( !( $object->getDateleave() instanceof DateTime ) )
        {
            if( !$form->getValue( 'dateleave' ) )
                $object->setDateleave( null );
            else
                $object->setDateleave( new DateTime( $form->getValue( 'dateleave' ) ) );
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
        if( $object->getIRRDB() instanceof \Entities\IRRDBConfig )
            $form->getElement( 'irrdb' )->setValue( $object->getIRRDB()->getId() );

        $form->enableResller( $this->resellerMode() );
        $form->setMultiIXP( $this->multiIXP(), $isEdit );

        if( $this->resellerMode() )
        {
            if( $object->getReseller() instanceof \Entities\Customer )
            {
                $form->getElement( 'isResold' )->setValue( true );
                $form->getElement( 'reseller' )->setValue( $object->getReseller()->getId() );
            }
        }

        if( $isEdit )
        {
            $form->assignEntityToForm( $object->getBillingDetails(), $this );
            $form->assignEntityToForm( $object->getRegistrationDetails(), $this );
            $form->updateCancelLocation( OSS_Utils::genUrl( 'customer', 'overview', null, [ 'id' => $object->getId() ] ) );
        }

        return true;
     }


    /**
     *
     *
     */
    public function billingRegistrationAction()
    {
        $this->view->cust = $c = $this->_loadCustomer();
        $this->view->form = $form = new IXP_Form_Customer_BillingRegistration();

        $form->updateCancelLocation( OSS_Utils::genUrl( 'customer', 'overview', null, [ 'id' => $c->getId() ] ) );

        if( ( !isset( $this->_options['reseller']['no_billing_for_resold_customers'] ) || !$this->_options['reseller']['no_billing_for_resold_customers']  )
                || !$this->resellerMode() || !$c->isResoldCustomer() )
            $form->assignEntityToForm( $c->getBillingDetails(), $this );

        $old = clone $c->getBillingDetails();

        $form->assignEntityToForm( $c->getRegistrationDetails(), $this );

        // Process a submitted form if it passes initial validation
        if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
        {
            $form->assignFormToEntity( $c->getBillingDetails(), $this, true );
            $form->assignFormToEntity( $c->getRegistrationDetails(), $this, true );

            $this->getD2EM()->flush();

            if( isset( $this->_options['billing_updates']['notify'] ) && !$c->getReseller() )
            {
                $this->view->oldDetails = $old;
                $this->view->customer   = $c;

                $this->getMailer()
                    ->setFrom( $this->_options['identity']['email'], $this->_options['identity']['name'] )
                    ->setSubject( $this->_options['identity']['sitename'] . ' - ' . _( 'Billing Details Change Notification' ) )
                    ->addTo( $this->_options['billing_updates']['notify'] , $this->_options['identity']['sitename'] .' - Accounts' )
                    ->setBodyHtml( $this->view->render( 'customer/email/billing-details-changed.phtml' ) )
                    ->send();

                if( $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER )
                    $this->addMessage( "Notification of updated billing details has been sent to " . $this->_options['billing_updates']['notify'], OSS_Message::INFO );
            }

            $this->redirect( 'customer/overview/id/' . $c->getId() . '/tab/billing' );
        }
    }

    /**
     * Send the member an operations welcome mail
     *
     */
    public function welcomeEmailAction()
    {
        $this->view->customer = $c = $this->_loadCustomer();
        $this->view->admins = $c->getAdminUsers();

        // Let's get the information we need for the welcome mail from the database.
        $this->view->netinfo = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();

        $this->view->form = $form = new IXP_Form_Customer_SendEmail();

        $form->getElement( 'to' )->setValue( $c->getNocemail() );

        $emails = array();
        foreach( $c->getUsers() as $user )
            if( Zend_Validate::is( $user->getEmail(), 'EmailAddress' ) )
                $emails[] = $user->getEmail();

        $form->getElement( 'cc' )->setValue( implode( ',', $emails ) );
        $form->getElement( 'bcc' )->setValue( $this->_options['identity']['email'] );
        $form->getElement( 'subject' )->setValue( $this->_options['identity']['name'] . ' :: Welcome Mail' );
        $form->getElement( 'message' )->setValue( $this->view->render( "customer/email/welcome-email.phtml" ) );

        // Process a submitted form if it passes initial validation
        if( $this->getRequest()->isPost() && $form->isValid( $_POST ) )
        {
            $mail = $this->_processSendEmailForm( $form );
            if( $mail )
            {
                $mail->setBodyText( $form->getValue( 'message' ) );
                $mail->setFrom( $this->_options['identity']['email'], $this->_options['identity']['name'] );
                $mail->setSubject( $form->getValue( 'subject' ) );
                $mail->send();

                $this->getLogger()->info( "Welcome email sent for {$c->getName()}" );
                $this->addMessage( "Welcome email successfully sent to {$c->getName()}", OSS_Message::SUCCESS );
                return $this->redirect( 'customer/overview/id/' . $c->getId() );
            }
        }
    }



    public function detailsAction()
    {
        if( $this->getParam( 'ixp', false ) )
        {
            $this->view->ixp = $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $this->getParam( 'ixp' ) );
            if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER && !$this->getUser()->getCustomer()->getIXPs()->contains( $ixp ) )
                $this->redirectAndEnsureDie( '/erro/insufficient-permissions' );
        }
        else if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
            $this->view->ixp = $ixp = $this->getUser()->getCustomer()->getIXPs()[0];
        else
        {
            $ixp = $this->getD2R( "\\Entities\\IXP" )->findAll();
            if( $ixp )
                $this->view->ixp = $ixp = $ixp[0];
            else
                $ixp = false;
        }

        $this->view->details = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( true, false, false, $ixp ? $ixp : false );

        if( $this->multiIXP() )
            $this->view->ixpNames = $this->getD2R( '\\Entities\\IXP' )->getNames( $this->getUser() );
    }

    public function detailAction()
    {
        $this->view->cust = $c = $this->_loadCustomer( $this->getParam( 'id', null ), 'customer/details' );
        $this->view->netinfo = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();

        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            $notallow = true;
            foreach( $this->getUser()->getCustomer()->getIXPs() as $ixp )
            {
                if( $ixp->getCustomers()->contains( $c ) )
                {
                    $notallow = false;
                    break;
                }
            }

            if( $notallow )
                $this->redirectAndEnsureDie( '/erro/insufficient-permissions' );
        }
    }


    /**
     * Load a customer from the database with the given ID (or ID in request) but
     * redirect to `customer/list` if no ID or no such customer.
     *
     * @param int|bool $id The customer `$id` to load (or, if false, look for an ID parameter)
     * @param string $redirect Alternative location to redirect to
     * @return \Entities\Customer The customer object
     */
    protected function _loadCustomer( $id = false, $redirect = null )
    {
        if( $id === false )
            $id = $this->getParam( 'id', false );

        if( $id )
            $c = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $id );

        if( !$id || !$c )
        {
            $this->addMessage( 'Invalid customer ID', OSS_Message::ERROR );
            $this->redirect( $redirect === null ? 'customer/list' : $redirect );
        }

        return $c;
    }

    /**
     * A utility function to process the To / CC / BCC fields of the Send Email
     * form and return a populated Zend_Mail object.
     *
     * @see welcomeEmailAction() for an example
     * @param IXP_Form_Customer_SendEmail $form The Send Email form
     * @return Zend_Mail|bool The Zend_Mail object on success
     */
    protected function _processSendEmailForm( $form )
    {
        $emailsOkay = null;
        $mail = $this->getMailer();
        // Validate all e-mail addresses
        foreach( [ 'to' => 'To', 'cc' => 'Cc', 'bcc' => 'Bcc' ] as $element => $function )
        {
            if( ( $v = $form->getValue( $element ) ) != '' )
            {
                foreach( explode( ',', $v ) as $email )
                {
                    if( !Zend_Validate::is( $email, 'EmailAddress' ) )
                    {
                        $form->getElement( $element )->addError( 'Invalid e-mail address: ' . $email );
                        $emailsOkay = false;
                    }
                    else if( $emailsOkay === null || $emailsOkay === true )
                    {
                        $fn = "add{$function}";
                        $mail->$fn( $email );
                        $emailsOkay = true;
                    }
                }
            }
        }

        return $emailsOkay ? $mail : false;
    }



    public function unreadNotesAction()
    {
        $lastReads = $this->getUser()->getAssocPreference( 'customer-notes' )[0];

        $latestNotes = [];
        foreach( $this->getD2EM()->getRepository( '\\Entities\\CustomerNote' )->getLatestUpdate() as $ln )
        {

            if( ( !isset( $lastReads['read_upto'] ) || $lastReads['read_upto'] < strtotime( $ln['latest']  ) )
                && ( !isset( $lastReads[ $ln['cid'] ] ) || $lastReads[ $ln['cid'] ]['last_read'] < strtotime( $ln['latest'] ) ) )
                $latestNotes[] = $ln;

        }

        $this->view->notes = $latestNotes;
    }
}
