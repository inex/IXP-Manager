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
                    $prefixes = $bgpq3->getPrefixList( $asmacro, $protocol );
                    $this->verbose( "found " . count( $prefixes ), false );
                    
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
    }

    /**
     * Update the database IrrdbPrefix table with the member's prefixes for a given protocol.
     *
     * This is transaction safe and works as follows:
     *
     * * Record the current time and use for last seen stamps
     * * For each prefix:
     *   * try to UPDATE a row of the same details stamping a last seen time
     *   * if UPDATE fails, INSERT instead
     * * delete any prefixes for this customer and protocol with a last seen before the recorded current time.
     *
     * The above is all one transaction ensuring the member's prefixes are available to any script requiring them.
     *
     * @param \Entities\Customer $cust The customer to update the prefixes of
     * @param array $prefixes An array of prefixes
     * @param int $protocol The protocol to use (4 or 6)
     * @throws Exception
     */
    private function updateCustomerPrefixes( $cust, $prefixes, $protocol )
    {
        $conn = $this->getD2EM()->getConnection();
        
        // The calling function and the IXP_BGPQ3 class does a lot of validation and error
        // checking. But the last thing we need to do is start filtering all prefixes if
        // something falls through to here. So, as a basic check, make sure we do not accept
        // an empty array of prefixes for a customer that has a lot.
        
        if( count( $prefixes ) == 0 )
        {
            // make sure the customer doesn't have a non-empty prefix set that we're about to delete
            if( $this->getD2R( '\\Entities\\IrrdbPrefix' )->getCountForCustomerAndProtocol( $cust, $protocol ) != 0 )
            {
                $msg = "IRRDB PREFIX: {$cust->getName()} has a non-zero prefix count for IPv{$protocol} in the database but "
                        . "BGPQ3 returned no prefixes. Please examine manually. No databases changes made for this customer.";
                $this->getLogger()->alert( $msg );
                echo $msg;
            }
            
            // in either case, we have nothing to do with an empty prefix list:
            return false;
        }
        
        
        $conn->beginTransaction();
        
        try
        {
            // get current time
            $stmt = $conn->query( "SELECT NOW() AS 'now'" );
            $currtime = $stmt->fetch()['now'];
            
            foreach( $prefixes as $prefix )
            {
                $update = $conn->executeUpdate(
                    "UPDATE irrdb_prefix SET last_seen = ? WHERE customer_id = ? AND prefix = ? AND protocol = ?",
                        [ $currtime, $cust->getId(), $prefix, $protocol ]
                );
                
                if( !$update )
                {
                    $conn->executeUpdate(
                        "INSERT INTO irrdb_prefix ( customer_id, prefix, protocol, last_seen, first_seen ) VALUES ( ?, ?, ?, ?, ? )",
                            [ $cust->getId(), $prefix, $protocol, $currtime, $currtime ]
                    );
                }
            }
            
            // remove any old prefixes
            $conn->executeUpdate(
                    "DELETE FROM irrdb_prefix WHERE customer_id = ? AND protocol = ? AND last_seen < ?",
                    [ $cust->getId(), $protocol, $currtime ]
            );
                
            
            $conn->commit();
        }
        catch( Exception $e )
        {
            $conn->rollback();
            throw $e;
        }
        
        return true;
    }
    
}












