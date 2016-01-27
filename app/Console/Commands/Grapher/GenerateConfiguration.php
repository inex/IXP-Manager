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
                        {--B|backend= : Which graphing backend to use}';

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
    public function handle()
    {
        if( !$this->getGrapher()->isConfigurationRequired() ) {
            $this->info("This grapher backend (" . $this->resolveBackend() . ") does not require any configuration to be generated");
            exit(0);
        }


    }


}
