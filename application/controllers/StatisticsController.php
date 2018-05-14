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

 use IXP\Services\Grapher\Graph;

/**
 * Controller: Statistics / graphs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsController extends IXP_Controller_AuthRequiredAction
{
    use IXP_Controller_Trait_Statistics;

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
        if( config('grapher.backends.sflow.show_graphs_on_index_page') !== null ) {
            $showGraphsOption = true;
            $showGraphs       = config('grapher.backends.sflow.show_graphs_on_index_page');
        } else {
            $showGraphsOption = false;
            $showGraphs       = true;
        }

        if( $showGraphsOption && isset( $_POST['submit' ] ) ) {
            if( $_POST['submit'] == "Show Graphs" ) {
                $showGraphs = true;
                $_SESSION['controller.statistics.p2p.show_graphs'] = true;
            } else if( $_POST['submit'] == "Hide Graphs" ) {
                $showGraphs = false;
                $_SESSION['controller.statistics.p2p.show_graphs'] = false;
            } else if( isset( $_SESSION['controller.statistics.p2p.show_graphs'] ) ) {
                $showGraphs = $_SESSION['controller.statistics.p2p.show_graphs'];
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
