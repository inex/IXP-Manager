<?php namespace IXP\Console\Commands\Grapher\Backend\Mrtg;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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


use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Output\OutputInterface;

use IXP\Contracts\Grapher\Backend as GrapherBackend;

use IXP\Console\Commands\Grapher\GrapherCommand;

use Config;
use D2EM;
use Grapher;

use IXP\Services\Grapher\Graph;

 /**
  * Artisan command to rename MRTG graphing files when upgrading from v3 to v4.
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class Upgrade extends GrapherCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:backend:mrtg:upgrade
                                {operation : One of ln/rm-new/rm-old/mv}
                                {--L|logdir= : MRTG log/rrd directory}
                                {--X|ixp : Show upgrade commands for the IXP}
                                {--I|infrastructures : Show upgrade commands for infrastructures}
                                {--S|switches : Show upgrade commands for switches}
                                {--M|memberdirs : Show upgrade commands for member directories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename MRTG files when upgrading from v3 to v4 - uses ln';


    private $logdir = null;
    private $mrtg   = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        $this->mrtg = Grapher::backend( 'mrtg' );

        if( $this->option( 'logdir' ) ) {
            $this->logdir = $this->option( 'logdir' );
            // this is just temporary / in memory for this process:
            Config::set('grapher.backends.mrtg.logdir', $this->logdir );
        } else {
            $this->logdir = config('grapher.backends.mrtg.logdir');
        }

        if( $this->option( 'ixp' ) ) {
            $this->ix();
        }

        if( $this->option( 'infrastructures' ) ) {
            $this->infrastructures();
        }

        if( $this->option( 'switches' ) ) {
            $this->switches();
        }

        if( $this->option( 'memberdirs' ) ) {
            $this->memberdirs();
        }

        return 0;
    }

    private function cmd( $old, $new ) {
        switch( $this->argument( 'operation' ) ) {
            case 'ln':
                return sprintf( "ln -s %s    \t%s\n", $old, $new );
                break;

            case 'rm-old':
                return sprintf( "rm %s\n", $old );
                break;

            case 'rm-new':
                return sprintf( "rm %s\n", $new );
                break;

            case 'mv':
                return sprintf( "mv %s    \t%s\n", $old, $new );
                break;

            default:
                $this->error( "Invalid command" );
                exit(-1);
        }
    }

    private function ix() {
        $i = $this->ixp();

        // need to convert between the old name and the new name
        $graph = Grapher::ixp( $i );

        foreach( Graph::CATEGORIES_BITS_PKTS as $c ) {
            $graph->setCategory( $c );
            echo $this->cmd(
                "{$this->logdir}/ixp_peering-{$i->getAggregateGraphName()}-{$c}.log",
                $this->mrtg->resolveFilePath( $graph, 'log' )
            );

            foreach( Graph::PERIODS as $p ) {
                $graph->setPeriod( $p );
                echo $this->cmd(
                    "{$this->logdir}/ixp_peering-{$i->getAggregateGraphName()}-{$c}-{$p}.png",
                    $this->mrtg->resolveFilePath( $graph, 'png' )
                );
            }

        }
    }

    private function infrastructures() {
        foreach( d2r('Infrastructure')->findAll() as $i ) {
            // need to convert between the old name and the new name
            $graph = Grapher::infrastructure( $i );

            foreach( Graph::CATEGORIES_BITS_PKTS as $c ) {
                $graph->setCategory( $c );
                echo $this->cmd(
                    "{$this->logdir}/ixp_peering-{$i->getAggregateGraphName()}-{$c}.log",
                    $this->mrtg->resolveFilePath( $graph, 'log' )
                );

                foreach( Graph::PERIODS as $p ) {
                    $graph->setPeriod( $p );
                    echo $this->cmd(
                        "{$this->logdir}/ixp_peering-{$i->getAggregateGraphName()}-{$c}-{$p}.png",
                        $this->mrtg->resolveFilePath( $graph, 'png' )
                    );
                }

            }
        }
    }

    private function switches() {
        foreach( d2r('Infrastructure')->findAll() as $i ) {
            foreach( $i->getSwitchers( \Entities\Switcher::TYPE_SWITCH, true ) as $s ) {
                // need to convert between the old name and the new name
                $graph = Grapher::switch( $s );

                foreach( Graph::CATEGORIES_BITS_PKTS as $c ) {
                    $graph->setCategory( $c );
                    echo $this->cmd(
                        "{$this->logdir}/switches/switch-aggregate-{$s->getName()}-{$c}.log",
                        $this->mrtg->resolveFilePath( $graph, 'log' )
                    );

                    foreach( Graph::PERIODS as $p ) {
                        $graph->setPeriod( $p );
                        echo $this->cmd(
                            "{$this->logdir}/switches/switch-aggregate-{$s->getName()}-{$c}-{$p}.png",
                            $this->mrtg->resolveFilePath( $graph, 'png' )
                        );
                    }

                }
            }
        }
    }

    private function memberdirs() {
        $pps = $this->mrtg->getPeeringPorts( $this->ixp() );

        foreach( $pps['custs'] as $id => $cust ) {

            $graph = Grapher::customer( $cust );

            echo $this->cmd(
                "{$this->logdir}/members/{$cust->getShortname()}",
                dirname( $this->mrtg->resolveFilePath( $graph, 'log' ) )
            );
        }
    }


}
