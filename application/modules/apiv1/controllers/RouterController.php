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
 * Controller: API V1 router controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_RouterController extends IXP_Controller_API_V1Action
{
    use IXP_Controller_Trait_Router;

    public function preDispatch()
    {
        $this->assertUserPriv( \Entities\User::AUTH_SUPERUSER );
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );

        // typically a module uses its own views/ folder - but we're sharing these
        // templates with the main / default module for the CLI actions so we'll
        // point it there instead
        $this->getView()->setScriptPath( APPLICATION_PATH . '/views' );
    }

    /**
     * @see https://github.com/inex/IXP-Manager/wiki/RIR-Objects
     * @throws Zend_Controller_Action_Exception
     */
    public function collectorConfAction()
    {
        $vlan = $this->view->vlan = $this->apiGetParamVlan();
        $quarantine = $this->apiGetParam( 'quarantine', false, false );

        // get, sanitise and verify the target name
        $target = preg_replace( '/[^\da-z_\-]/i', '', $this->apiGetParam( 'target', true ) );
        if( !$this->view->templateExists( "router-cli/collector/{$target}/index.cfg" ) )
            throw new Zend_Controller_Action_Exception( 'The specified target template does not exist', 401 );

        $this->apiLoadConfig();

        $this->view->proto = $proto = $this->apiGetParamProtocol( false );

        echo $this->generateCollectorConfiguration( $vlan, $proto, $target, $quarantine );
    }

    /**
     * Action to generate a route server configuration
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Route-Server
     */
    public function serverConfAction()
    {
        $vlan = $this->view->vlan = $this->apiGetParamVlan();

        // get, sanitise and verify the target name
        $target = preg_replace( '/[^\da-z_\-]/i', '', $this->apiGetParam( 'target', true ) );
        if( !$this->view->templateExists( "router-cli/collector/{$target}/index.cfg" ) )
            throw new Zend_Controller_Action_Exception( 'The specified target template does not exist', 401 );

        $this->apiLoadConfig();

        $this->view->proto = $proto = $this->apiGetParamProtocol( false );

        if( $proto == 6 )
            $ints = $this->sanitiseVlanInterfaces( $vlan, 6, true );
        else
        {
            $ints = $this->sanitiseVlanInterfaces( $vlan, 4, true );
            $this->view->proto = $proto = 4;
        }

        // should we limit this to one customer only?
        $lcustomer = $this->apiGetParam( 'cust', false, false );

        // should we wrap the output with the header and footer
        $wrappers = (bool)$this->apiGetParam( 'wrappers', false, true );

        // is test mode enabled?
        $this->view->testmode = (bool)$this->apiGetParam( 'testmode', false, false );

        if( !$lcustomer && $wrappers && $this->getView()->templateExists( "router-cli/server/{$target}/header.cfg" ) )
            echo $this->view->render( "router-cli/server/{$target}/header.cfg" );

        $asnsProcessed = [];
        foreach( $ints as $int )
        {
            if( $lcustomer && $int['cshortname'] != $lcustomer )
                continue;

            // $this->view->cust = $this->getD2R( '\\Entities\\Customer' )->find( $int[ 'cid' ] );
            $this->view->int           = $int;
            $this->view->prefixes      = $this->getD2R( '\\Entities\\IrrdbPrefix' )->getForCustomerAndProtocol( $int[ 'cid' ], $proto );
            $this->view->irrdbAsns     = $this->getD2R( '\\Entities\\IrrdbAsn'    )->getForCustomerAndProtocol( $int[ 'cid' ], $proto );
            $this->view->asnsProcessed = $asnsProcessed;

            // some sanity warnings
            if( $int['irrdbfilter'] && ( !count( $this->view->prefixes ) || !count( $this->view->irrdbAsns ) ) ) {
                if( !count( $this->view->prefixes ) ) {
                    $this->getLogger()->alert( sprintf( "WARNING: no prefixes found for %s/IPv%d in route server config generation",
                    $int['cname'], $proto
                    ) );
                }

                if( !count( $this->view->irrdbAsns ) ) {
                    $this->getLogger()->alert( sprintf( "WARNING: no ASNs found for %s/IPv%d in route server config generation",
                    $int['cname'], $proto
                    ) );
                }
            }
            
            echo $this->view->render( "router-cli/server/{$target}/neighbor.cfg" );
            $asnsProcessed[] = $int['autsys'];
        }

        if( !$lcustomer && $wrappers && $this->getView()->templateExists( "router-cli/server/{$target}/footer.cfg" ) )
            echo $this->view->render( "router-cli/server/{$target}/footer.cfg" );
    }

}
