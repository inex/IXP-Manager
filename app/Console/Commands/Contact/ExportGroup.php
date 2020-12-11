<?php

namespace IXP\Console\Commands\Contact;

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

use Illuminate\Database\Eloquent\Builder;
use IXP\Console\Commands\Command;

use D2EM;
use IXP\Models\Contact;

/**
  * Artisan command to export contacts by group
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin <yanny@islandbridgenetworks.ie>
  * @category   Contact
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class ExportGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact:export-group
                        {--type= : Contact group type (e.g. ROLE)}
                        {--name= : Contact group name (e.g. beer)}
                        {--format=json : Output format - one of json (default) or csv}
                        {--cid= : Optionally limit results to given customer id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export contacts based on group information';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $name = $this->option('name');
        // Imported from Zend Framework with little change on 2017-11
        if( ( !$type && !$name ) || ( $type && $name ) )  {
            $this->error( "Group name or type must be set (and not both)." );
            return -1;
        }

        if( !in_array( $this->option('format'), [ 'json', 'csv' ] ) ) {
            $this->error( "Format must be either 'json' or 'csv'." );
            return -2;
        }

        $contacts = Contact::selectRaw( 'c.name AS name, c.position as position, c.email AS email, c.phone AS phone, c.mobile AS mobile,
                    c.facilityaccess AS facilityaccess, c.mayauthorize AS mayauthorize, c.notes as notes' )
            ->from( 'contact AS c' )
            ->leftJoin( 'contact_to_group AS ctg', 'ctg.contact_id', 'c.id' )
            ->leftJoin( 'contact_group AS cg', 'cg.id', 'ctg.contact_group_id' )
            ->leftJoin( 'cust AS cu', 'cu.id', 'c.custid')
            ->when( $type, function( Builder $q, $type ) {
                return $q->where( 'cg.type', $type );
            }, function( $query ) use( $name ) {
                return $query->where( 'cg.name', $name );
            } )
            ->when( $cid = $this->option('cid'), function( Builder $q, $cid ) {
                return $q->where( 'cu.id', $cid );
            })->groupBy( 'c.id' )->get()->toArray();

        if( !count( $contacts ) ) {
            if( $this->option('format' ) === 'json' ) {
                echo json_encode( [] ) . "\n";
            }
            return 0;
        }

        if( $this->option('format') === "csv" )  {
            $names= [];
            foreach( $contacts[0] as $name => $data ) {
                $names[] = $name;
            }

            array_unshift( $contacts, $names );

            $out = fopen('php://output', 'w');

            foreach( $contacts as $c ) {
                fputcsv( $out, $c );
            }

            fclose( $out );
        } else {
            echo json_encode( $contacts );
        }
        echo "\n";
        return 0;
    }
}