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
        
                    'autsys'      => 'AS',
                    
                    'shortname'   => [
                        'title'      => 'Shortname',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'id'
                    ],
                    
                    'peeringemail'   => 'Peering Email',
                    'noc24hphone'    => 'NOC 24h Phone',
                    
                    'type'            => [
                        'title'         => 'Type',
                        'type'          => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'        => \Entities\Customer::$CUST_TYPES_TEXT
                    ],
                    
                    'datejoin'       => [
                        'title'     => 'Joined',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];
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
                'status'          => [
                    'title'         => 'Status',
                    'type'          => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'        => \Entities\Customer::$CUST_STATUS_TEXT
                ],
                'activepeeringmatrix' => 'Active Peering Matrix',
                'peeringmacro'    => 'Peering Macro',
                'peeringpolicy'   => 'Peering Policy',
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
                'notes'           => 'Notes',
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
                            c.peeringmacro AS peeringmacro, c.peeringpolicy AS peeringpolicy,
                            c.billingContact AS billingContact, c.billingAddress1 AS billingAddress1,
                            c.billingAddress2 AS billingAddress2, c.billingCity AS billingCity, c.billingCountry AS billingCountry,
                            c.corpwww AS corpwww, c.datejoin AS datejoin, c.dateleave AS dateleave,
                            c.notes AS notes, c.lastupdated AS lastupdated, c.lastupdatedby AS lastupdatedby,
                            c.creator AS creator, c.created AS created'
                        )
                ->from( '\\Entities\\Customer', 'c' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
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
        $this->view->netinfo = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();
    	$this->view->cust = $cust = $this->_loadCustomer();
    	
    	if( $this->getCustomer()->isRouteServerClient() )
    	    $this->view->rsRoutes = $this->getD2EM()->getRepository( '\\Entities\\RSPrefix' )->aggregateRouteSummariesForCustomer( $cust->getId() );
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
        }
        else
        {
            $object->setCreated( new DateTime() );
            $object->setCreator( $this->getUser()->getUsername() );
        }
        
        if( $form->getElement( 'irrdb' )->getValue() )
        {
            $object->setIRRDB(
                    $this->getD2EM()->getRepository( '\\Entities\\IRRDBConfig' )->find( $form->getElement( 'irrdb' )->getValue() )
            );
        }
        else
        {
            $object->setIRRDB( null );
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
        $this->redirect( 'customer/overview/id/' . $object->getId() );
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
     * @param object $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     */
     protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
     {
         if( !$isEdit && isset( $this->getOptions()['identity']['default_country'] ) )
             $form->getElement( 'billingCountry' )->setValue( $this->getOptions()['identity']['default_country'] );

         $form->getElement( 'irrdb' )->setValue( $object->getIRRDB()->getId() );
          
         return true;
     }
                                                                                               

    /**
     * Send the member an operations welcome mail
     *
     */
    public function welcomeEmailAction()
    {
        $this->view->customer = $c = $this->_loadCustomer();
        $this->view->admins = $c->getAdminUsers();
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
        
        // Let's get the information we need for the welcome mail from the database.
        $this->view->netinfo = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();
        
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
        $this->view->details = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( true );
    }
        
    public function detailAction()
    {
        $this->view->cust = $c = $this->_loadCustomer( $this->getParam( 'id', null ), 'customer/details' );
        $this->view->netinfo = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();
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
    
}

