<?php namespace IXP\Console\Commands\Contact;

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

use D2EM;

 /**
  * Artisan command to export contacts by group
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @category   Contact
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class ExportGroup extends Command {

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
    public function handle(): int {

        // Imported from Zend Framework with little change on 2017-11
        if( ( !$this->option('type') && !$this->option('name') ) || ( $this->option('type') && $this->option('name') ) )  {
            $this->error( "Group name or type must be set (and not both)." );
            return -1;
        }

        if( !in_array( $this->option('format'), [ 'json', 'csv' ] ) ) {
            $this->error( "Format must be either 'json' or 'csv'." );
            return -2;
        }

        $dql = "SELECT c.name AS name, c.position as position, c.email AS email, c.phone AS phone, c.mobile AS mobile,
                    c.facilityaccess AS facilityacces, c.mayauthorize AS mayauthorize, c.notes as notes

             FROM Entities\\Contact c
                LEFT JOIN c.Groups cg
                LEFT JOIN c.Customer cu\n";

        if( $this->option('type') ) {
            $dql .= " WHERE cg.type = :type";
        } else {
            $dql .= " WHERE cg.name = :name";
        }

        if( $this->option('cid') ) {
            $dql .= " AND cu.id = :cid";
        }

        $dql .= " GROUP BY c.id";

        $q = D2EM::createQuery( $dql );

        if( $this->option('type') ) {
            $q->setParameter( 'type', $this->option( 'type' ) );
        } else {
            $q->setParameter( 'name', $this->option('name') );
        }

        if( $this->option('cid') ) {
            $q->setParameter( 'cid', $this->option('cid') );
        }

        $contacts = $q->getArrayResult();

        if( !$contacts ) {
            if( $this->option('format' ) == 'json' ) {
                echo json_encode( [] ) . "\n";
            }
            return 0;
        }

        if( $this->option('format') == "csv" )  {
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
