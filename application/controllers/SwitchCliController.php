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
 * Controller: Manage switches (and other devices)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
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
        // have we been given a specific switch?
        if( $this->getParam( 'switch', false ) )
        {
            if( $sw = $this->getD2R( '\\Entities\\Switcher' )->findOneBy( [ 'name' => $this->getParam( 'switch' ) ] ) )
            {
                $this->_snmpPoll( $sw );
                //$this->getD2EM()->flush();
            }
            else
                echo "ERR: No switch found with name " . $this->getParam( 'switch' ) . "\n";
        }
        else
        {
            // find all active switches
            if( $sws = $this->getD2R( '\\Entities\\Switcher' )->getActive() )
            {
                foreach( $sws as $sw )
                    $this->_snmpPoll( $sw );

                //$this->getD2EM()->flush();
            }
        }
    }


    /**
     *
     * @param \Entities\Switcher $sw
     */
    private function _snmpPoll( $sw )
    {
        if( $sw->getLastPolled() == null )
            echo "First time polling of {$sw->getName()} with SNMP request to {$sw->getHostname()}\n";
        else
            $this->verbose( "Polling {$sw->getName()} with SNMP request to {$sw->getHostname()}" );

        $formatDate = function( $d ) {
            return $d instanceof \DateTime ? $d->format( 'Y-m-d H:i:s' ) : 'Unknown';
        };

        try
        {
            $host = new \OSS_SNMP\SNMP( $sw->getHostname(), $sw->getSnmppasswd() );

            foreach( [ 'Model', 'Os', 'OsDate', 'OsVersion' ] as $p )
            {
                $fn = "get{$p}";
                $n = $host->getPlatform()->$fn();

                if( ( $p == 'OsDate' && $formatDate( $sw->$fn() ) != $formatDate( $n ) )
                    || ( $p != 'OsDate' && $sw->$fn() != $n ) )
                {
                    if( $p == 'OsDate' )
                        echo " - [{$sw->getName()}] Updating {$p} from " . $formatDate( $sw->$fn() ) . " to " . $formatDate( $n ) . "\n";
                    else
                        echo " - [{$sw->getName()}] Updating {$p} from {$sw->$fn()} to {$n}\n";
                    $fn = "set{$p}";
                    $sw->$fn( $n );
                }

                $this->_snmpPollSwitchPorts( $sw, $host );
                $sw->setLastPolled( new \DateTime() );
            }
        }
        catch( \OSS_SNMP\Exception $e )
        {
            echo "ERR: Could not poll {$sw->getName()}\n";
        }
    }

    /**
     *
     * @param \Entities\Switcher $sw
     * @param \OSS_SNMP\SNMP $host
     */
    private function _snmpPollSwitchPorts( $sw, $host )
    {
        // we'll be matching data from OSS_SNMP to the switchport database table using the following:
        $map = [
            'names'           => 'IfName',
            'aliases'         => 'IfAlias',
            'highSpeeds'      => 'IfHighspeed',
            'mtus'            => 'IfMtu',
            'physAddresses'   => 'IfPhysAddress',
            'adminStates'     => 'IfAdminStatus',
            'operationStates' => 'IfOperStatus',
            'lastChanges'     => 'IfLastChange'
        ];
        
        $existingPorts = $sw->getPorts();
        
        try
        {
            
            // we traditionally stored ports in the database by their ifDesc so we need to key
            // off that for backwards compatibility
            $ports = $host->useIface()->descriptions();

            // iterate over all the ports discovered on the switch:
            foreach( $ports as $index => $ifDesc )
            {
                // find the matching switchport in the database (or create a new one)
                foreach( $existingPorts as $ep )
                {
                    if( $ep->getName() == $ifDesc )
                    {
                        $switchport = $ep;
                        break;
                    }
                    
                    // no existing port in database so we have found a new port
                    echo " - {$sw->getName()}: found a new port - {$ifDesc}\n";
                    
                    $switchport = new \Entities\SwitchPort();
                    
                    $switchport->setSwitcher( $sw );
                    $sw->addPort( $switchport );
                    
                    $switchport->setName( $ifDesc );
                    $switchport->setType( \Entities\SwitchPort::TYPE_UNSET );
                    
                    $this->getD2EM()->persist( $switchport );
                }
                
                foreach( $map as $snmp => $entity )
                {
                    $fn = "get{$entity}";
                    $n = $host->useIface()->$fn()[ $index ];
    
                    if( $switchport->$fn() != $n )
                            echo " - [{$sw->getName()}]:{$ifDesc} Updating {$entity} from {$switchport->$fn()} to {$n}\n";
                    
                    $fn = "set{$entity}";
                    $switchport->$fn( $n );
                }
    
                $switchport->setLastPolled( new \DateTime() );
            }
        }
        catch( \OSS_SNMP\Exception $e )
        {
            echo "ERR: Could not poll {$sw->getName()}\n";
        }
    }

}

