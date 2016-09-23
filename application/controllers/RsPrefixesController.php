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
 * Controller: List prefixes accepted (or otherwise) by the route servers
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RsPrefixesController extends IXP_Controller_AuthRequiredAction
{
    public function init()
    {
        if( isset( $this->_options['frontend']['disabled'][ $this->getRequest()->getControllerName() ] )
            && $this->_options['frontend']['disabled'][ $this->getRequest()->getControllerName() ] )
        {
            $this->addMessage( _( 'This controller has been disabled.' ), OSS_Message::ERROR );
            $this->redirectAndEnsureDie( '' );
        }
    }

    public function indexAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );

        $this->view->types         = \Entities\RSPrefix::$SUMMARY_TYPES_FNS;
        $this->view->rsRouteTypes  = array_keys( \Entities\RSPrefix::$ROUTES_TYPES_FNS );
        $this->view->cust_prefixes = $this->getD2EM()->getRepository( '\\Entities\\RSPrefix' )->aggregateRouteSummaries();
    }

    public function listAction()
    {
        $this->assertPrivilege( \Entities\User::AUTH_CUSTUSER, false );

        if( $this->getUser()->getPrivs() != \Entities\User::AUTH_SUPERUSER )
        {
            $cust = $this->getCustomer();
        }
        else
        {
            if( !( $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $this->getParam( 'custid', 0 ) ) ) )
            {
                $this->addMessage( 'Invalid customer ID in request', OSS_Message::ERROR );
                return $this->forward( 'index' );
            }
        }

        // does the customer have VLAN interfaces that filtering is disabled on?
        $totalVlanInts = 0;
        $filteredVlanInts = 0;
        foreach( $cust->getVirtualInterfaces() as $vi ) {
            foreach( $vi->getVlanInterfaces() as $vli ) {
                if( $vli->getVlan()->getPrivate() )
                    continue;

                $totalVlanInts++;
                if( $vli->getIrrdbfilter() )
                    $filteredVlanInts++;
            }
        }

        $this->view->totalVlanInts    = $totalVlanInts;
        $this->view->filteredVlanInts = $filteredVlanInts;

        $protocol = $this->getParam( 'protocol', null );
        if( !in_array( $protocol, [ 4, 6 ] ) )
            $protocol = null;

        $this->view->tab          = $this->getParam( 'tab', false );
        $this->view->cust         = $cust;
        $this->view->protocol     = $protocol;
        $this->view->rsRouteTypes = array_keys( \Entities\RSPrefix::$ROUTES_TYPES_FNS );

        $this->view->aggRoutes    = $this->getD2EM()->getRepository( '\\Entities\\RSPrefix' )->aggregateRoutes( $cust->getId(), $protocol );
    }
}
