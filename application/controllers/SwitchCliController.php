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
 * Controller: Manage switches (and other devices)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchCliController extends IXP_Controller_CliAction
{
    public function init()
    {
        require_once APPLICATION_PATH . '/../library/OSS_SNMP.git/OSS_SNMP/SNMP.php';
    }

    /**
     * Poll and update switch objects via SNMP
     */
    public function snmpPollAction()
    {
        $sws = [];

        // have we been given a specific switch?
        if( $this->getParam( 'switch', false ) )
        {
            if( ( $sw = $this->getD2R( '\\Entities\\Switcher' )->findOneBy( [ 'name' => $this->getParam( 'switch' ) ] ) ) )
                $sws[] = $sw;
            else
            {
                echo "ERR: No switch found with name " . $this->getParam( 'switch' ) . "\n";
                return;
            }
        }
        else
        {
            // find all active switches
            $sws = $this->getD2R( '\\Entities\\Switcher' )->getActive();
        }

        if( count( $sws ) )
        {
            // create a logger for stdout
            if( $this->isVerbose() || $this->isDebug() )
            {
                $writer = new Zend_Log_Writer_Stream( 'php://output' );
                $logger = new OSS_Log();

                if( !$this->isDebug() );
                    $logger->addFilter( new Zend_Log_Filter_Priority( OSS_Log::INFO ) );

                $logger->addWriter( $writer );
            }
            else
                $logger = false;

            foreach( $sws as $sw )
            {
                if( trim( $sw->getSnmppasswd() ) == '' ) {
                    $this->verbose( "Skipping {$sw->getName()} as no SNMP password set" );
                    continue;
                }

                if( $sw->getLastPolled() == null )
                    echo "First time polling of {$sw->getName()} with SNMP request to {$sw->getHostname()}\n";
                else
                    $this->verbose( "Polling {$sw->getName()} with SNMP request to {$sw->getHostname()}" );

                $swPolled = false;
                try
                {
                    $swPolled = false;
                    $host = new \OSS_SNMP\SNMP( $sw->getHostname(), $sw->getSnmppasswd() );
                    $sw->snmpPoll( $host, $logger );
                    $swPolled = true;

                    if( $sw->getSwitchtype() == \Entities\Switcher::TYPE_SWITCH )
                        $sw->snmpPollSwitchPorts( $host, $logger );

                    if( $this->getParam( 'noflush', false ) )
                        $this->verbose( '*** noflush parameter set - NO CHANGES MADE TO DATABASE' );
                    else
                        $this->getD2EM()->flush();
                }
                catch( \OSS_SNMP\Exception $e )
                {
                    if( $swPolled )
                        echo "ERROR: OSS_SNMP exception polling {$sw->getName()} by SNMP\n";
                    else
                        echo "ERROR: OSS_SNMP exception polling ports for {$sw->getName()} by SNMP\n";
                }
            }
        }
    }
}
