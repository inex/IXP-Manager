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
 * Controller: IRRDB CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbCliController extends IXP_Controller_CliAction
{
    private $netTime  = 0.0;
    private $dbTime   = 0.0;
    private $procTime = 0.0;

    /**
     * Update the IrrdbPrefix table with the members' registered route: and route6: IRRDB
     * objects. These are used for filtering in the route server configuration generator.
     */
    public function updatePrefixDbAction()
    {
        $customers = $this->getD2R( '\\Entities\\Customer' )->getCurrentActive( false, true );
        $bgpq3 = new IXP_BGPQ3( $this->_options['irrdb']['bgpq']['path'] );
        
        foreach( $customers as $c )
        {
            if( !$c->isRouteServerClient() )
                continue;
            
            $this->verbose( "Processing {$c->getAbbreviatedName()}: ", false );
            
            //$bgpq3->setWhois( $c->getIRRDB()->getHost() );
            $bgpq3->setSources( $c->getIRRDB()->getSource() );
            
            foreach( [ 4, 6 ] as $protocol )
            {
                $asmacro = $c->resolveAsMacro( $protocol, 'as' );
                
                $this->verbose( "[IPv{$protocol}: ", false );
                
                try
                {
                    $timing = microtime( true );
                    $prefixes = $bgpq3->getPrefixList( $asmacro, $protocol );
                    $this->netTime += ( microtime( true ) - $timing );
                    
                    $this->verbose( "IRRDB " . count( $prefixes ), false );
                    
                    if( $this->updateCustomerPrefixes( $c, $prefixes, $protocol ) )
                        $this->verbose( "; DB updated] ", false );
                    else
                        $this->verbose( "; DB not updated] ", false );
                }
                catch( IXP_Exception $e )
                {
                    $this->getLogger()->alert( "\nERROR executing BGPQ3 utility: {$e->getMessage()}" );
                    $this->verbose( "ERROR] ", false );
                }
                
            }
            
            $this->verbose();
        }
        
        $this->verbose( "Database time  : " . $this->dbTime );
        $this->verbose( "Processing time: " . $this->procTime );
        $this->verbose( "Network time   : " . $this->netTime );
    }

    
    /**
     * Update the database IrrdbPrefix table with the member's prefixes for a given protocol.
     *
     * This is transaction safe and works as follows ensuring the member's prefixes are available
     * to any script requiring them at any time.
     *
     * @param \Entities\Customer $cust The customer to update the prefixes of
     * @param array $prefixes An array of prefixes
     * @param int $protocol The protocol to use (4 or 6)
     * @throws Exception
     */
    private function updateCustomerPrefixes( $cust, $prefixes, $protocol )
    {
        $conn = $this->getD2EM()->getConnection();
        $timing = microtime( true );
        $dbPrefixes = $this->getD2R( '\\Entities\\IrrdbPrefix' )->getForCustomerAndProtocol( $cust, $protocol );
        $this->dbTime += ( microtime( true ) - $timing );
        
        // The calling function and the IXP_BGPQ3 class does a lot of validation and error
        // checking. But the last thing we need to do is start filtering all prefixes if
        // something falls through to here. So, as a basic check, make sure we do not accept
        // an empty array of prefixes for a customer that has a lot.
        
        if( count( $prefixes ) == 0 )
        {
            // make sure the customer doesn't have a non-empty prefix set that we're about to delete
            if( count( $dbPrefixes ) != 0 )
            {
                $msg = "IRRDB PREFIX: {$cust->getName()} has a non-zero prefix count for IPv{$protocol} in the database but "
                        . "BGPQ3 returned no prefixes. Please examine manually. No databases changes made for this customer.";
                $this->getLogger()->alert( $msg );
                echo $msg;
            }
            
            // in either case, we have nothing to do with an empty prefix list:
            return false;
        }
        
        $timing = microtime( true );
        
        foreach( $dbPrefixes as $i => $p )
        {
            if( ( $j = array_search( $p['prefix'], $prefixes ) ) !== false )
            {
                // prefix exists in both db and IRRDB - no action required
                unset( $dbPrefixes[ $i ] );
                unset( $prefixes[ $j ] );
            }
        }
        
        // at this stage, the arrays are now:
        // $dbPrefixes => prefixes in the database that need to be deleted
        // $prefixes   => new prefixes that need to be added
        
        $this->verbose( "; " . count( $dbPrefixes ) . " stale", false );
        $this->verbose( "; " . count( $prefixes ) . " new", false );
        
        // validate any remaining IRRDB prefixes before we put them near the database
        $prefixes = $this->validatePrefixes( $prefixes, $protocol );
        
        $this->procTime += ( microtime( true ) - $timing );
        
        $timing = microtime( true );
        $conn->beginTransaction();
        
        try
        {
            foreach( $prefixes as $prefix )
            {
                $this->debug( "INSERT: {$cust->getShortname()} IPv{$protocol} {$prefix}", false );
                $now = date( 'Y-m-d H:i:s' );
                $conn->executeUpdate(
                    "INSERT INTO irrdb_prefix ( customer_id, prefix, protocol, last_seen, first_seen ) VALUES ( ?, ?, ?, ?, ? )",
                    [ $cust->getId(), $prefix, $protocol, $now, $now ]
                );
                $this->debug( ' - DONE.' );
            }
            
            foreach( $dbPrefixes as $prefix )
            {
                $this->debug( "DELETE: {$cust->getShortname()} IPv{$protocol} {$prefix['prefix']}", false );
                $conn->executeUpdate(
                    "DELETE FROM irrdb_prefix WHERE customer_id = ? AND protocol = ? AND prefix = ?",
                    [ $cust->getId(), $protocol, $prefix['prefix'] ]
                );
                $this->debug( ' - DONE.' );
            }
            
            $conn->executeUpdate(
                "UPDATE irrdb_prefix SET last_seen = ? WHERE customer_id = ? AND protocol = ?",
                [ date( 'Y-m-d H:i:s' ), $cust->getId(), $protocol ]
            );
                
            $conn->commit();
            
            $this->dbTime += ( microtime( true ) - $timing );
        }
        catch( Exception $e )
        {
            $conn->rollback();
            $this->dbTime += ( microtime( true ) - $timing );
            throw $e;
        }
        
        return true;
    }
    
    /**
     * Validate a given array of CIDR formatted prefixes for the given protocol and
     * remove (and alert on) any elements failing validation.
     *
     * @param array $prefixes Prefixes in CIDR notation
     * @param int $protocol Either 4/6
     * @return array Valid prefixes
     */
    private function validatePrefixes( $prefixes, $protocol )
    {
        $fn = "OSS_Validate_OSSIPv{$protocol}Cidr";
        $validator = new $fn();
        
        foreach( $prefixes as $i => $p )
        {
            if( !$validator->isValid( $p ) )
            {
                unset( $prefixes[$i] );
                $this->getLogger()->alert( 'IRRDB CLI action - removing invalid prefix ' . $p . ' from IRRDB result set!' );
            }
        }
        
        return $prefixes;
    }
}












