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
     * Return a Doctrine result of the users IXP connections.
     */
    private function _getConnections( $cust = null )
    {
        if( $cust === null )
            $cust = $this->_customer;

        return Doctrine_Query::create()
            ->from( 'Virtualinterface vi' )
            ->leftJoin( 'vi.Cust c' )
            ->leftJoin( 'vi.Physicalinterface pi' )
            ->leftJoin( 'vi.Vlaninterface vli' )
            ->leftJoin( 'vli.Ipv4address v4' )
            ->leftJoin( 'vli.Ipv6address v6' )
            ->leftJoin( 'vli.Vlan v' )
            ->leftJoin( 'pi.Switchport sp' )
            ->leftJoin( 'sp.SwitchTable s' )
            ->leftJoin( 's.Cabinet cb' )
            ->leftJoin( 'cb.Location l' )
            ->where( 'c.id = ?', $cust['id'] )
            ->orderBy( 'pi.monitorindex' )
            ->execute();
    }

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

    /**
     * Return a Doctrine result of a customer.
     */
    private function _getCustomerByShortname( $shortname = null )
    {
        if( $shortname === null || $shortname == $this->_customer->shortname )
            return $this->_customer;

        if( $cust = Doctrine::getTable( 'Cust' )->findOneByShortname( $shortname ) )
            return $cust;

        return $this->_customer;
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



    public function statisticsAction()
    {
        if( $this->user['privs'] < User::AUTH_SUPERUSER )
            $shortname = $this->customer['shortname'];
        else
            $shortname = $this->getRequest()->getParam( 'shortname', $this->customer['shortname'] );

        $cust = $this->_getCustomerByShortname( $shortname );

        // get the connections
        $this->view->connections = $this->_getConnections( $cust );

        // is there a category selected?
        $category = $this->getRequest()->getParam( 'category', false );
        if( $category === false )
            $this->view->category = INEX_Mrtg::$CATEGORIES['Bits'];
        else if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $this->view->category = INEX_Mrtg::$CATEGORIES['Bits'];
        else
            $this->view->category = $category;

        $this->view->categories = INEX_Mrtg::$CATEGORIES;
        $this->view->shortname = $shortname;

        $this->view->customer = $cust;

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'statistics.tpl' );
    }


    public function statisticsDrilldownAction()
    {
        // is there a category selected?
        $category = $this->getRequest()->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );
        if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $this->view->category = INEX_Mrtg::$CATEGORIES['Bits'];
        else
            $this->view->category = $category;

        // monitorindex and member shortname is checked by the MrtgController so no need for it here
        $monitorindex = $this->getRequest()->getParam( 'monitorindex', 1 );
        if( $this->user['privs'] < User::AUTH_SUPERUSER )
            $shortname = $this->customer['shortname'];
        else
            $shortname = $this->getRequest()->getParam( 'shortname', $this->customer['shortname'] );

        $cust = $this->_getCustomerByShortname( $shortname );

        if( $monitorindex != 'aggregate' )
        {
	        $interface = Doctrine_Query::create()
	            ->from( 'Virtualinterface vi' )
	            ->leftJoin( 'vi.Physicalinterface pi' )
	            ->leftJoin( 'pi.Switchport sp' )
	            ->leftJoin( 'sp.SwitchTable s' )
	            ->where( 'vi.custid = ?', $cust['id'] )
	            ->andWhere( 'pi.monitorindex = ?', $monitorindex )
	            ->limit( 1 )
	            ->fetchOne( null, Doctrine_Core::HYDRATE_ARRAY );

            $this->view->switchname = $interface['Physicalinterface'][0]['Switchport']['SwitchTable']['name'];
            $this->view->portname   = $interface['Physicalinterface'][0]['Switchport']['name'];
        }
        else
        {
            $this->view->switchname = '';
            $this->view->portname   = '';
        }

        $this->view->periods      = INEX_Mrtg::$PERIODS;
        $this->view->categories   = INEX_Mrtg::$CATEGORIES;
        $this->view->monitorindex = $monitorindex;

        $stats = array();
        foreach( INEX_Mrtg::$PERIODS as $period )
        {
            $mrtg = new INEX_Mrtg( INEX_Mrtg::getMrtgFilePath( $this->config['mrtg']['path'],
                    'LOG', $this->view->monitorindex, $this->view->category,
                    $cust->shortname )
            );

            $stats[$period] = $mrtg->getValues( $period, $this->view->category );
        }

        $this->view->customer  = $cust;
        $this->view->shortname = $shortname;
        $this->view->stats     = $stats;

        if( $this->_request->getParam( 'mini', 0 ) == '1' )
        {
            if( $this->config->resources.zfdebug.enabled )
            {
	            $this->getFrontController()->unregisterPlugin(
	                $this->_bootstrap->getResource( 'zfdebug' )
	            );
            }
            $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'statistics-drilldown-mini.tpl' );
        }
        else
            $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'statistics-drilldown.tpl' );
    }


    /**
     * sFlow Peer to Peer statistics
     */
    public function p2pAction()
    {
        if( $this->user['privs'] < User::AUTH_SUPERUSER )
        {
            if( $this->getRequest()->getParam( 'shortname', $this->customer['shortname'] ) != $this->customer['shortname'] )
                $this->getLogger()->alert( $this->customer['shortname'] . " requested shortname " . $this->getRequest()->getParam( 'shortname' ) . " in p2p" );
                
            $shortname = $this->customer['shortname'];
        }
        else
            $shortname = $this->getRequest()->getParam( 'shortname', $this->customer['shortname'] );

        $cust = $this->_getCustomerByShortname( $shortname );

        // is there a category selected?
        $this->view->category = $category = $this->getRequest()->getParam( 'category', INEX_Mrtg::$CATEGORIES_AGGREGATE['Bits'] );
        if( !in_array( $category, INEX_Mrtg::$CATEGORIES_AGGREGATE ) )
            $this->view->category = $category = INEX_Mrtg::$CATEGORIES_AGGREGATE['Bits'];

        // is there a period selected?
        $this->view->period = $period = $this->getRequest()->getParam( 'period', INEX_Mrtg::$PERIODS['Day'] );
        if( !in_array( $period, INEX_Mrtg::$PERIODS ) )
            $this->view->period = $period = INEX_Mrtg::$PERIODS['Day'];

        // is there an infrastructure selected?
        $infra = $this->view->infra = $this->getRequest()->getParam( 'infra', 1 );
        if( !in_array( $infra, INEX_Mrtg::$INFRASTRUCTURES ) )
            $infra = $this->view->infra = INEX_Mrtg::INFRASTRUCTURE_PRIMARY;

        // is there an protocol selected?
        $proto = $this->view->proto = $this->getRequest()->getParam( 'proto', 4 );
        if( !in_array( $proto, INEX_Mrtg::$PROTOCOLS ) )
            $proto = $this->view->proto = INEX_Mrtg::PROTOCOL_IPV4;

        $dvid = $this->view->dvid = $this->getRequest()->getParam( 'dvid', false );

        // find the virtual interfaces that this customer has
        // find the possible virtual interfaces that this customer peers with
        $this->view->customersVirtualInterfaces = Doctrine_Query::create()
            ->select( '
                c.id, c.name, c.shortname, vi.id, pi.id, vint.id, sp.id, s.id
                ' )
            ->from( 'Cust c' )
            ->leftJoin( 'c.Virtualinterface vi' )
            ->leftJoin( 'vi.Physicalinterface pi' )
            ->leftJoin( 'vi.Vlaninterface vint' )
            ->leftJoin( 'pi.Switchport sp' )
            ->leftJoin( 'sp.SwitchTable s' )
            ->where( 's.infrastructure = ?', $infra )
            ->andWhere( 'vint.ipv' . $proto . 'enabled = 1' )
            ->andWhere( 'c.shortname = ?', $shortname )
            ->fetchOne( null, Doctrine_Core::HYDRATE_ARRAY );

        if( isset( $this->view->customersVirtualInterfaces['Virtualinterface'] ) and count( $this->view->customersVirtualInterfaces['Virtualinterface'] ) )
        {
            if( count( $this->view->customersVirtualInterfaces['Virtualinterface'] ) > 1 )
            {
                $interfaces = array();
                foreach( $this->view->customersVirtualInterfaces['Virtualinterface'] as $vint )
                    $interfaces[] = $vint['id'];
                
                $interface = $this->view->interface = $this->getRequest()->getParam( 'interface', $interfaces[0] );
                if( !in_array( $interface, $interfaces ) )
                    $interface = $this->view->interface = $interfaces[0];
                
                $this->view->svid = $interface;
            }
            else
                $this->view->svid = $this->view->customersVirtualInterfaces['Virtualinterface'][0]['id'];
            
            // find the possible virtual interfaces that this customer peers with
            $this->view->customersWithVirtualInterfaces = Doctrine_Query::create()
                ->select( '
                    c.id, c.name, c.shortname, vi.id, pi.id, vint.id, sp.id, s.id
                    ' )
                ->from( 'Cust c' )
                ->leftJoin( 'c.Virtualinterface vi' )
                ->leftJoin( 'vi.Physicalinterface pi' )
                ->leftJoin( 'vi.Vlaninterface vint' )
                ->leftJoin( 'pi.Switchport sp' )
                ->leftJoin( 'sp.SwitchTable s' )
                ->where( 's.infrastructure = ?', $infra )
                ->andWhere( 'vint.ipv' . $proto . 'enabled = 1' )
                ->andWhere( 'c.shortname != ?', $shortname )
                ->andWhereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_PROBONO ) )
                ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
                ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
                ->andWhere( 'pi.status = ?', Physicalinterface::STATUS_CONNECTED )
                ->orderBy( 'c.name ASC' );

            if( $dvid )
            {
                $this->view->dcust
                    = $this->view->customersWithVirtualInterfaces->andWhere( 'vi.id = ?', $dvid )
                        ->fetchOne( null, Doctrine_Core::HYDRATE_ARRAY );
            }
            else
            {
                $this->view->customersWithVirtualInterfaces
                    = $this->view->customersWithVirtualInterfaces->fetchArray();
            }
        }
        
        $this->view->categories      = INEX_Mrtg::$CATEGORIES_AGGREGATE;
        $this->view->periods         = INEX_Mrtg::$PERIODS;
        $this->view->infrastructures = INEX_Mrtg::$INFRASTRUCTURES;
        $this->view->protocols       = INEX_Mrtg::$PROTOCOLS;
        
        $this->view->shortname = $shortname;

        $this->view->customer = $cust;

        if( $dvid )
            $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'p2p-single.tpl' );
        else
            $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'p2p.tpl' );
    }







    public function rsInfoAction()
    {
        $this->view->rsEnabled = $this->customer->isRouteServerClient(  $this->config['primary_peering_lan']['vlan_tag'] );

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'rs-info.tpl' );
    }

    public function enableRouteServerAction()
    {
        foreach( $this->customer->getConnections() as $connection )
            foreach( $connection->Vlaninterface as $interface )
            {
                $interface['rsclient'] = 1;
	            $interface->save();
            }

        $this->getLogger()->notice( "{$this->user->username} of {$this->customer->shortname} enabled route server sessions" );
        $this->view->rsSessionsEnabled = true;
        $this->_forward( 'rs-info' );
    }

    public function as112Action()
    {
        if( $this->_request->getParam( 'enable', 0 ) )
        {
	        foreach( $this->customer->getConnections() as $connection )
	            foreach( $connection->Vlaninterface as $interface )
	            {
	                $interface['as112client'] = 1;
	                $interface->save();
	            }
	        $this->view->as112JustEnabled = true;
        }

        $this->view->as112Enabled = $this->customer->isAS112Client();
        $this->view->rsEnabled    = $this->customer->isRouteServerClient( $this->config['primary_peering_lan']['vlan_tag'] );

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'as112.tpl' );
    }

    public function staticAction()
    {
        $page = $this->_request->getParam( 'page', null );

        if( $page == null )
            return( $this->_redirect( 'dashboard/index' ) );

        // does the requested static page exist? And if so, display it
        if( preg_match( '/^[a-zA-Z0-9\-]+$/', $page ) > 0
                && file_exists( APPLICATION_PATH . "/views/dashboard/static/{$page}.tpl" ) )
        {
            $this->view->display( "dashboard/static/{$page}.tpl" );
        }
        else
        {
            $this->view->message = new INEX_Message(
                "The requested page was not found.",
                INEX_Message::MESSAGE_TYPE_ERROR
            );
            $this->_forward( 'index' );
        }
    }


    public function peeringMatrixAction()
    {
        $lan = $this->_request->getParam( 'lan', 0 );

        if( !isset( $this->config['peering_matrix']['public'][$lan] ) )
        {
            $this->session->message = new INEX_Message(
                            "Invalid peering matrix requested",
                            INEX_Message::MESSAGE_TYPE_ERROR
                        );

            return( $this->_redirect( 'dashboard' ) );
        }

        $peering_states = Doctrine_Query::create()
            ->select( 'pm.x_as, pm.y_as, pm.peering_status' )
            ->addSelect( 'xc.name, xc.id, xc.peeringmacro, xc.peeringpolicy' )
            ->addSelect( 'yc.name, yc.id, yc.peeringmacro, yc.peeringpolicy' )
            ->from( 'PeeringMatrix pm' )
            ->leftJoin( 'pm.X_Cust xc' )
            ->leftJoin( 'pm.Y_Cust yc' )
            ->where( 'pm.vlan = ?', $this->config['peering_matrix']['public'][$lan]['number'] )
            ->orderBy( 'pm.x_as ASC, pm.y_as ASC' )
            ->fetchArray();

        // try and arrange the array as n x n keyed by x's as number
        $matrix = array();

        $potential = 0;
        $active    = 0;

        foreach( $peering_states as $pm )
        {
            $matrix[$pm['x_as']][] = $pm;

            if( $pm['peering_status'] == 'YES' )
                $active++;

            $potential++;
        }

        $this->view->potential = $potential;
        $this->view->active    = $active;

        $this->view->lan    = $lan;
        $this->view->matrix = $matrix;
        $this->view->display( 'dashboard/peering-matrix.tpl' );
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
    
    
    public function weathermapAction()
    {
        // do we have a valid key
        $key = $this->_getParam( 'id', null );
        
        if( $key === null || !isset( $this->config['weathermap'][$key] ) )
        {
            $this->session->message = new INEX_Message(
            	'Unknown weathermap requested', INEX_Message::MESSAGE_TYPE_ERROR
            );
            $this->_redirect();
        }
        
        $this->view->weathermap = $this->config['weathermap'][$key];
        $this->view->display( 'dashboard/statistics-weathermap.tpl' );
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


