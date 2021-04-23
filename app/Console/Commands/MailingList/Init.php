<?php

namespace IXP\Console\Commands\MailingList;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Ds\Set;

use IXP\Utils\MailingList as ML;

 /**
  * Artisan command to export subscribers to a mailing list
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   MailingList
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class Init extends MailingList
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing-list:init
                        {list : Handle of the mailing list}
                        {--format=text : Response format - either text (default) or json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialise a mailing list - see documentation';


    /**
     * Mailing list initialisation script
     *
     * First sets a user preference for ALL users *WITHOUT* a mailing list sub for this list to unsub'd.
     *
     * Then takes a list of *existing* mailing list addresses from stdin and:
     *   - is a user does not exist with same email, skips
     *   - if a user does exist with same email, sets his mailing list preference
     *
     * NB: This function is NON-DESTRUCTIVE. It will *NOT* affect any users with *EXISTING* settings
     * but set those without a setting to on / off as appropriate.
     *
     * @return mixed
     *
     * @throws
     */
    public function handle(): int
    {
        if( !config( 'mailinglists.enabled' ) ) {
            die( "Mailing list functionality is disabled. See: http://docs.ixpmanager.org/features/mailing-lists/\n" );
        }

        $ml = new ML( $this->argument('list') );

        $stdin = fopen( "php://stdin","r" );
        $addresses = collect();

        while( $address = strtolower( trim( fgets( $stdin ) ) ) ) {
            if( !$addresses->contains( $address ) ) {
                $addresses->add( $address );
            }
        }

        fclose( $stdin );

        $this->info( "Setting mailing list subscription for all users without a pre-existing subscription setting...\n" );

        $result = $ml->init( $addresses );

        if( $this->isVerbosityVerbose() || $this->option('format') === 'json' ) {
            if( $this->option( 'format' ) === 'json' ) {
                echo json_encode( $result, JSON_THROW_ON_ERROR );
            } else {
                foreach( $result as $k => $v ) {
                    foreach( $v as $e ) {
                        $this->info( "{$k}: {$e}" );
                    }
                }
            }
        }

        echo "\n";
        return 0;
    }
}