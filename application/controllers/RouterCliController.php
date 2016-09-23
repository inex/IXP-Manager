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
 * Controller: Router CLI Actions (such as collectors and servers)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterCliController extends IXP_Controller_CliAction
{
    use IXP_Controller_Trait_Router;

    /**
     * Action to generate a route collector configuration
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Route-Collector
     */
    public function genCollectorConfAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();
        $quarantine = $this->cliResolveParam( 'quarantine', false, false );

        $target = $this->cliResolveTarget(
            isset( $this->_options['router']['collector']['conf']['target'] )
                ? $this->_options['router']['collector']['conf']['target']
                : false
        );

        $this->cliLoadConfig();

        $this->collectorConfSanityCheck( $vlan );

        $this->view->proto = $proto = $this->cliResolveProtocol( false );

        $conf = $this->generateCollectorConfiguration( $vlan, $proto, $target, $quarantine );

        if( isset( $this->_options['router']['collector']['conf']['dstpath'] ) )
        {
            if( !$this->writeConfig( $this->_options['router']['collector']['conf']['dstpath'] . "/rc-{$vlan->getId()}.conf", $conf ) )
                fwrite( STDERR, "Error: could not save configuration data\n" );
        }
        else
            echo $conf;
    }


    /**
     * Action to generate a route server configuration
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Route-Server
     */
    public function genServerConfAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();

        $target = $this->cliResolveTarget(
                isset( $this->_options['router']['collector']['conf']['target'] )
                ? $this->_options['router']['collector']['conf']['target']
                : false
        );

        $this->view->proto = $proto = $this->cliResolveProtocol( false );

        if( $proto == 6 )
            $ints = $this->sanitiseVlanInterfaces( $vlan, 6, true );
        else
        {
            $ints = $this->sanitiseVlanInterfaces( $vlan, 4, true );
            $this->view->proto = $proto = 4;
        }

        // should we limit this to one customer only?
        $lcustomer = $this->cliResolveParam( 'cust', false, false );

        // should we wrap the output with the header and footer
        $wrappers = (bool)$this->cliResolveParam( 'wrappers', false, true );

        // is test mode enabled?
        $this->view->testmode = (bool)$this->cliResolveParam( 'testmode', false, false );

        // load Smarty config file
        $this->getView()->configLoad( $this->loadConfig() );

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

    /**
     * Action to generate test route server client configurations
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Route-Server-Testing
     */
    public function genServerTestConfsAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();

        $target = $this->cliResolveTarget(
                isset( $this->_options['router']['collector']['conf']['target'] )
                ? $this->_options['router']['collector']['conf']['target']
                : false
        );

        $this->view->proto = $proto = $this->cliResolveProtocol( false );

        if( $proto == 6 )
            $ints = $this->sanitiseVlanInterfaces( $vlan, 6, true );
        else
        {
            $ints = $this->sanitiseVlanInterfaces( $vlan, 4, true );
            $this->view->proto = $proto = 4;
        }

        // prepare the test directory and its subdirectories
        $dir = $this->prepareTestDirectory();

        // should we limit this to one customer only?
        $lcustomer = $this->cliResolveParam( 'cust', false, false );

        // load Smary config file
        $this->getView()->configLoad( $this->loadConfig() );

        foreach( $ints as $int )
        {
            if( $lcustomer && $int['cshortname'] != $lcustomer )
                continue;

            $this->view->int           = $int;
            $this->view->prefixes      = $this->getD2R( '\\Entities\\IrrdbPrefix' )->getForCustomerAndProtocol( $int[ 'cid' ], $proto );
            file_put_contents( "{$dir}/confs/{$int['cshortname']}-vlanid{$vlan->getId()}-vliid{$int['vliid']}-ipv{$proto}.conf", $this->view->render( "router-cli/server-testing/{$target}.cfg" ) );
        }
    }

    /**
     * Action to generate test route server client setup commands
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Route-Server-Testing
     */
    public function genServerTestSetupAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();

        $target = $this->cliResolveTarget(
                isset( $this->_options['router']['collector']['conf']['target'] )
                ? $this->_options['router']['collector']['conf']['target']
                : false
        );

        $this->view->proto = $proto = $this->cliResolveProtocol( false );

        if( $proto == 6 )
            $ints = $this->sanitiseVlanInterfaces( $vlan, 6, true );
        else
        {
            $ints = $this->sanitiseVlanInterfaces( $vlan, 4, true );
            $this->view->proto = $proto = 4;
        }

        if( $vlan->getSubnetSize( $proto ) === false )
            throw new IXP_Exception( "Subnet size for this VLAN is not defined. See http://git.io/TkSVVw" );

        // should we limit this to one customer only?
        $lcustomer = $this->cliResolveParam( 'cust', false, false );

        // prepare the test directory and its subdirectories
        $this->view->dir = $this->prepareTestDirectory();

        // the OS to generate commands for
        $os = $this->cliResolveParam( 'os', false, 'linux' );

        // generate down rather than up commands?
        $down = $this->cliResolveParam( 'down', false, false );

        // load Smary config file
        $this->getView()->configLoad( $this->loadConfig() );

        foreach( $ints as $int )
        {
            if( $lcustomer && $int['cshortname'] != $lcustomer )
                continue;

            $this->view->int  = $int;
            if( $down )
                echo $this->view->render( "router-cli/server-testing/{$target}-{$os}-setup-down.cfg" );
            else
                echo $this->view->render( "router-cli/server-testing/{$target}-{$os}-setup-up.cfg" );
        }
    }

    /**
     * Used by the route server test generator to create and prepare a test
     * directory.
     *
     * @see genServerTestConfsAction()
     */
    private function prepareTestDirectory()
    {
        // we need a directory to spit out the files
        $dir = realpath( $this->cliResolveParam( 'dir', true ) );

        if( !$dir )
            throw new IXP_Exception( "The target directory does not exist" );
        else if( !is_dir( $dir ) )
            throw new IXP_Exception( "{$dir} exists but is not a directory" );
        else if( !is_writable( $dir ) )
            throw new IXP_Exception( "{$dir} is not writable" );

        if( !file_exists( "{$dir}/confs" ) )
        {
            if( !mkdir( "{$dir}/confs" ) )
            {
                echo "ERROR: {$dir}/confs does not exist and could not be created\n";
                return;
            }
        }

        return $dir;
    }

    /**
     * Action to generate an AS112 router configuration
     *
     * @see https://github.com/inex/IXP-Manager/wiki/AS112
     */
    public function genAs112ConfAction()
    {
        $this->view->vlan = $vlan = $this->cliResolveVlanId();

        $target = $this->cliResolveTarget(
            isset( $this->_options['router']['as112']['conf']['target'] )
                ? $this->_options['router']['as112']['conf']['target']
                : false
        );

        if( $this->getParam( 'rc', false ) )
        {
            $this->view->routeCollectors   = $vlan->getRouteCollectors( \Entities\Vlan::PROTOCOL_IPv4 );
            $this->view->routeCollectorASN = $this->getParam( 'rcasn', 65500 );
        }

        $this->view->v4ints = $this->sanitiseVlanInterfaces( $vlan, 4 );

        if( isset( $this->_options['router']['as112']['conf']['dstpath'] ) )
        {
            if( !$this->writeConfig( $this->_options['router']['as112']['conf']['dstpath'] . "/as112-{$vlan->getId()}.conf",
                    $this->view->render( "router-cli/as112/{$target}/index.cfg" ) ) )
            {
                fwrite( STDERR, "Error: could not save configuration data\n" );
            }
        }
        else
            echo $this->view->render( "router-cli/as112/{$target}/index.cfg" );
    }

    /**
     * Action to generate a TACACS+ configuration
     *
     * @see https://github.com/inex/IXP-Manager/wiki/TACACS
     */
    public function genTacacsConfAction()
    {
        $this->view->users = $this->getD2R( '\\Entities\\User' )->arrangeByType();

        $dstfile                    = $this->cliResolveParam( 'dstfile',        false );
        $target                     = $this->cliResolveParam( 'target',         true, 'tacplus' );
        $this->view->secret         = $this->cliResolveParam( 'secret',         true, 'soopersecret' );
        $this->view->accountingfile = $this->cliResolveParam( 'accountingfile', true, '/var/log/tac_plus/tac_plus.log' );

        if( $dstfile )
        {
            if( !$this->writeConfig( $dstfile, $this->view->render( "router-cli/tacacs/{$target}/index.cfg" ) ) )
                fwrite( STDERR, "Error: could not save configuration data\n" );
        }
        else
            echo $this->view->render( "router-cli/tacacs/{$target}/index.cfg" );
    }

    /**
     * This is a summy function for gen-tacacs-conf.
     *
     * @see https://github.com/inex/IXP-Manager/wiki/RADIUS
     */
    public function genRadiusConfAction()
    {
        $this->forward( 'gen-tacacs-conf' );
    }

}
