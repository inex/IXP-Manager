<?php namespace IXP\Console\Commands\Grapher;

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

use D2EM;


 /**
  * Artisan command to generate configuration for graphing
  *
  * ** Grapher Impementation Dependant! **
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class GenerateConfiguration extends GrapherCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:generate-configuration
                        {--B|backend= : Which graphing backend to use}
                        {--O|output= : Save configuration to specified file (default: stdout) [do not use with --directory]}
                        {--D|directory= : Save configuration file(s) to specified directory} [do not use with --output]';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate configuration for a graphing backend';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if( !$this->getGrapher()->isConfigurationRequired() ) {
            $this->info("This grapher backend (" . $this->resolveBackend() . ") does not require any configuration to be generated");
            return 100;
        }

        if( ( $retval = $this->verifyArgsAndOptions() ) !== 0 )
            return $retval;

        return 0;
    }


    /**
     * Check the various arguments and options that have been password to the console command
     * @return int 0 for success or else an error code
     */
    protected function verifyArgsAndOptions(): int {
        if( $this->option('output') && $this->option('directory') ) {
            $this->error( 'Options --output and --directory are mutually exclusive' );
            return 254;
        }

        if( $this->option('output') ) {
            $fn = $this->option('output');

            // does it exist but is not writable?
            if( is_file($fn)  && !is_writable($fn) ) {
                $this->error( "The output file exists but is not writable" );
                return 253;
            }

            // can we write to the directory?
            if( !( dirname($fn) && is_dir( dirname($fn) ) && is_writable( dirname($fn) ) ) ) {
                $this->error( "The output file does not exists and cannot be created" );
                return 252;
            }

            if( !$this->getGrapher()->isMonolithicConfigurationSupported() ) {
                $this->error( "This backend ({$this->resolveBackend()}) does not support single configuration files" );
                return 251;
            }
        }

        if( $this->option('directory') ) {
            $fn = $this->option('directory');

            // can we write to the directory?
            if( !( is_dir($fn) && is_writable($fn) ) ) {
                $this->error( "The output directory does not exist or is not writable" );
                return 248;
            }

            if( !$this->getGrapher()->isMultiFileConfigurationSupported() ) {
                $this->error( "This backend ({$this->resolveBackend()}) does not support multi-file configuration" );
                return 247;
            }
        }

        // all good :-D
        return 0;
    }

}
