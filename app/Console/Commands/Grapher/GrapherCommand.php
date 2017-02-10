<?php namespace IXP\Console\Commands\Grapher;

use IXP\Services\Grapher as Grapher;

use IXP\Console\Commands\Command as IXPCommand;
use IXP\Console\Commands\Command;


class GrapherCommand extends IXPCommand {

    /**
     * @var Grapher
     */
    private $grapher;


    /**
     * @return Grapher
     */
    protected function grapher(): Grapher {
        return $this->grapher;
    }

    /**
     * @param Grapher $g
     * @return Command
     */
    protected function setGrapher( Grapher $g ): Command {
        $this->grapher = $g;
        return $this;
    }

}
