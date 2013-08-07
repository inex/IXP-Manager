<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Statistics / graphs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsController extends IXP_Controller_AuthRequiredAction
{
    use IXP_Controller_Trait_Statistics;
    

    
    public function listAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );

        $this->_setIXP();
        $this->_setInfrastructure();
        
        $this->view->custs = $custs = $this->getD2R( '\\Entities\\Customer')->getCurrentActive( false, true, false, $this->ixp );
        
        if( !is_string( $this->infra ) && $this->infra )
            $this->view->custs = $this->getD2R( '\\Entities\\Customer')->filterForInfrastructure( $custs, $this->infra );
    }

    public function membersAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );
    
        $this->_setIXP();
        $this->_setInfrastructure();
        $this->_setCategory();
        $this->_setPeriod();
        
        $this->view->custs = $custs = $this->getD2R( '\\Entities\\Customer')->getCurrentActive( false, true, false, $this->ixp );
        
        if( !is_string( $this->infra ) && $this->infra )
            $this->view->custs = $this->getD2R( '\\Entities\\Customer')->filterForInfrastructure( $custs, $this->infra );
    }
    
    public function memberAction()
    {
        if( $this->getUser()->getPrivs() < \Entities\User::AUTH_SUPERUSER )
            $shortname = $this->getCustomer()->getShortname();
        else
            $shortname = $this->getParam( 'shortname', $this->getCustomer()->getShortname() );
    
        $this->view->cust = $cust = $this->loadCustomerByShortname( $shortname );  // redirects on failure
        
        $this->_setIXP( $cust );
        
        $this->_setCategory();
    }
    
    public function memberDrilldownAction()
    {
        $category = $this->_setCategory();
        $this->view->monitorindex = $monitorindex = $this->getParam( 'monitorindex', 1 );
    
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
            $shortname = $this->getCustomer()->getShortname();
        else
            $shortname = $this->getParam( 'shortname', $this->getCustomer()->getShortname() );
    
        $this->view->cust = $cust = $this->loadCustomerByShortname( $shortname );  // redirects on failure

        $this->_setIXP( $cust );
        
        if( $monitorindex != 'aggregate' )
        {
            $vint = false;
            $pi = null;
            foreach( $cust->getVirtualInterfaces() as $vi )
            {
                foreach( $vi->getPhysicalInterfaces() as $pi )
                {
                    if( $pi->getMonitorindex() == $monitorindex )
                    {
                        $this->view->pi   = $pi;
                        $this->view->vint = $vint = $vi;
                        break 2;
                    }
                }
            }
    
            if( !$vint )
                throw new IXP_Exception( 'Member statistics drilldown requested for unknown monitor index' );
    
            $this->view->switchname = $pi->getSwitchPort()->getSwitcher()->getName();
            $this->view->portname   = $pi->getSwitchPort()->getName();
        }
        else
        {
            $this->view->switchname = '';
            $this->view->portname   = '';
        }
    
        $this->view->periods      = IXP_Mrtg::$PERIODS;
    
        $stats = array();
        foreach( IXP_Mrtg::$PERIODS as $period )
        {
            $mrtg = new IXP_Mrtg(
                    IXP_Mrtg::getMrtgFilePath( $this->ixp->getMrtgPath() . '/members', 'LOG', $monitorindex, $category, $cust->getShortname() )
            );
    
            $stats[$period] = $mrtg->getValues( $period, $this->view->category );
        }
        $this->view->stats     = $stats;
    
        if( $this->getParam( 'mini', false ) )
        {
            Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
            $this->view->display( 'statistics/member-drilldown-mini.phtml' );
        }
    }
    
    
    public function leagueTableAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );
        
        $this->_setIXP();
        
        $this->view->metrics = $metrics = [
            'Total'   => 'data',
            'Max'     => 'max',
            'Average' => 'average'
        ];

        $metric = $this->getParam( 'metric', $metrics['Total'] );
        if( !in_array( $metric, $metrics ) )
            $metric = $metrics['Total'];
        $this->view->metric     = $metric;
        
        $day = $this->getParam( 'day', date( 'Y-m-d' ) );
        if( !Zend_Date::isDate( $day, 'Y-m-d' ) )
            $day = date( 'Y-m-d' );
        $this->view->day = $day = new \DateTime( $day );
        
        $category = $this->_setCategory();
                
        $this->view->trafficDaily = $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )->load( $day, $category, $this->ixp );
    }
    
    
    public function publicAction()
    {
        // get the available graphs
        $ixps = $this->getD2R( '\\Entities\\IXP' )->findAll();
        
        $graphs = [];
        foreach( $ixps as $ixp )
        {
            if( $ixp->getAggregateGraphName() )
            {
                $graphs[ $ixp->getShortname() ]['name']  = $ixp->getAggregateGraphName();
                $graphs[ $ixp->getShortname() ]['title'] = ( $this->multiIXP() ? $ixp->getShortname() . ' - ' : ' ' ) . 'Aggregate';
                $graphs[ $ixp->getShortname() ]['mrtg']  = $ixp->getMrtgPath();
            }
                
            foreach( $ixp->getInfrastructures() as $inf )
            {
                if( $inf->getAggregateGraphName() )
                {
                    $graphs[ $ixp->getShortname() . '_' . $inf->getId() ]['name']  = $inf->getAggregateGraphName();
                    $graphs[ $ixp->getShortname() . '_' . $inf->getId() ]['title'] = ( $this->multiIXP() ? $ixp->getShortname() . ' - ' : ' ' ) . $inf->getName();
                    $graphs[ $ixp->getShortname() . '_' . $inf->getId() ]['mrtg']  = $ixp->getMrtgPath();
                }
            }
        }
        
        if( !count( $graphs ) )
        {
            $this->addMessage(
                "Aggregate graphs have not been configured. Please see <a href=\"https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs\">this documentation</a> for instructions.",
                OSS_Message::ERROR
            );
            $this->redirect();
        }
        
        $this->view->graphs     = $graphs;
        
        $graph = $this->getParam( 'graph', array_keys( $graphs )[0] );
        if( !isset( $graphs[ $graph ] ) )
            $graph = array_keys( $graphs )[0];
        $this->view->graph      = $graph;
        
        $category = $this->_setCategory();

        $stats = array();
        foreach( IXP_Mrtg::$PERIODS as $period )
        {
            $mrtg = new IXP_Mrtg(
                $graphs[ $graph ][ 'mrtg' ] . '/ixp_peering-' . $graphs[ $graph ][ 'name' ] . '-' . $category . '.log' );
            $stats[$period] = $mrtg->getValues( $period, $category );
        }
        
        $this->view->stats      = $stats;
        $this->view->periods    = IXP_Mrtg::$PERIODS;
    }
    
    public function trunksAction()
    {
        if( !isset( $this->_options['mrtg']['trunk_graphs'] ) || !is_array( $this->_options['mrtg']['trunk_graphs'] ) || !count( $this->_options['mrtg']['trunk_graphs'] ) )
        {
            $this->addMessage(
                "Aggregate graphs have not been configured. Please see <a href=\"https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs\">this documentation</a> for instructions.",
                OSS_Message::ERROR
            );
            $this->redirect();
        }
                                                                                                
        // get the available graphs
        foreach( $this->_options['mrtg']['trunk_graphs'] as $g )
        {
            $p = explode( '::', $g );
            $ixpid         = $p[0];
            $images[]      = $p[1];
            $graphs[$p[1]] = $p[2];
        }
        $this->view->graphs  = $graphs;
        
        $graph = $this->getParam( 'trunk', $images[0] );
        if( !in_array( $graph, $images ) )
            $graph = $images[0];
        $this->view->graph   = $graph;

        // load the IXP
        $ixp = $this->loadIxpById( $ixpid );
        
        $stats = array();
        foreach( IXP_Mrtg::$PERIODS as $period )
        {
            $mrtg = new IXP_Mrtg( $ixp->getMrtgPath() . '/trunks/' . $graph . '.log' );
            $stats[$period] = $mrtg->getValues( $period, IXP_Mrtg::CATEGORY_BITS );
        }
        $this->view->stats   = $stats;
        
        $this->view->periods = IXP_Mrtg::$PERIODS;
    }
    
    public function switchesAction()
    {
        $eSwitches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getAndCache( true, \Entities\Switcher::TYPE_SWITCH );
    
        $switches = [];
        foreach( $eSwitches as $s )
        {
            // if we're not doing infrastructure aggregates, we're probably not doing swicth aggregates:
            if( $s->getInfrastructure()->getAggregateGraphName() )
            {
                $switches[ $s->getId() ][ 'name' ] = $s->getName();
                $switches[ $s->getId() ][ 'mrtg' ] = $s->getInfrastructure()->getIXP()->getMrtgPath();
            }
        }
        
        $this->view->switches = $switches;
        
        $switch = $this->getParam( 'switch', array_keys( $switches )[0] );
        if( !in_array( $switch, array_keys( $switches ) ) )
            $switch = array_keys( $switches )[0];
        $this->view->switch     = $switch;
        
        $category = $this->_setCategory();
        
        // override allowed categories as some aren't available here
        $this->view->categories = IXP_Mrtg::$CATEGORIES_AGGREGATE;
        
        $this->_setPeriod();
        
        $stats = array();
        
        foreach( IXP_Mrtg::$PERIODS as $period )
        {
            $mrtg = new IXP_Mrtg(
                $switches[ $switch ][ 'mrtg' ] . '/switches/' . 'switch-aggregate-'
                    . $switches[ $switch ][ 'name' ] . '-' . $category . '.log'
            );
    
            $stats[$period] = $mrtg->getValues( $period, $category );
        }
        $this->view->stats      = $stats;
    }
    
    /**
     * sFlow Peer to Peer statistics
     */
    public function p2pAction()
    {
        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
            $shortname = $this->getCustomer()->getShortname();
        else
            $shortname = $this->getParam( 'shortname', $this->getCustomer()->getShortname() );
    
        $this->view->cust = $cust = $this->loadCustomerByShortname( $shortname );  // redirects on failure

        $this->_setIXP( $cust );
        $this->_setInfrastructure( false );
        $category = $this->_setCategory( 'category', true );
        $period   = $this->_setPeriod();
        $proto    = $this->_setProtocol();
        $dvid     = $this->view->dvid = $this->getParam( 'dvid', false );
    
        // find the possible virtual interfaces that this customer peers with
        $vints = [];
        foreach( $cust->getVirtualInterfaces() as $vi )
        {
            $enabled = false;
            foreach( $vi->getVlanInterfaces() as $vli )
            {
                $fn = "getIpv{$proto}enabled";
                if( $vli->$fn() )
                {
                    $enabled = true;
                    break;
                }
            }
            
            if( !$enabled )
                continue;
            
            foreach( $vi->getPhysicalInterfaces() as $pi )
            {
                if( $pi->getSwitchPort()->getSwitcher()->getInfrastructure()->getId() == $this->infra->getId() )
                    $vints[ $vi->getId() ] = $vi;
            }
        }

        $this->view->vints = $vints;
        $this->view->customersWithVirtualInterfaces = false;
        
        if( count( $vints ) )
        {
            if( count( $vints ) > 1 )
            {
                $interfaces = array();
                foreach( $vints as $vi )
                    $interfaces[] = $vi->getId();
    
                $interface = $this->view->interface = $this->getParam( 'interface', $interfaces[0] );
                if( !in_array( $interface, $interfaces ) )
                    $interface = $this->view->interface = $interfaces[0];
    
                $this->view->svid = $interface;
            }
            else
                $this->view->svid = $vints[ ( array_keys( $vints )[0] ) ]->getId();
    
            // find the possible virtual interfaces that this customer peers with
            
            $pvints = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )
                ->getForInfrastructure( $this->infra, $proto );

            if( $dvid )
            {
                foreach( $pvints as $idx => $pvint )
                {
                    if( $pvint['id'] == $dvid )
                    {
                        $pvints = [ $pvint ];
                        $this->view->dcust = $pvint;
                        break;
                    }
                }
            }
            else
            {
                foreach( $pvints as $idx => $pvint )
                {
                    if( $pvint['cshortname'] == $shortname )
                        unset( $pvints[ $idx ] );
                }
            }
            
            $this->view->customersWithVirtualInterfaces = $pvints;
        }
    
        if( $dvid )
        {
            Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
            $this->view->display( 'statistics/p2p-single.phtml' );
        }
    }
    
        
    
    /**
     * Utility function to extract, validate (and default if necessary) a
     * category from request parameters.
     *
     * Sets the view variables `$category` to the chosen / defaulted category
     * and `$categories` to all available categories.
     *
     * @param string $pname The name of the parameter to extract the category from
     * @param bool $aggregate Use aggregate categories only (i.e. bits, pkts, no errs, no discs)
     * @return string The chosen / defaulted category
     */
    protected function _setCategory( $pname = 'category', $aggregate = false )
    {
        $category = $this->getParam( $pname, IXP_Mrtg::$CATEGORIES['Bits'] );
        if( !in_array( $category, $aggregate ? IXP_Mrtg::$CATEGORIES_AGGREGATE : IXP_Mrtg::$CATEGORIES ) )
            $category = IXP_Mrtg::$CATEGORIES['Bits'];
        $this->view->category   = $category;
        $this->view->categories = $aggregate ? IXP_Mrtg::$CATEGORIES_AGGREGATE : IXP_Mrtg::$CATEGORIES;
        return $category;
    }
    
    /**
     * Utility function to extract, validate (and default if necessary) a
     * period from request parameters.
     *
     * Sets the view variables `$period` to the chosen / defaulted category
     * and `$periods` to all available periods.
     *
     * @param string $pname The name of the parameter to extract the period from
     * @return string The chosen / defaulted period
     */
    protected function _setPeriod( $pname = 'period' )
    {
        $period = $this->getParam( $pname, IXP_Mrtg::$PERIODS['Day'] );
        if( !in_array( $period, IXP_Mrtg::$PERIODS ) )
            $period = IXP_Mrtg::$PERIODS['Day'];
        $this->view->period     = $period;
        $this->view->periods    = IXP_Mrtg::$PERIODS;
        return $period;
    }
    

}

