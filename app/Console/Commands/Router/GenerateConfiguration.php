<?php namespace IXP\Console\Commands\Router;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Console\Commands\Command;

use IXP\Tasks\Router\ConfigurationGenerator as RouterConfigurationGenerator;

use Entities\{
    Router as RouterEntity
};

use D2EM;

 /**
  * Artisan command to generate router configurations
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   Router
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class GenerateConfiguration extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'router:generate-configuration
                        {handle : Router handle (from "Routers" admin action) to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate route server/collector/etc. configurations';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int {
        if( !( $router = D2EM::getRepository( RouterEntity::class )->findOneBy( [ 'handle' => $this->argument('handle') ] ) ) ){
            $this->error( "Unknown router handle" );
            return -1;
        }

        echo ( new RouterConfigurationGenerator( $router ) )->render();
        return 0;
    }

}
