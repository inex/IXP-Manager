<?php namespace IXP\Console\Commands\Grapher;

use App;

use IXP\Console\Commands\Command as IXPCommand;

class GrapherCommand extends IXPCommand {


    private $grapher = null;

    protected function getGrapher() {
        if( $this->grapher === null ) {
            $this->grapher = App::make('IXP\Contracts\Grapher');
        }

        return $this->grapher;
    }

}
