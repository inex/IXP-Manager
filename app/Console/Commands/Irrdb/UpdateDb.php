<?php

namespace IXP\Console\Commands\Irrdb;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Support\Facades\Mail;
use IXP\Console\Commands\Command;
use IXP\Mail\Alert;
use IXP\Models\Customer;

/**
  * Artisan command to update the IRRDB database
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin      <yann@islandbridgenetworks.ie>
  * @category   Irrdb
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
abstract class UpdateDb extends Command
{
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
     * @return mixed
     */
    protected function resolveCustomers( array $options ): mixed
    {
        if( $options[ 'asn' ] ) {
            $c = Customer::whereAutsys( $options[ 'asn' ] )->get();

            if( !count( $c ) ) {
                $this->error( "No customer found with ASN {$options[ 'asn' ]}" );
                exit(-1);
            }

            return $c;
        }

        if( $options[ 'id' ] ) {
            $c = Customer::whereId( $options[ 'id' ] )->get();

            if( !count( $c ) ) {
                $this->error( "No customer found with ID {$options[ 'id' ]}" );
                exit(-1);
            }

            return $c;
        }

        return Customer::currentActive( true )->get();
    }

    /**
     * Print Results
     * @param Customer $c
     * @param array $r
     * @param string $irrdbType
     *
     * @return void
     */
    protected function printResults( Customer $c, array $r, string $irrdbType = 'prefix' ): void
    {
        $this->netTime  += $r[ 'netTime' ];
        $this->dbTime   += $r[ 'dbTime' ];
        $this->procTime += $r[ 'procTime' ];

        if( $this->isVerbosityQuiet() ) {
            return;
        }

        $base = $c->abbreviatedName . ': [IPv4: '
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
                            $this->line( "        " . ( $type === 'stale' ? '-' . $e[ $irrdbType ] : '+' . $e ) . "  [IPv{$protocol}]" );
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle exceptions
     */
    protected function handleException( Customer $c, \Exception $e, string $type ): void
    {
        $this->error( "IRRDB {$type} update failed for {$c->name}/AS{$c->autsys}" );
        $this->error( $e->getMessage() );

        if( !$this->option('alert-email') ) {
            return;
        }

        if( !config('mail.alerts_recipient.address') ) {
            $this->warn( "Alert email not sent as IDENTITY_ALERTS_EMAIL .env option not set." );
            return;
        }

        Mail::to( [ [ 'name' => config( 'mail.alerts_recipient.name' ), 'email' => config( 'mail.alerts_recipient.address' ) ] ] )
            ->send( new Alert("IRRDB {$type} update failed for {$c->name}/AS{$c->autsys}", $e ) );

        $this->info( "Alert email sent to " . config( 'mail.alerts_recipient.address' ) );
    }

}