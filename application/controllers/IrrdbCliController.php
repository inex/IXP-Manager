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
            //$bgpq3->setWhois( $c->getIRRDB()->getHost() );
            $bgpq3->setSources( $c->getIRRDB()->getSource() );
            
            foreach( [ 4, 6 ] as $protocol )
            {
                $asmacro = $c->resolveAsMacro( $protocol, 'as' );
                $prefixes = $bgpq3->getPrefixList( $asmacro, $protocol );
                $this->updateCustomerPrefixes( $c, $prefixes, $protocol );
            }
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
        
        
    }
    
}












