<?php namespace IXP\Console\Commands\Helpdesk;

use App;

use IXP\Console\Commands\Command as IXPCommand;

class HelpdeskCommand extends IXPCommand {


    private $helpdesk = null;

    protected function getHelpdesk() {
        if( $this->helpdesk === null ) {
            $this->helpdesk = App::make('IXP\Contracts\Helpdesk');
        }

        if( $this->helpdesk === false ) {
            $this->error( 'No helpdesk provider defined' );
            exit -1;
        }

        return $this->helpdesk;
    }

}
