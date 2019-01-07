<?php namespace IXP\Console\Commands\Irrdb;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM;
use Entities\Customer;
use IXP\Console\Commands\Command;

 /**
  * Artisan command to update the IRRDB database
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   Irrdb
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
abstract class UpdateDb extends Command {

    protected $netTime  = 0.0;
    protected $dbTime   = 0.0;
    protected $procTime = 0.0;

    /**
     * Setup checks
     */
    protected function setupChecks(): bool
    {
        if( !extension_loaded("ds") ) {
            $this->warn( "The PHP Data Structure Ds\Set extension is not loaded/available. Falling back to polyfill which, in extreme cases, can take ~3 hours. Install the php-ds extension!" );
        }

        return true;
    }

    /**
     * Returns all customers or, if specified on the command line, a specific customer
     *
     * @return array Customer entities
     */
    protected function resolveCustomers(): array {
        $custarg = $this->argument('customer');

        // if not customer specific, return all appropriate ones:
        if( !$custarg ) {
            return D2EM::getRepository( 'Entities\Customer' )->getCurrentActive( false, true );
        }

        // assume ASN first:
        if( is_numeric( $custarg ) && ( $c = D2EM::getRepository( 'Entities\Customer')->findBy( [ 'autsys' => $custarg ] ) ) ) {
            return $c;
        }

        // then ID:
        if( is_numeric( $custarg ) && ( $c = D2EM::getRepository( 'Entities\Customer')->find( $custarg ) ) ) {
            return [ $c ];
        }

        if( $c = d2r('Customer')->findBy( ['shortname' => $custarg] ) ) {
            return $c;
        }

        $this->error( "Could not find a customer matching id/shortname: " . $custarg );

        exit(-1);
    }

    public function printResults( Customer $c, array $r, $irrdbType = 'prefix' )
    {
        $this->netTime  += $r[ 'netTime' ];
        $this->dbTime   += $r[ 'dbTime' ];
        $this->procTime += $r[ 'procTime' ];

        if( $this->isVerbosityQuiet() ) {
            return;
        }

        $base = $c->getAbbreviatedName() . ': [IPv4: '
            . $r[ 'v4' ][ 'count' ] . ' total; ' . count( $r[ 'v4' ][ 'stale' ] ) . ' stale; ' . count( $r[ 'v4' ][ 'new' ] )
            . ' new; DB ' . ( $r[ 'v4' ][ 'dbUpdated' ] ? 'updated' : 'not updated' ) . '] [IPv6: '
            . $r[ 'v6' ][ 'count' ] . ' total; ' . count( $r[ 'v6' ][ 'stale' ] ) . ' stale; ' . count( $r[ 'v6' ][ 'new' ] )
            . ' new; DB ' . ( $r[ 'v6' ][ 'dbUpdated' ] ? 'updated' : 'not updated' ) . ']';

        $this->info( $base );

        if( $r[ 'msg' ] ) {
            $this->comment( "    " . $r[ 'msg' ] );
        }

        if( $this->isVerbosityVeryVerbose() ) {
            $this->info( "    Time for net/database/processing: "
                . sprintf( "%0.6f/",       $r[ 'netTime' ] )
                . sprintf( "%0.6f/",       $r[ 'dbTime' ] )
                . sprintf( "%0.6f (secs)", $r[ 'procTime' ] )
            );
        }

        if( $this->isVerbosityDebug() ) {
            foreach( [ 4, 6 ] as $protocol ) {
                foreach( [ 'stale', 'new' ] as $type ) {
                    if( count( $r[ 'v' . $protocol ][ $type ] ) ) {
                        foreach( $r[ 'v' . $protocol ][ $type ] as $e ) {
                            $this->line( "        " . ( $type == 'stale' ? '-' . $e[$irrdbType] : '+' . $e ) . "  [IPv{$protocol}]" );
                        }
                    }
                }
            }
        }
    }
}
