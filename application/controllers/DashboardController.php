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
 * Controller: Customer user dashboard and actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DashboardController extends INEX_Controller_AuthRequiredAction
{
    
    public function preDispatch()
    {
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_CUSTUSER )
            $this->_redirect( '' );
    }
    
    public function indexAction()
    {
        // Get the three most recent members
        $this->view->recentMembers = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getRecent( 3 );

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
            $this->view->netinfo = $this->getD2EM()->getRepository( '\\Entities\\NetworkInfo' )->asVlanProtoArray();
	        $this->view->categories = INEX_Mrtg::$CATEGORIES;

	        $this->getNocDetailsForm();
	        $this->getBillingDetailsForm();
        }
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
        $form = new INEX_Form_Customer_NocDetails();
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
                $form->assignFormToEntity( $this->getCustomer(), $this, true );
                $this->getD2EM()->flush();
                $this->addMessage( 'Your billing details have been updated', OSS_Message::SUCCESS );
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
        $form = new INEX_Form_Customer_BillingDetails();
        
        if( !isset( $this->view->billingDetails ) )
            $this->view->billingDetails = $form;
        
        $form->assignEntityToForm( $this->getCustomer(), $this, true );
        $form->setAction( OSS_Utils::genUrl( 'dashboard', 'update-billing' ) );
        return $form;
    }
}


