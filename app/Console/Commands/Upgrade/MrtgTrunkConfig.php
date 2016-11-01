<?php namespace IXP\Console\Commands\Upgrade;
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

use IXP\Console\Commands\Command as IXPCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Output\OutputInterface;

use IXP\Console\Commands\Grapher\GrapherCommand;

use Zend_Application;
use Config;
use D2EM;
use View;

/**
 * This command line utility is used for upgrading IXP Manager.
 *
 * Code will be added / removed as is relevant from version to version.
 *
 * @see https://ixp-manager.readthedocs.org/en/latest/upgrade-from-v3.html
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Console\Commands
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MrtgTrunkConfig extends IXPCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:upgrade:mrtg-trunk-config
                                {ini-file} : INI file to read existing trunk configuration from (or "help")
                                {--no-backup : Disable backups for commands that create backup file(s)}
                                {--ixp-help : Additional IXP Manager help}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade Tools - MRTG Trunk Configuration Migration from <=4.1 to v4.2';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {

        if( $this->argument('ini-file') == 'help' ) {
            $this->help();
            return 0;
        }

        // load ini file
        if( !@file_exists($this->argument('ini-file')) || !( $ini = @parse_ini_file($this->argument('ini-file'))) ) {
            $this->error("Could not load / parse INI file");
            return -1;
        }

        if( !isset($ini['mrtg.trunk_graphs']) ) {
            $this->error("Could not fine 'mrtg.trunk_graphs' section in INI file");
            return -2;
        }

        $this->migrateTrunkConfig($ini['mrtg.trunk_graphs']);
        return 0;
    }
    
    /**
     * Display a help message via Artisan
     */
    private function help() {
        echo View::make( 'console.commands.upgrade.mrtg-trunk-config.help' )->render();
    }


    /**
     * Take MRTG trunk definitiions from the old Zend configuration
     * format and migrate them to a new one (config/grapher_trunks.php).
     *
     * It will backup any existing config/grapher_trunks.php file unless the
     * option --no-backup is passed.
     *
     * @param array $trunksConf The raw trunk definitions
     */
    private function migrateTrunkConfig( array $trunksConf ) {

        // copy the current file (if it exists)
        $conffile = config_path() . "/grapher_trunks.php";
        $bkupfile = $conffile . "." . date('YmdHms');

        if( file_exists( $conffile ) && !$this->option('no-backup') ) {
            if( !@file_put_contents( $bkupfile, @file_get_contents( $conffile ) ) ) {
                $this->error( "Could not back up existing configuration: " . $conffile );
                exit -1;
            } else {
                $this->info( "Backed up existing configuration to " . $bkupfile );
            }
        }
        
        $trunks = [];
        foreach( $trunksConf as $tc ) {
            $trunks[] = explode( '::', $tc );
        }

        file_put_contents( $conffile,
            View::make( 'console.commands.upgrade.mrtg-trunk-config.grapher_trunks' )->with( [ 'trunks' => $trunks ] )->render()
        );
        $this->info( "Migrated configuration to " . $conffile );
    }


}
