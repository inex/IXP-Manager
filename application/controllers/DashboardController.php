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

    /**
     * Return a Doctrine result of the users VLANs.
     */
    private function _getVLANS( $cust = null )
    {
        if( $cust === null )
            $cust = $this->_customer;

        $vints = Doctrine_Query::create()
            ->from( 'Vlaninterface vint' )
            ->leftJoin( 'vint.Virtualinterface vi' )
            ->leftJoin( 'vi.Cust c' )
            ->where( 'c.id = ?', $cust['id'] )
            ->execute();
        $vlanids = array();
        foreach( $vints as $v )
            $vlanids[] = $v['vlanid'];
            
        return Doctrine_Query::create()
            ->from( 'Vlan v' )
            ->whereIn( 'v.id', $vlanids )
            ->execute();
    }


    public function indexAction()
    {
        // Get the three most recent members
        $this->view->recentMembers = Doctrine_Query::create()
            ->from( 'Cust c' )
            ->where( 'c.type != ?', Cust::TYPE_ASSOCIATE )
            ->orderBy( 'c.datejoin DESC' )
            ->limit( 3 )
            ->execute()
            ->toArray();

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


        $this->view->recentMembers = Doctrine_Query::create()
            ->from( 'Cust c' )
            ->where( 'c.type != ?', Cust::TYPE_ASSOCIATE )
            ->orderBy( 'c.datejoin DESC' )
            ->limit( 3 )
            ->execute()
            ->toArray();


        if( $this->customer->isFullMember() )
        {
	        // Get the member's port and vlan details
	        $this->view->networkInfo = Networkinfo::toStructuredArray();
	        $this->view->connections = $this->customer->getConnections();

	        $this->view->categories = INEX_Mrtg::$CATEGORIES;

	        $this->view->rsEnabled    = $this->customer->isRouteServerClient( $this->config['primary_peering_lan']['vlan_tag'] );
	        $this->view->as112Enabled = $this->customer->isAS112Client();
	        
	        
	        $this->view->nocDetails     = $this->_getNocDetailsForm();
	        $this->view->billingDetails = $this->_getBillingDetailsForm();
        }

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'index.tpl' );
    }










    /**
     * Allow users to set the member preferences for delivery of various SEC event
     * notifications.
     */
    public function secEventEmailConfigAction()
    {
        // possible events that can be set with default values
        $events = SecEvent::$TYPES_DEFAULTS;

        // are we updating the preferences?
        if( $this->_request->getParam( 'update', false ) )
        {
            // get existing preferences, if any
            foreach( $events as $name => $value )
            {
                if( $this->_request->getParam( $name, 0 ) )
                {
                    $this->user->Parent->setPreference( 'sec.notification.' . $name, 1 );
                    $events[$name] = 1;
                }
                else
                {
                    $this->user->Parent->setPreference( 'sec.notification.' . $name, 0 );
                    $events[$name] = 0;
                }
            }

            $this->view->message = new INEX_Message( 'SEC Notification preferecnces updated',
                INEX_Message::MESSAGE_TYPE_SUCCESS
            );
        }
        else
        {
            // get existing preferences, if any
            foreach( $events as $name => $value )
            {
                $pref = $this->user->Parent->getPreference( 'sec.notification.' . $name );

                if( $pref === false ) // not set
                    $this->user->Parent->setPreference( 'sec.notification.' . $name, 1 );
                else
                    $events[$name] = $pref;
            }
        }

        $this->view->assign( 'events', $events );
        $this->view->display( 'dashboard/sec-event-email-config.tpl' );
    }
    
    
    
    public function updateNocAction()
    {
        $f = $this->_getNocDetailsForm();
        
        if( $this->getRequest()->isPost() && $f->isValid( $_POST ) )
        {
            $f->assignToModel( $this->customer );
            $this->customer->save();
            
            $this->view->message = new INEX_Message( 'Your NOC details have been updated',
                INEX_Message::MESSAGE_TYPE_SUCCESS
            );
        }
        
        $this->_forward( 'index' );
    }
    
    private function _getNocDetailsForm()
    {
        $f = new INEX_Form_Customer_NocDetails();
        $f->assignFromModel( $this->customer );
        $f->setAction( Zend_Controller_Front::getInstance()->getBaseUrl() . '/dashboard/update-noc' );
        return $f;
    }


    public function updateBillingAction()
    {
        $f = $this->_getBillingDetailsForm();
        
        if( $this->getRequest()->isPost() && $f->isValid( $_POST ) )
        {
            $f->assignToModel( $this->customer );
            $this->customer->save();
            
            $this->view->message = new INEX_Message( 'Your billing details have been updated',
                INEX_Message::MESSAGE_TYPE_SUCCESS
            );
        }
        
        $this->_forward( 'index' );
    }
    
    private function _getBillingDetailsForm()
    {
        $f = new INEX_Form_Customer_BillingDetails();
        $f->assignFromModel( $this->customer );
        $f->setAction( Zend_Controller_Front::getInstance()->getBaseUrl() . '/dashboard/update-billing' );
        return $f;
    }
}


