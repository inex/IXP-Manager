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
 * Controller: Customer user dashboard and actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DashboardController extends IXP_Controller_AuthRequiredAction
{
    
    public function preDispatch()
    {
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_CUSTUSER )
            $this->_redirect( '' );
    }
    
    public function indexAction()
    {
        // Get the three most recent members
        $this->view->recentMembers = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getRecent();

        $this->view->cust = $this->getUser()->getCustomer();
        
        /*
            // is there a meeting available to register for?
            $this->view->meeting = false;
    
            if( ( $meeting = MeetingTable::getUpcomingMeeting() ) !== false
                && ( !isset( $this->session->dashboard_skip_meeting ) || !$this->session->dashboard_skip_meeting )
            )
            {
                $rsvp = $this->getUser()->hasPreference( 'meeting.attending.' . $meeting['id'] );
    
                if( $rsvp === false )
                {
                    $this->view->meeting = $meeting;
                    $this->view->meeting_pref = $rsvp;
                }
            }
        */

        if( !$this->getCustomer()->isTypeAssociate() )
        {
            $this->view->resoldCustomer = $this->getCustomer()->isResoldCustomer();
            $this->view->netinfo = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();
            $this->view->categories = IXP_Mrtg::$CATEGORIES;

            $this->getNocDetailsForm();
            $this->getBillingDetailsForm();
            
            if( $this->getCustomer()->isRouteServerClient() )
            {
                $this->view->rsRoutes = $this->getD2EM()->getRepository( '\\Entities\\RSPrefix' )
                ->aggregateRouteSummariesForCustomer( $this->getCustomer()->getId() );
            }
        }

        if( $this->multiIXP() )
            $this->view->validIXPs = $this->getD2R( "\\Entities\\IXP" )->getNamesNotAssignedToCustomer( $this->getUser()->getCustomer()->getId() );
        
        // do we have any notes?
        $this->_fetchCustomerNotes( $this->getCustomer()->getId(), true );
    }
    
    public function updateNocAction()
    {
        $form = $this->getNocDetailsForm();
        
        if( $this->getRequest()->isPost() )
        {
            if( $form->isValid( $_POST ) )
            {
                $form->assignFormToEntity( $this->getCustomer(), $this, true );
                $this->getD2EM()->flush();
                $this->addMessage( 'Your NOC details have been updated', OSS_Message::SUCCESS );
            }
            else
            {
                $this->addMessage( 'There was an error updating your NOC details', OSS_Message::ERROR );
            }
        }
        
        $this->forward( 'index' );
    }
    
    protected function getNocDetailsForm()
    {
        $form = new IXP_Form_Customer_NocDetails();
        $form->assignEntityToForm( $this->getCustomer(), $this, true );
        $form->setAction( OSS_Utils::genUrl( 'dashboard', 'update-noc' ) );
        
        if( !isset( $this->view->nocDetails ) )
            $this->view->nocDetails = $form;
        
        return $form;
    }


    public function updateBillingAction()
    {
        $form = $this->getBillingDetailsForm();
        
        if( $this->getRequest()->isPost() )
        {
            if( $form->isValid( $_POST ) )
            {
                if( $this->_options['billing_updates']['notify'] )
                    $old = clone $this->getCustomer()->getBillingDetails();

                $form->assignFormToEntity( $this->getCustomer()->getBillingDetails(), $this, true );
                $this->getD2EM()->flush();
                $this->addMessage( 'Your billing details have been updated', OSS_Message::SUCCESS );
                
                if( isset( $this->_options['billing_updates']['notify'] )  )
                {
                    $this->view->oldDetails = $old;
                    $this->view->customer = $this->getCustomer();
                    
                    $this->getMailer()
                        ->setFrom( $this->_options['identity']['email'], $this->_options['identity']['name'] )
                        ->setSubject( $this->_options['identity']['sitename'] . ' - ' . _( 'Billing Details Change Notification' ) )
                        ->addTo( $this->_options['billing_updates']['notify'] , $this->_options['identity']['sitename'] .' - Accounts' )
                        ->setBodyHtml( $this->view->render( 'customer/email/billing-details-changed.phtml' ) )
                        ->send();

                }
            }
            else
            {
                $this->addMessage( 'There was an error updating your billing details', OSS_Message::ERROR );
            }
        }
        
        $this->forward( 'index' );
    }
    
    protected function getBillingDetailsForm()
    {
        $form = new IXP_Form_Customer_BillingDetails();
        
        if( !isset( $this->view->billingDetails ) )
            $this->view->billingDetails = $form;
        
        $form->assignEntityToForm( $this->getCustomer()->getBillingDetails(), $this, true );
        $form->setAction( OSS_Utils::genUrl( 'dashboard', 'update-billing' ) );
        return $form;
    }
}


