<?php namespace IXP\Console\Commands\Utils\Export\JsonSchema;

use App;
use IXP\Console\Commands\Command as IXPCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use GuzzleHttp\Client;

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

 /**
  * Artisan command to POST the JSON Export to a given endpoint
  *
  * @author     Barry O'Donovan <barry@opensolutions.ie>
  * @category   Utils
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class Post extends IXPCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'utils:json-schema-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'POST the JSON Schema export to IXF / Euro-IX ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if( $this->getOutput()->isVerbose() ) $this->info( "{$this->name} :: Generating schema..." );

        $exporter = new \IXP\Utils\Export\JsonSchema();
        $json = $exporter->get( $this->option( 'ver' ) );

        try {
            if( $this->getOutput()->isVerbose() ) $this->info( "{$this->name} :: Posting schema..." );
            $client = new Client([ 'base_uri' => $this->argument( 'url' ), 'timeout'  => 60.0, ]);
            $response = $client->post( $this->argument( 'url' ), [ 'body' => $json ] );
        } catch( \Exception $e ) {
            $this->error( 'Could not post data: ' . $e->getMessage() );
            return -1;
        }

        if( $this->getOutput()->isVerbose() ) $this->info( "{$this->name} :: Schema posted." );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [ 'url', InputArgument::REQUIRED, 'The endpoint to POST the schema to' ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [ 'ver', null, InputOption::VALUE_OPTIONAL,
                'Schema version to export (defualt: v' . \IXP\Utils\Export\JsonSchema::EUROIX_JSON_LATEST . ')', null ],
        ];
    }
}
