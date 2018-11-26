<?php

/**
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Database\Seeder;

/**
 * Seed the database IRRDB table with common use cases
 */
class IRRDBs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $irrdbs = [
            [
                'host'     => 'whois.ripe.net',
                'protocol' => 'ripe',
                'source'   => 'RIPE',
                'notes'    => 'RIPE Query from RIPE Database'
            ],

            [
                'host'     => 'whois.ripe.net',
                'protocol' => 'ripe',
                'source'   => 'RIPE,RIPE-NONAUTH',
                'notes'    => 'RIPE+RIPE-NONAUTH Query from RIPE Database'
            ],

            [
                'host'     => 'whois.radb.net',
                'protocol' => 'irrd',
                'source'   => 'RADB',
                'notes'    => 'RADB Query from RADB Database'
            ],

            [
                'host'     => 'whois.lacnic.net',
                'protocol' => 'ripe',
                'source'   => 'LACNIC',
                'notes'    => 'LACNIC Query from LACNIC Database'
            ],

            [
                'host'     => 'whois.afrinic.net',
                'protocol' => 'ripe',
                'source'   => 'AFRINIC',
                'notes'    => 'AFRINIC Query from AFRINIC Database'
            ],

            [
                'host'     => 'whois.apnic.net',
                'protocol' => 'ripe',
                'source'   => 'APNIC',
                'notes'    => 'APNIC Query from APNIC Database'
            ],

            [
                'host'     => 'rr.level3.net',
                'protocol' => 'ripe',
                'source'   => 'LEVEL3',
                'notes'    => 'Level3 Query from Level3 Database'
            ],

            [
                'host'     => 'whois.radb.net',
                'protocol' => 'irrd',
                'source'   => 'ARIN',
                'notes'    => 'ARIN Query from RADB Database'
            ],

            [
                'host'     => 'whois.radb.net',
                'protocol' => 'irrd',
                'source'   => 'RADB,ARIN',
                'notes'    => 'RADB+ARIN Query from RADB Database'
            ],

            [
                'host'     => 'whois.radb.net',
                'protocol' => 'irrd',
                'source'   => 'ALTDB',
                'notes'    => 'ALTDB Query from RADB Database'
            ],

            [
                'host'     => 'whois.radb.net',
                'protocol' => 'irrd',
                'source'   => 'RADB,RIPE',
                'notes'    => 'RADB+RIPE Query from RADB Database'
            ],

            [
                'host'     => 'whois.radb.net',
                'protocol' => 'irrd',
                'source'   => 'RADB,APNIC,ARIN',
                'notes'    => 'RADB+APNIC+ARIN Query from RADB Database'
            ],

            [
                'host'     => 'whois.radb.net',
                'protocol' => 'irrd',
                'source'   => 'RIPE,ARIN',
                'notes'    => 'RIPE+ARIN Query from RADB Database'
            ]
        ];



        foreach( $irrdbs as $irrdb )
        {
            $e = new \Entities\IRRDBConfig();
            $e->setHost(     $irrdb['host']     );
            $e->setProtocol( $irrdb['protocol'] );
            $e->setSource(   $irrdb['source']   );
            $e->setNotes( $irrdb['notes']       );
            D2EM::persist( $e );
        }

        D2EM::flush();
    }
}
