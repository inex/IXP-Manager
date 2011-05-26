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


/*
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

class DashboardController extends INEX_Controller_Action
{

    /**
     * The Identity Object
     */
    protected $_identity;

    /**
     * The User Object
     */
    protected $_user;

    /**
     * The Customer Object
     */
    protected $_customer;


    public function preDispatch()
    {
        // let's get the user's details sorted before everything else
        $auth = Zend_Auth::getInstance();
        if( !$auth->hasIdentity() )
        {
            // record the page we wanted
            $this->session->postAuthRedirect = $this->_request->getPathInfo();
            $this->_redirect( 'auth/login' );
        }
        else
        {
            $this->_identity = $auth->getIdentity();
            $this->_customer = Doctrine::getTable( 'Cust' )->find( $this->_identity['user']['custid'] );

            $this->view->customer = $this->_customer;
        }
    }

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
	        // Get peering stats as set by the member
	        $this->_generateOrUpdateMyPeeringMatrix();
	        $peering_stats = array();
	        foreach( $this->config['peering_matrix']['public'] as $v )
	            $peering_stats[$v['name']] = MyPeeringMatrixTable::getStatesTotal( $this->customer['id'], $v['number'] );

	        $peering_stats['Total'] =  MyPeeringMatrixTable::getStatesTotal( $this->customer['id'] );
	        $this->view->peering_stats = $peering_stats;

	        // Get the member's port and vlan details
	        $this->view->networkInfo = Networkinfo::toStructuredArray();
	        $this->view->connections = $this->customer->getConnections();

	        $this->view->categories = INEX_Mrtg::$CATEGORIES;

	        $this->view->rsEnabled    = $this->customer->isRouteServerClient( $this->config['primary_peering_lan']['vlan_tag'] );
	        $this->view->as112Enabled = $this->customer->isAS112Client();
        }

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'index.tpl' );
    }


    public function switchConfigurationAction()
    {
        $q = Doctrine_Query::create()
            ->from( 'ViewCustCurrentActive vca' )
            ->leftJoin( 'vca.ViewSwitchDetailsByCustid vsd' )
            ->leftJoin( 'vsd.ViewVlaninterfaceDetailsByCustid vvid' )
            ->whereIn( 'vca.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->groupBy( 'vvid.virtualinterfaceid' )
            ->orderBy( 'name' );

        $this->view->vlans = Doctrine::getTable( 'Vlan' )->findAll();

        if( ( $vlan = (int)$this->getRequest()->getParam( 'vlan', null ) ) != null )
        {
            if( is_integer( $vlan ) && Doctrine::getTable( 'Vlan' )->findByNumber( $vlan ) )
            {
                $q->andWhere( 'vvid.vlan = ?', $vlan );
                $this->view->vlannum = $vlan;
            }
        }

        $this->view->swconf = $q->execute();

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'switch-configuration.tpl' );
    }


    public function membersDetailsListAction()
    {
        $this->view->memberDetails = Doctrine_Query::create()
            ->from( 'ViewCustCurrentActive vca' )
            ->leftJoin( 'vca.ViewVlaninterfaceDetailsByCustid vvid' )
            ->whereIn( 'vca.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->groupBy( 'vvid.virtualinterfaceid' )
            ->orderBy( 'name' )
            ->execute();

        #echo '<pre>';
        #print_r( $q->execute()->toArray(true) );
        #die();

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'members-details-list.tpl' );
    }

    public function memberDetailsAction()
    {
        if( ( $custid = $this->getRequest()->getParam( 'id', null ) ) === null
            || !( $this->view->cust = Doctrine::getTable( 'Cust' )->find( (int)$custid ) ) )
        {
            $this->_forward( 'members-details-list' );
            return;
        }

        // Let's get the information we need for the welcome mail from the database.

        $this->view->networkInfo = Networkinfo::toStructuredArray();

        $this->view->connections = Doctrine_Query::create()
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
            ->where( 'c.id = ?', (int)$custid )
            ->orderBy( 'pi.monitorindex' )
            ->execute()
            ->toArray( true );

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'member-details.tpl' );
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


    public function trafficStatsAction()
    {
        // get the available graphs
        foreach( $this->config['mrtg']['traffic_graphs'] as $g )
        {
            $p = explode( '::', $g );
            $graphs[$p[0]] = $p[1];
            $images[]      = $p[0];
        }

        $graph = $this->_request->getParam( 'graph', $images[0] );
        if( !in_array( $graph, $images ) )
            $graph = $images[0];

        // is there a category selected?
        $category = $this->getRequest()->getParam( 'category', INEX_Mrtg::CATEGORY_BITS );
        if( !in_array( $category, INEX_Mrtg::$CATEGORIES_AGGREGATE ) )
            $this->view->category = INEX_Mrtg::CATEGORY_BITS;
        else
            $this->view->category = $category;

        $stats = array();
        foreach( INEX_Mrtg::$PERIODS as $period )
        {
            $mrtg = new INEX_Mrtg(
                $this->config['mrtg']['path']
                    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'inex_peering-' . $graph . '-' . $category . '.log'
            );

            $stats[$period] = $mrtg->getValues( $period, $category );
        }

        $this->view->graphs     = $graphs;
        $this->view->periods    = INEX_Mrtg::$PERIODS;
        $this->view->categories = INEX_Mrtg::$CATEGORIES_AGGREGATE;
        $this->view->graph      = $graph;
        $this->view->stats      = $stats;

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'statistics-peering-graphs.tpl' );
    }


    public function trunkGraphsAction()
    {
        // get the available graphs
        foreach( $this->config['mrtg']['trunk_graphs'] as $g )
        {
            $p = explode( '::', $g );
            $graphs[$p[0]] = $p[1];
            $images[]      = $p[0];
        }

        $graph = $this->_request->getParam( 'trunk', $images[0] );
        if( !in_array( $graph, $images ) )
            $graph = $images[0];

        $stats = array();
        foreach( INEX_Mrtg::$PERIODS as $period )
        {
            $mrtg = new INEX_Mrtg(
                $this->config['mrtg']['path']
                    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'trunks' . DIRECTORY_SEPARATOR . $graph . '.log'
            );

            $stats[$period] = $mrtg->getValues( $period, INEX_Mrtg::CATEGORY_BITS );
        }

        $this->view->graphs  = $graphs;
        $this->view->periods = INEX_Mrtg::$PERIODS;
        $this->view->graph   = $graph;
        $this->view->stats   = $stats;

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'statistics-trunk-graphs.tpl' );
    }


    public function switchGraphsAction()
    {
        // get the available graphs
        $_switches = Doctrine_Query::create()
            ->select( 'sw.name' )
            ->addSelect( 'sw.model' )
            ->addSelect( 'l.name AS location' )
            ->from( 'Switchtable sw' )
            ->leftJoin( 'sw.Cabinet c' )
            ->leftJoin( 'c.Location l' )
            ->where( 'sw.active = 1' )
            ->andWhere( 'sw.switchtype = ?', SwitchTable::SWITCHTYPE_SWITCH )
            ->orderBy( 'l.name, sw.name' )
            ->fetchArray();

        foreach( $_switches as $s )
            $switches[$s['id']] = $s;

        $switch = $this->_request->getParam( 'switch', $_switches[0]['id'] );
        if( !in_array( $switch, array_keys( $switches ) ) )
            $switch = $_switches[0]['id'];

        // is there a category selected?
        $category = $this->getRequest()->getParam( 'category', INEX_Mrtg::CATEGORY_BITS );
        if( !in_array( $category, INEX_Mrtg::$CATEGORIES_AGGREGATE ) )
            $this->view->category = INEX_Mrtg::CATEGORY_BITS;
        else
            $this->view->category = $category;

        $stats = array();
        foreach( INEX_Mrtg::$PERIODS as $period )
        {
            $mrtg = new INEX_Mrtg(
                $this->config['mrtg']['path']
                    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'switches' . DIRECTORY_SEPARATOR . 'switch-aggregate-'
                    . $switches[$switch]['name'] . '-' . $category . '.log'
            );

            $stats[$period] = $mrtg->getValues( $period, $category );
        }

        $this->view->switches   = $switches;
        $this->view->periods    = INEX_Mrtg::$PERIODS;
        $this->view->switch     = $switch;
        $this->view->stats      = $stats;
        $this->view->categories = INEX_Mrtg::$CATEGORIES_AGGREGATE;

        $this->view->display( 'dashboard' . DIRECTORY_SEPARATOR . 'statistics-switch-graphs.tpl' );
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

        $this->logger->notice( "{$this->user->username} of {$this->customer->shortname} enabled route server sessions" );
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
     * Generates or updates the users My Peering Matrix table
     *
     * @param bool $force Force the update even if it's already been done this session
     */
    private function _generateOrUpdateMyPeeringMatrix( $force = false)
    {
        // we're only going to do the following once per session unless told otherwise
        if( !isset( $this->session->myPeeringMatrixChecked ) )
            $this->session->myPeeringMatrixChecked = false;

        if( !$force && $this->session->myPeeringMatrixChecked )
            return;

        MyPeeringMatrixTable::generateOrUpdateMyPeeringMatrix( $this->customer['id'] );

        $this->session->myPeeringMatrixChecked = true;
    }

    public function myPeeringMatrixAction()
    {
        // are we downloading in a non-html format?
        $dl_as = $this->_getParam( 'as', false );

        // do we have a VLAN and is it valid
        $vlan = $this->_request->getParam( 'vlan', $this->config['peering_matrix']['public'][0]['number'] );

        // is it one of the allowed VLANs?
        $vlan_valid = false;
        foreach( $this->config['peering_matrix']['public'] as $v )
        {
            if( $v['number'] == $vlan )
            {
                $vlan_valid = true;
                break;
            }
        }

        // if it's not valid, just bounce them to the first default
        if( !$vlan_valid )
            $vlan = $this->config['peering_matrix']['public'][0]['number'];

        $this->view->vlans    = $this->config['peering_matrix']['public'];
        $this->view->vlan     = $vlan;

        // update the my peering table
        $this->_generateOrUpdateMyPeeringMatrix();

        $matrix = Doctrine_Query::create()
            ->from( 'PeeringMatrix pm' )
            ->leftJoin( 'pm.X_Cust xc' )
            ->leftJoin( 'pm.Y_Cust yc' )
            ->leftJoin( 'pm.MyPeeringMatrix mpm' )
            ->leftJoin( 'mpm.MyPeeringMatrixNotes mpmn' )
            ->where( 'pm.x_custid = ?', $this->customer['id'] )
            ->andWhere( 'pm.vlan = ?', $vlan )
            ->andWhere( 'mpm.vlan = ?', $vlan )
            ->andWhere( 'pm.y_custid = mpm.peerid')
            ->orderBy( 'pm.y_as ASC' )
            ->fetchArray();

        $this->view->matrix = $matrix;

        $this->view->ipv6     = ViewVlaninterfaceDetailsByCustidTable::getIPv6EnabledPerVLAN( $vlan );
        $this->view->rsclient = ViewVlaninterfaceDetailsByCustidTable::getRSClientEnabledPerVLAN( $vlan );

        if( $dl_as )
        {
            switch( $dl_as )
            {
                case 'ascii':
                    header( "Content-type: text/plain" );
                    header( 'Content-Disposition: filename="my-peering-matrix.txt"' );
                    $content = $this->view->render( 'dashboard/my-peering-matrix/ascii.tpl' );
                    break;

                case 'php':
                    header( "Content-type: text/plain" );
                    header( 'Content-Disposition: filename="my-peering-matrix.inc"' );
                    $content = $this->view->render( 'dashboard/my-peering-matrix/php.tpl' );
                    break;

                    case 'csv':
                default:
                    header( "Content-type: text/csv" );
                    header( 'Content-Disposition: filename="my-peering-matrix.csv"' );
                    $content = $this->view->render( 'dashboard/my-peering-matrix/csv.tpl' );
                    break;
            }

            header( "Content-length: " . strlen( $content ) );
            header( "Cache-control: private");
            echo $content;
            return;
        }


        if( $this->_request->getParam( 'email', false ) )
            $this->view->email = $this->_request->getParam( 'email', false );

        // if we haven't been here before, show the instructions
        if( !$this->user->hasPreference( 'dashboard.my_peering_matrix.first_visit' ) )
        {
            $this->user->setPreference( 'dashboard.my_peering_matrix.first_visit', mktime() );
            $this->view->showInstructions = true;
        }

        $this->view->display( 'dashboard/my-peering-matrix.tpl' );
    }

    public function myPeeringMatrixEmailAction()
    {
        $bcust = Doctrine_Core::getTable( 'Cust' )->find( $this->_request->getParam( 'id', null ) );

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html');

        if( !$bcust && $this->_request->getParam( 'send' ) == '1' )
        {
            $this->getResponse()
                 ->setBody( Zend_Json::encode(
                    array(
                        'status' => '0',
                        'message' => "Error: Invalid parameters supplied"
                  ) ) )
            ->sendResponse();
            exit;
        }
        else if( !$bcust )
        {
            echo '';
            exit;
        }

        // need to get VLAN interfaces in common for these two members
        $aints = Doctrine_Core::getTable( 'ViewVlaninterfaceDetailsByCustid' )->findByCustid(
            $this->customer['id'], Doctrine_Core::HYDRATE_ARRAY
        );
        $bints = Doctrine_Core::getTable( 'ViewVlaninterfaceDetailsByCustid' )->findByCustid(
            $bcust['id'], Doctrine_Core::HYDRATE_ARRAY
        );

        // potential peerings
        $pp = array();
        $count = 0;
        foreach( $aints as $aint )
        {
            // does b member have one (or more than one)?
            foreach( $bints as $bint )
            {
                if( $aint['vlanid'] == $bint['vlanid'] )
                {
                    $pp[$count]['a'] = $aint;
                    $pp[$count]['b'] = $bint;
                    $count++;
                }
            }
        }

        $this->view->bcust  = $bcust;
        $this->view->pp     = $pp;

        if( $this->_request->getParam( 'send' ) == '1' )
        {
            $mail = new Zend_Mail();
            $mail->setFrom( $this->customer['peeringemail'], $this->customer['peeringemail'] . ' Peering Team' )
                 ->setSubject( stripslashes( $this->_request->getParam( 'subject' ) ) )
                 ->addTo( $bcust['peeringemail'], $bcust['name'] . ' Peering Team' )
                 ->addBcc( $this->customer['peeringemail'], $this->customer['peeringemail'] . ' Peering Team' )
                 ->setBodyText( stripslashes( $this->_request->getParam( 'message' ) ) );

            try {
                $mail->send();

                $myPeerRecord = Doctrine_Query::create()
                    ->from( 'MyPeeringMatrix mpm' )
                    ->where( 'mpm.custid = ?', $this->customer['id'] )
                    ->andWhere( 'mpm.peerid = ?', $bcust['id'] )
                    ->fetchOne( null, Doctrine_core::HYDRATE_RECORD );

                $myPeerRecord->updateNotes( date( 'Y-m-d ' ) . $this->user['username']
                        . ": Peering request sent by email via IXP Manager",
                    true
                );

                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '1',
                            'message' => "Email successfully sent to the {$bcust['name']} Peering Team",
                            'commentAdded' => '1', 'cid' => $bcust['id']
                        ) ) )
                    ->sendResponse();
            }
            catch( Zend_Exception $e )
            {
                $this->logger->err( $e->getMessage() . "\n\n" . $e->getTraceAsString() );

                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '0',
                            'message' => "Error: Sorry, we could not send the email. Please try later or send manually."
                        ) ) )
                    ->sendResponse();
            }


        }
        else
        {

            $content = array(
                'subject' => $this->_config['identity']['orgname'] . " Peering Request between AS" . $this->customer['autsys']
                                . ' - AS' . $bcust['autsys'],
                'to'      => $bcust['name'] . " Peering Team <" . $bcust['peeringemail'] . ">",
                'from'    => $this->customer['name'] . " Peering Team <" . $this->customer['peeringemail'] . ">",
                'bcc'     => $this->customer['name'] . " Peering Team <" . $this->customer['peeringemail'] . ">",
                'message' => $this->view->render( 'dashboard/email/peering-request.tpl' )
            );

            $this->getResponse()
                ->setHeader('Content-Type', 'text/html')
                ->setBody( Zend_Json::encode( $content ) )
                ->sendResponse();
        }

        exit();
    }


    public function myPeeringMatrixNotesAction()
    {
        $bcust = Doctrine_Core::getTable( 'Cust' )->find( $this->_request->getParam( 'id', null ) );

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html');

        if( !$bcust && $this->_request->getParam( 'save' ) == '1' )
        {
            $this->getResponse()
                 ->setBody( Zend_Json::encode(
                    array(
                        'status' => '0',
                        'message' => "Error: Invalid parameters supplied"
                  ) ) )
            ->sendResponse();
            exit;
        }
        else if( !$bcust )
        {
            echo '';
            exit;
        }

        $myPeerRecord = Doctrine_Query::create()
            ->from( 'MyPeeringMatrix mpm' )
            ->where( 'mpm.custid = ?', $this->customer['id'] )
            ->andWhere( 'mpm.peerid = ?', $bcust['id'] )
            ->fetchOne( null, Doctrine_core::HYDRATE_RECORD );

        if( $this->_request->getParam( 'save' ) == '1' )
        {
            try
            {
                $myPeerRecord->updateNotes( stripslashes( $this->_request->getParam( 'notes' ) ) );
                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '1',
                            'message' => "Peering notes updated for {$bcust['name']}.",
                            'commentAdded' => '1', 'cid' => $bcust['id']
                        ) ) )
                    ->sendResponse();
            }
            catch( Zend_Exception $e )
            {
                $this->getResponse()
                    ->setBody( Zend_Json::encode(
                        array(
                            'status' => '1',
                            'message' => "Error: Sorry, we could not save your updated notes. Please contact support to report this issue."
                        ) ) )
                    ->sendResponse();
            }
        }
        else
        {
            $prefix = date( 'Y-m-d ' ) . $this->user['username'] . ": ";
            $content = array(
                'name'  => $bcust['name'],
                'notes' => "$prefix\n\n" . $myPeerRecord->getNotes(),
                'pos'   => strlen( $prefix )
            );

            $this->getResponse()
                ->setHeader('Content-Type', 'text/html')
                ->setBody( Zend_Json::encode( $content ) )
                ->sendResponse();
        }

        exit();
    }


    public function myPeeringMatrixPeeredStateAction()
    {
        $type  = $this->_request->getParam( 'type', 'state' );

        $bcust = Doctrine_Core::getTable( 'Cust' )->find( $this->_request->getParam( 'id', false ) );

        if( !$bcust )
            exit;

        // do we have a VLAN and is it valid
        $vlan = $this->_request->getParam( 'vlan', false );

        // is it one of the allowed VLANs?
        $vlan_valid = false;
        foreach( $this->config['peering_matrix']['public'] as $v )
        {
            if( $v['number'] == $vlan )
            {
                $vlan_valid = true;
                break;
            }
        }

        // if it's not valid, just bounce them to the first default
        if( !$vlan_valid )
            exit;


        $myPeeringMatrix = Doctrine_Query::create()
            ->from( 'MyPeeringMatrix mpm' )
            ->where( 'mpm.custid = ?', $this->customer['id'] )
            ->andWhere( 'mpm.peerid = ?', $bcust['id'] )
            ->andWhere( 'mpm.vlan = ?', $vlan )
            ->fetchOne( null, Doctrine_Core::HYDRATE_RECORD );

        if( !$myPeeringMatrix )
            exit;

        if( $type == 'ipv6' )
        {
            $myPeeringMatrix['ipv6'] = ( $myPeeringMatrix['ipv6'] + 1 ) % 2;
            $newstate = $myPeeringMatrix['ipv6'];
        }
        else
        {
	        $newstate = ( array_search( $myPeeringMatrix['peered'], MyPeeringMatrix::$PEERED_STATES ) + 1 )
	            % count( MyPeeringMatrix::$PEERED_STATES );

	        $myPeeringMatrix['peered'] = MyPeeringMatrix::$PEERED_STATES[ $newstate ];
        }

        $myPeeringMatrix->save();

        $content = array( 'newstate' => $newstate );

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody( Zend_Json::encode( $content ) )
            ->sendResponse();
        exit();
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
}
