<?php namespace IXP\Console\Commands\MailingList;

use IXP\Services\Grapher as Grapher;

use IXP\Console\Commands\Command as IXPCommand;
use IXP\Console\Commands\Command;


abstract class MailingList extends IXPCommand {

    public function __construct()
    {
        if( !config( 'mailinglists.enabled' ) ) {
            die( "Mailing list functionality is disabled. See: http://docs.ixpmanager.org/features/mailing-lists/\n" );
        }

        parent::__construct();
    }

}
