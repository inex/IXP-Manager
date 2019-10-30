<?php namespace IXP\Console\Commands\Grapher\Backend\Mrtg;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Artisan;
use Config;
use D2EM;
use Grapher;

use IXP\Services\Grapher\Graph;

/**
 * This command line utility allows MRTG graphing to be upgraded
 * from IXP Manager v3 to v4 by automating the process of file
 * renaming.
 *
 * @see https://ixp-manager.readthedocs.org/en/latest/upgrade-from-v3.html
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Console\Commands
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Upgrade extends GrapherCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:backend:mrtg:upgrade
                                {operation : One of ln/rm-new/rm-old/mv/cp/mkdir (or: all)}
                                {--L|logdir= : MRTG log/rrd directory}
                                {--X|ixp : Show upgrade commands for the IXP}
                                {--I|infrastructures : Show upgrade commands for infrastructures}
                                {--S|switches : Show upgrade commands for switches}
                                {--T|trunks : Show upgrade commands for trunks}
                                {--M|memberdirs : Show upgrade commands for member directories}
                                {--P|physints : Show upgrade commands for member physical interfaces}
                                {--Q|memberlags : Show upgrade commands for member LAG interfaces}
                                {--C|customeragg : Show upgrade commands for member aggregate graphs}
                                {--B|corebundles : Show upgrade commands for core bundle graphs}
                                {--agg-name= : Name of aggregate graphs for IXP and infrastructure migration}';

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

        // evaluate path for old mrtg files:
        if( $this->option( 'logdir' ) ) {
            $this->logdir = $this->option( 'logdir' );
        } else {
            $this->logdir = config('grapher.backends.mrtg.workdir');
        }

        //  evaluate path for new mrtg files:
        // this is just temporary / in memory for this process:
        Config::set('grapher.backends.mrtg.logdir', Config::get('grapher.backends.mrtg.workdir' ) );

        // what should we do?
        if( $this->argument('operation') == 'all' ) {
            $this->all();
        } else if( $this->option( 'ixp' ) ) {
            $this->ix();
        } else if( $this->option( 'infrastructures' ) ) {
            $this->infrastructures();
        } else if( $this->option( 'switches' ) ) {
            $this->switches();
        } else if( $this->option( 'trunks' ) ) {
            $this->trunks();
        } else if( $this->option( 'memberdirs' ) ) {
            $this->memberdirs();
        } else if( $this->option( 'physints' ) ) {
            $this->physints();
        } else if( $this->option( 'memberlags' ) ) {
            $this->memberlags();
        } else if( $this->option( 'customeragg' ) ) {
            $this->customeragg();
        } else if( $this->option( 'corebundles' ) ) {
            $this->coreBundles();
        }

        return 0;
    }

    /**
     * Generate the command line from given parameters
     *
     * @param string $old Old file
     * @param string $new New file
     * @return string The command line
     */
    private function cmd( string $old, string $new ): string {
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

            case 'cp':
                return sprintf( "cp %s    \t%s\n", $old, $new );
                break;

            case 'mv':
                return sprintf( "mv %s    \t%s\n", $old, $new );
                break;

            case 'mkdir':
                if( !$this->option( 'memberdirs' ) ) {
                    $this->error("Invalid operation for graph type");
                    exit(-1);
                }
                return sprintf( "mkdir -p %s\n", $new );
                break;

            default:
                $this->error( "Invalid command" );
                exit(-1);
        }
    }

    /**
     * Generate commands for IXP graphs
     */
    private function ix() {
        $i = $this->ixp();

        // need to convert between the old name and the new name
        $graph = Grapher::ixp( $i );

        // aggregate graph name from v3
        $aggname = $this->option( 'agg-name' ) ? $this->option( 'agg-name' ) : 'XXXXXX';

        // parent dir:
        echo "mkdir -p " . Config::get('grapher.backends.mrtg.workdir' ) . "/ixp\n";

        foreach( Graph::CATEGORIES_BITS_PKTS as $c ) {
            $graph->setCategory( $c );
            echo $this->cmd(
                "{$this->logdir}/ixp_peering-{$aggname}-{$c}.log",
                $this->mrtg->resolveFilePath( $graph, 'log' )
            );

            foreach( Graph::PERIODS as $p ) {
                $graph->setPeriod( $p );
                echo $this->cmd(
                    "{$this->logdir}/ixp_peering-{$aggname}-{$c}-{$p}.png",
                    $this->mrtg->resolveFilePath( $graph, 'png' )
                );
            }

        }
    }

    /**
     * Generate commands for infrastructure graphs
     */
    private function infrastructures() {
        foreach( d2r('Infrastructure')->findAll() as $i ) {
            // need to convert between the old name and the new name
            $graph = Grapher::infrastructure( $i );

            // aggregate graph name from v3
            $aggname = $this->option( 'agg-name' ) ? $this->option( 'agg-name' ) : 'XXXXXX';

            // parent dir:
            echo "mkdir -p " . dirname( $this->mrtg->resolveFilePath( $graph, 'log' ) ) . "\n";

            foreach( Graph::CATEGORIES_BITS_PKTS as $c ) {
                $graph->setCategory( $c );
                echo $this->cmd(
                    "{$this->logdir}/ixp_peering-{$aggname}-{$c}.log",
                    $this->mrtg->resolveFilePath( $graph, 'log' )
                );

                foreach( Graph::PERIODS as $p ) {
                    $graph->setPeriod( $p );
                    echo $this->cmd(
                        "{$this->logdir}/ixp_peering-{$aggname}-{$c}-{$p}.png",
                        $this->mrtg->resolveFilePath( $graph, 'png' )
                    );
                }

            }
        }
    }

    /**
     * Generate commands for trunk directories graphs
     */
    private function trunks() {
        echo $this->cmd(
            "{$this->logdir}/trunks",
            config('grapher.backends.mrtg.workdir')
        );
    }

    /**
     * Generate commands for switch graphs
     */
    private function switches() {
        foreach( d2r('Infrastructure')->findAll() as $i ) {
            foreach( $i->getSwitchers( true ) as $s ) {
                // need to convert between the old name and the new name
                $graph = Grapher::switch( $s );

                // parent dir:
                echo "mkdir -p " . dirname( $this->mrtg->resolveFilePath( $graph, 'log' ) ) . "\n";

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

    /**
     * Generate commands for member directories graphs
     */
    private function memberdirs() {
        $pps = $this->mrtg->getPeeringPorts( $this->ixp() );

        foreach( $pps['custs'] as $id => $cust ) {

            $graph = Grapher::customer( $cust );

            $root = dirname( $this->mrtg->resolveFilePath( $graph, 'log' ) );

            echo $this->cmd(
                "{$this->logdir}/members/{$cust->getShortname()}",
                 $root . "/ints"
            );

            echo $this->cmd(
                "{$this->logdir}/members/{$cust->getShortname()}",
                 $root . "/lags"
            );
        }
    }

    /**
     * Generate commands for physical interfaces graphs
     */
    private function physints() {
        $pps = $this->mrtg->getPeeringPorts( $this->ixp() );

        foreach( $pps['custports'] as $cid => $custpis ) {
            foreach( $custpis as $piid ) {
                $graph = Grapher::physint( $pps['pis'][$piid] );

                foreach( Graph::CATEGORIES as $c ) {
                    if( $c == Graph::CATEGORY_BROADCASTS ) {
                        continue;
                    }
                    $graph->setCategory( $c );
                    echo $this->cmd(
                        "{$this->logdir}/members/{$pps['custs'][$cid]->getShortname()}/"
                            . "{$pps['custs'][$cid]->getShortname()}-{$graph->physicalInterface()->getMonitorindex()}-{$c}.log",
                        $this->mrtg->resolveFilePath( $graph, 'log' )
                    );

                    foreach( Graph::PERIODS as $p ) {
                        $graph->setPeriod( $p );
                        echo $this->cmd(
                            "{$this->logdir}/members/{$pps['custs'][$cid]->getShortname()}/"
                                . "{$pps['custs'][$cid]->getShortname()}-{$graph->physicalInterface()->getMonitorindex()}-{$c}-{$p}.png",
                            $this->mrtg->resolveFilePath( $graph, 'png' )
                        );
                    }
                }
            }
        }
    }

    /**
     * Generate commands for member LAG graphs
     */
    private function memberlags() {
        $pps = $this->mrtg->getPeeringPorts( $this->ixp() );

        foreach( $pps['custlags'] as $cid => $lags ) {

            foreach( $lags as $viid => $pis ) {
                if( !isset( $pis[0] ) ) {
                    continue;
                }

                $vi = $pps['pis'][$pis[0]]->getVirtualInterface();
                $graph = Grapher::virtint( $vi );

                foreach( Graph::CATEGORIES as $c ) {
                    if( $c == Graph::CATEGORY_BROADCASTS ) {
                        continue;
                    }

                    $graph->setCategory( $c );
                    echo $this->cmd(
                        "{$this->logdir}/members/{$pps['custs'][$cid]->getShortname()}/"
                            . "{$pps['custs'][$cid]->getShortname()}-lag-viid-{$vi->getId()}-{$c}.log",
                        $this->mrtg->resolveFilePath( $graph, 'log' )
                    );

                    foreach( Graph::PERIODS as $p ) {
                        $graph->setPeriod( $p );
                        echo $this->cmd(
                            "{$this->logdir}/members/{$pps['custs'][$cid]->getShortname()}/"
                                . "{$pps['custs'][$cid]->getShortname()}-lag-viid-{$vi->getId()}-{$c}-{$p}.png",
                            $this->mrtg->resolveFilePath( $graph, 'png' )
                        );
                    }

                }
            }
        }
    }

    /**
     * Generate commands for customer aggregate graphs
     */
    private function customeragg() {
        $pps = $this->mrtg->getPeeringPorts( $this->ixp() );

        foreach( $pps['custs'] as $cid => $c ) {

            $graph = Grapher::customer( $c );

            foreach( Graph::CATEGORIES as $c ) {
                if( $c == Graph::CATEGORY_BROADCASTS ) {
                    continue;
                }

                $graph->setCategory( $c );
                echo $this->cmd(
                    "{$this->logdir}/members/{$pps['custs'][$cid]->getShortname()}/"
                        . "{$pps['custs'][$cid]->getShortname()}-aggregate-{$c}.log",
                    $this->mrtg->resolveFilePath( $graph, 'log' )
                );

                foreach( Graph::PERIODS as $p ) {
                    $graph->setPeriod( $p );
                    echo $this->cmd(
                        "{$this->logdir}/members/{$pps['custs'][$cid]->getShortname()}/"
                            . "{$pps['custs'][$cid]->getShortname()}-aggregate-{$c}-{$p}.png",
                        $this->mrtg->resolveFilePath( $graph, 'png' )
                    );
                }

            }
        }
    }

    /**
     * Generate commands for core bundle graphs
     */
    private function coreBundles() {
        if( !config( 'grapher_trunks', [] ) ) {
            $this->error('No grapher trunks defined and so no migration required.');
            return;
        }

        foreach( config( 'grapher_trunks' ) as $trunk ) {
            /*
             * Sample trunk:
             * 'core-degkcp-tcydub1-lan1' => [
             *     'ixpid' => 1,
             *     'cbid' => 12, 'side' => 'b',
             *     'name'  => 'core-degkcp-tcydub1-lan1',
             *     'title' => 'Equinix DB2 (KCP) to Equinix DB1 (CWT) (LAN1 / Primary)'
             * ],
             */

            if( !( $cb = d2r('CoreBundle')->find( $trunk['cbid'] ) ) ) {
                $this->error( 'No core bundle entity for trunk ' . $trunk['name'] );
                continue;
            }

            $graph = Grapher::coreBundle( $cb );

            // dir:
            echo "mkdir -p " . dirname( $this->mrtg->resolveFilePath( $graph, 'log' ) ) . "\n";

            $graph->setCategory( Graph::CATEGORY_BITS );
            echo $this->cmd(
                "{$this->logdir}/trunks/{$trunk['name']}.log",
                $this->mrtg->resolveFilePath( $graph, 'log' )
            );

            foreach( Graph::PERIODS as $p ) {
                $graph->setPeriod( $p );
                echo $this->cmd(
                    "{$this->logdir}/trunks/{$trunk['name']}-{$p}.png",
                    $this->mrtg->resolveFilePath( $graph, 'png' )
                );
            }

        }

    }



    /**
     * Do all the migrations at once as you would if upgrading from <=4.1 to 4.2
     */
    private function all() {
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mv',    '--logdir' => $this->logdir, '--ixp' => true ] );
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mv',    '--logdir' => $this->logdir, '--infrastructures' => true ] );
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mv',    '--logdir' => $this->logdir, '--switches' => true ] );
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mv',    '--logdir' => $this->logdir, '--trunks' => true ] );
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mkdir', '--logdir' => $this->logdir, '--memberdirs' => true ] );
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mv',    '--logdir' => $this->logdir, '--physints' => true ] );
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mv',    '--logdir' => $this->logdir, '--memberlags' => true ] );
        Artisan::call( 'grapher:backend:mrtg:upgrade', [ 'operation' => 'mv',    '--logdir' => $this->logdir, '--customeragg' => true ] );
    }

}
