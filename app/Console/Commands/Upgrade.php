<?php namespace IXP\Console\Commands;
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

use IXP\Console\Commands\Command as IXPCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Output\OutputInterface;

use IXP\Console\Commands\Grapher\GrapherCommand;

use Zend_Application;
use Config;
use D2EM;

/**
 * This command line utility is used for upgrading IXP Manager.
 *
 * Code will be added / removed as is relevant from version to version.
 *
 * @see https://ixp-manager.readthedocs.org/en/latest/upgrade-from-v3.html
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Console\Commands
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Upgrade extends IXPCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:upgrade
                                {--T|migrate-trunk-config : Migrate trunk config from application.ini to grapher_trunks.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tools to upgrade IXP Manager';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        // what should we do?
        if( $this->option( 'migrate-trunk-config' ) ) {
            $this->migrateTrunkConfig();
        } else {
            $this->error( 'No operation requested' );
            exit -1;
        }

        return 0;
    }

    private function zend(): Zend_Application {
        return \App::make('ZendFramwork');
    }

    /**
     * Generate commands for trunk directories graphs
     */
    private function migrateTrunkConfig() {
        // copy the current file (if it exists)
        $conffile = config_path() . "/grapher_trunks.php";
        $bkupfile = $conffile . "." . date('YmdHms');

        if( file_exists( $conffile ) ) {
            if( !@file_put_contents( $bkupfile, @file_get_contents( $conffile ) ) ) {
                $this->error( "Could not back up existing configuration: " . $conffile );
                exit -1;
            } else {
                $this->info( "Backed up existing configuration to " . $bkupfile );
            }
        }

        $this->scriptutils_get_application_env();
        $zend = $this->zend();
        dd( $zend->getOptions() );
    }


    /**
     * Parses public/.htaccess for application environment
     *
     * Only required when upgrading from v3 to v4. Should be removed in v5.
     * die()'s if not found
     */
    private function scriptutils_get_application_env()
    {
        $htaccess_path = base_path() . "/public/.htaccess";

        if( !is_readable( $htaccess_path ) ) {
            die( "ERROR: public/.htaccess does not exist / is not readable - set this up first!\n" );
        }

        $htaccess = file_get_contents( $htaccess_path );

        $matches = array();
        preg_match_all( '/SetEnv\s+APPLICATION_ENV\s+([a-zA-Z0-9_\-\.]+)/i', $htaccess, $matches );

        if( isset( $matches[1][0] ) && strlen( $matches[1][0] ) ) {
            $appenv = trim( $matches[1][0] );
            define( "APPLICATION_ENV", $appenv );
            return $appenv;
        }

        die( "ERROR: Could not parse or find APPLICATION_ENV in $htaccess_path\n" );
    }

}
