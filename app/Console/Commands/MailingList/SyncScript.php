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

 /**
  * Artisan command to export subscribers to a mailing list
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin <yann@islandbridgenetworks.ie>
  * @category   IXP
  * @package    IXP\Console\Commands\MailingList
  * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class SyncScript extends MailingList
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing-list:sync-script
                            {--sh : Generate for shell commands rather than API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a sample mailing list syncronisation script';

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
     */
    public function handle(): int
    {
        if( !config( 'mailinglists.enabled' ) ) {
            die( "Mailing list functionality is disabled. See: http://docs.ixpmanager.org/features/mailing-lists/\n" );
        }
        
        // do we have mailing lists defined?
        if( !is_array( config( 'mailinglists.lists' ) ) || !count( config( 'mailinglists.lists' ) ) ) {
            $this->error( "No valid mailing lists defined in config/mailinglist.php" );
            $this->info( "See: http://docs.ixpmanager.org/features/mailing-lists/" );
            return -1;
        }

        echo view( 'console/commands/mailing-list/sync-' . ( $this->option( 'sh' ) ? 'sh' : 'apiv4' ) )
            ->with( [ 'lists' => config( 'mailinglists.lists' ) ] )
            ->render();

        return 0;
    }
}