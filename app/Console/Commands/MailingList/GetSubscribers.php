<?php namespace IXP\Console\Commands\MailingList;

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

use Entities\{
    User as UserEntity
};

use IXP\Utils\MailingList as ML;

 /**
  * Artisan command to export subscribers to a mailing list
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   MailingList
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class GetSubscribers extends MailingList {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing-list:get-subscribers
                        {list : Handle of the mailing list}
                        {--format=text : Output format - one of text (default) or json}
                        {--unsubscribed : Provide a list of user emails that are unsubscribed rather than subscribed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get subscribers to the specified mailing list';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {

        if( !config( 'mailinglists.enabled' ) ) {
            die( "Mailing list functionality is disabled. See: http://docs.ixpmanager.org/features/mailing-lists/\n" );
        }

        $ml = new ML( $this->argument('list') );

        if( $this->option('format') == 'json' ) {
            echo json_encode( $ml->getSubscriberEmails( !$this->option( 'unsubscribed' ) ) );
        } else {
            echo implode( "\n", $ml->getSubscriberEmails( !$this->option( 'unsubscribed' ) ) );
        }

        echo "\n";
        return 0;
    }

}
