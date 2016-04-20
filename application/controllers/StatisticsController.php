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

 use IXP\Services\Grapher\Graph;

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

        $this->setIXP();
        $this->setInfrastructure();

        $this->view->custs = $custs = $this->getD2R( '\\Entities\\Customer')->getConnected( false, false, $this->ixp );

        if( !is_string( $this->infra ) && $this->infra )
            $this->view->custs = $this->getD2R( '\\Entities\\Customer')->filterForInfrastructure( $custs, $this->infra );
    }

    public function membersAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );

        $this->setIXP();
        $this->setInfrastructure();
        $this->setCategory();
        $category = $this->setCategory();
        $period   = $this->setPeriod();

        $grapher = App::make('IXP\Services\Grapher');

        $custs = $this->getD2R( '\\Entities\\Customer')->getCurrentActive( false, true, false, $this->ixp );

        /**
         * FIXME - add back in infra
         *
         * if( !is_string( $this->infra ) && $this->infra )
         * $custs = $this->getD2R( '\\Entities\\Customer')->filterForInfrastructure( $custs, $this->infra );
         */

        $graphs = [];

        foreach( $custs as $c ) {
            $graphs[] = $grapher->customer( $c )
                ->setType(     Graph::TYPE_PNG )
                ->setProtocol( Graph::PROTOCOL_ALL )
                ->setCategory( $category )
                ->setPeriod( $period );
        }

        $this->view->graphs = $graphs;
    }

    public function memberAction()
    {
        $cust = $this->view->cust = $this->resolveCustomerByShortnameParam(); // includes security checks
        $this->setIXP( $cust );
        $this->setCategory();
        $this->view->grapher = App::make('IXP\Services\Grapher');
    }

    public function memberDrilldownAction()
    {
        $category = $this->setCategory();
        $this->view->monitorindex = $monitorindex = $this->getParam( 'monitorindex', 1 );
        $grapher = App::make('IXP\Services\Grapher');

        $cust = $this->view->cust = $this->resolveCustomerByShortnameParam(); // includes security checks

        $this->setIXP( $cust );

        $this->view->isLAG       = false;
        $this->view->isPort      = false;
        $this->view->isAggregate = false;

        if( is_numeric( $monitorindex ) )
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
            $this->view->isPort     = true;

            $graph = $grapher->physint( $pi );
        }
        else if( substr( $monitorindex, 0, 9 ) == 'lag-viid-' )
        {
            $viid = substr( $monitorindex, 9 );
            $vint = false;
            $pi = null;
            foreach( $cust->getVirtualInterfaces() as $vi )
            {
                if( $vi->getId() == $viid )
                {
                    $this->view->vi   = $vi;
                    $this->view->vint = $vint = $vi;
                    break;
                }
            }

            if( !$vint )
                throw new IXP_Exception( 'Member statistics drilldown requested for unknown LAG index' );

            $this->view->switchname = $vi->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getName();
            foreach( $vi->getPhysicalInterfaces() as $pi )
            $portnames[] = $pi->getSwitchPort()->getName();
            $this->view->portname = implode( ', ', $portnames );
            $this->view->isLAG = true;
            $graph = $grapher->virtint( $vi );
        }
        else
        {
            $this->view->switchname  = '';
            $this->view->portname    = '';
            $this->view->isAggregate = true;
            $this->view->graph = $graph = $grapher->customer( $cust );
        }

        $this->view->periods      = Graph::PERIOD_DESCS;
        $this->view->graph = $graph->setCategory( $category );

        if( $this->getParam( 'mini', false ) )
        {
            Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
            $this->view->display( 'statistics/member-drilldown-mini.phtml' );
        }
    }


    public function leagueTableAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER, true );

        $this->setIXP();

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

        $category = $this->setCategory();

        $this->view->trafficDaily = $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )->load( $day, $category, $this->ixp );
    }


    public function publicAction()
    {
        // get the available graphs
        $ixps = $this->getD2R( '\\Entities\\IXP' )->findAll();
        $grapher = App::make('IXP\Services\Grapher');
        $category = $this->setCategory( 'category', true );

        $graphs = [];
        foreach( $ixps as $ixp )
        {
            $graphs[] = $grapher->ixp( $ixp )
                            ->setType(     Graph::TYPE_PNG )
                            ->setProtocol( Graph::PROTOCOL_ALL )
                            ->setCategory( $category );

            foreach( $ixp->getInfrastructures() as $inf )
            {
                $graphs[] = $grapher->infrastructure( $inf )
                                ->setType(     Graph::TYPE_PNG )
                                ->setProtocol( Graph::PROTOCOL_ALL )
                                ->setCategory( $category );
            }
        }

        if( !count( $graphs ) )
        {
            $this->addMessage(
                "Aggregate graphs have not been configured. Please see <a href=\"https://ixp-manager.readthedocs.org/en/latest/features/grapher.html\">this documentation</a> for instructions.",
                OSS_Message::ERROR
            );
            $this->redirect('');
        }

        $this->view->graphs     = $graphs;

        $graphid = $this->getParam( 'graph', 0 );
        if( !isset( $graphs[ $graphid ] ) )
            $graphid = 0;

        $this->view->graphid    = $graphid;
        $this->view->graph      = $graphs[$graphid];

        $this->setPeriod();
    }

    public function trunksAction()
    {
        if( !isset( $this->_options['mrtg']['trunk_graphs'] ) || !is_array( $this->_options['mrtg']['trunk_graphs'] ) || !count( $this->_options['mrtg']['trunk_graphs'] ) )
        {
            $this->addMessage(
                "Aggregate graphs have not been configured. Please see <a href=\"https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs\">this documentation</a> for instructions.",
                OSS_Message::ERROR
            );
            $this->redirect(null);
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
        $grapher = App::make('IXP\Services\Grapher');
        $category = $this->setCategory( 'category', true );

        $switches = [];
        foreach( $eSwitches as $s ) {
            $switches[ $s->getId() ] = $grapher->switch( $s )->setType( Graph::TYPE_PNG )->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );
        }

        $this->view->switches = $switches;

        $switchid = $this->getParam( 'switch', array_keys( $switches )[0] );
        if( !in_array( $switchid, array_keys( $switches ) ) )
            $switchid = array_keys( $switches )[0];

        $this->view->switchid     = $switchid;
        $this->view->graph        = $switches[$switchid];

        $this->setPeriod();
    }

    /**
     * sFlow Peer to Peer statistics
     */
    public function p2pAction()
    {
        $cust = $this->view->cust = $this->resolveCustomerByShortnameParam(); // includes security checks

        $this->setIXP( $cust );
        $category = $this->setCategory( 'category', true );
        $period   = $this->setPeriod();
        $proto    = $this->setProtocol();

        $this->view->grapher = App::make('IXP\Services\Grapher');

        // for larger IXPs, it's quite intensive to display all the graphs - decide if we need to do this or not
        if( isset( $this->_options['sflow']['show_graphs_on_index_page'] ) ) {
            $showGraphsOption = true;
            $showGraphs       = $this->_options['sflow']['show_graphs_on_index_page'];
        } else {
            $showGraphsOption = false;
            $showGraphs       = true;
        }

        if( $showGraphsOption && isset( $_POST['submit' ] ) ) {
            if( $_POST['submit'] == "Show Graphs" ) {
                $showGraphs = true;
            } else if( $_POST['submit'] == "Hide Graphs" ) {
                $showGraphs = false;
            }
        }

        $this->view->showGraphs       = $showGraphs;
        $this->view->showGraphsOption = $showGraphsOption;

        // Find the possible VLAN interfaces that this customer has for the given IXP
        if( !count( $srcVlis = $this->view->srcVlis = $this->getD2R( '\\Entities\\VlanInterface' )->getForCustomer( $cust, $this->ixp ) ) )
        {
            $this->addMessage( 'There were no interfaces available for the given criteria. Returning to default view.' );
            $this->redirect( 'statistics/p2p' );
        }

        if( ( $svlid = $this->getParam( 'svli', false ) ) && isset( $srcVlis[ $svlid ] ) )
            $this->view->srcVli = $srcVli = $srcVlis[ $svlid ];
        else
            $this->view->srcVli = $srcVli = $srcVlis[ array_keys( $srcVlis )[0] ];

        // Now find the possible other VLAN interfaces that this customer could exchange traffic with
        // (as well as removing the source vli)
        $dstVlis = $this->getD2R( '\\Entities\\VlanInterface' )->getObjectsForVlan( $srcVli->getVlan() );
        unset( $dstVlis[ $srcVli->getId() ] );
        $this->view->dstVlis = $dstVlis;

        if( !count( $dstVlis ) )
        {
            $this->addMessage( 'There were no other interfaces available for traffic exchange for the given criteria. Returning to default view.' );
            $this->redirect( 'statistics/p2p' );
        }

        if( ( $dvlid = $this->getParam( 'dvli', false ) ) && isset( $dstVlis[ $dvlid ] ) )
            $this->view->dstVli = $dstVli = $dstVlis[ $dvlid ];
        else
            $this->view->dstVli = $dstVli = false;

        if( $dstVli )
        {
            Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
            $this->view->display( 'statistics/p2p-single.phtml' );
        }
    }
}
