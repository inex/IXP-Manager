<?php

namespace IXP\Console\Commands\Utils\Export\JsonSchema;

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
use IXP\Console\Commands\Command as IXPCommand;

use IXP\Utils\Export\JsonSchema;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use GuzzleHttp\Client;

 /**
  * Artisan command to POST the JSON Export to a given endpoint
  *
  * @author     Barry O'Donovan <barry@opensolutions.ie>
  * @category   Utils
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class Post extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:json-schema-post
                            {url       : The url end point}
                            {--ver=    : Schema version to export (default: v' . JsonSchema::EUROIX_JSON_LATEST . ')}';

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
        if( $this->getOutput()->isVerbose() ){
            $this->info( "{$this->name} :: Generating schema..." );
        }

        $exporter = new JsonSchema();
        $json = $exporter->get( $this->option( 'ver' ) );

        try {
            if( $this->getOutput()->isVerbose() ){
                $this->info( "{$this->name} :: Posting schema..." );
            }

            $client = new Client( [ 'base_uri' => $this->argument( 'url' ), 'timeout'  => 60.0, ] );
            $response = $client->post( $this->argument( 'url' ), [ 'body' => $json ] );
        } catch( \Exception $e ) {
            $this->error( 'Could not post data: ' . $e->getMessage() );
            return -1;
        }

        if( $this->getOutput()->isVerbose() ){
            $this->info( "{$this->name} :: Schema posted." );
        }
    }
}