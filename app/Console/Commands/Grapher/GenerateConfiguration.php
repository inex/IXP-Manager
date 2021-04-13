<?php

namespace IXP\Console\Commands\Grapher;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Grapher;

use IXP\Contracts\Grapher\Backend as GrapherBackend;
 /**
  * Artisan command to generate configuration for graphing
  *
  * ** Grapher Implementation Dependent! **
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin      <yann@islandbridgenetworks.ie>
  * @category   Grapher
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class GenerateConfiguration extends GrapherCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grapher:generate-configuration
                        {--B|backend= : Which graphing backend to use}
                        {--O|output=- : Save configuration to specified file (default: stdout)}';

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
        $grapher = Grapher::backend( $this->option( 'backend' ) );

        if( !$grapher->isConfigurationRequired() ) {
            $this->info("This grapher backend (" . $grapher->name() . ") does not require any configuration to be generated");
            return 100;
        }

        if( ( $retval = $this->verifyArgsAndOptions($grapher) ) !== 0 )
            return $retval;

        // backend and options are now valid
        // let's generate the configuration
        return $this->outputConfiguration( $grapher->generateConfiguration( GrapherBackend::GENERATED_CONFIG_TYPE_MONOLITHIC )[0] );
    }

    /**
     * Output the configuration in the requested format
     *
     * @param string $conf The Configuration
     *
     * @return int Suggested status code for script exit (0 == success)
     */
    protected function outputConfiguration( $conf ): int
    {
        if( $this->option('output') === '-' ) {
            echo $conf;
            return 0;
        }

        if( !@file_put_contents( $this->option('output'), $conf ) ) {
            $this->error( "Could not save configuration to the specified file [{$this->option('output')}]" );
            return -2;
        }

        return 0;
    }

    /**
     * Check the various arguments and options that have been password to the console command
     *
     * @param GrapherBackend $grapher
     *
     * @return int 0 for success or else an error code
     */
    protected function verifyArgsAndOptions( GrapherBackend $grapher ): int
    {
        $fn = $this->option('output');

        if( !$grapher->isMonolithicConfigurationSupported() ) {
            $this->error( "This backend ({$grapher->name()}) does not support single configuration files" );
            return 251;
        }

        if( $fn === '-' ) return 0;

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

        // all good :-D
        return 0;
    }
}