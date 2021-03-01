<?php

namespace IXP\Database\Seeders;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Database\Seeder;

use IXP\Models\IrrdbConfig;

/**
 * Seed the database IRRDB table with common use cases
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Database\Seeds
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IRRDBs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
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

        foreach( $irrdbs as $irrdb ) {
            IrrdbConfig::create([
                'host'      => $irrdb['host'],
                'protocol'  => $irrdb['protocol'],
                'source'    => $irrdb['source'],
                'notes'     => $irrdb['notes'],
            ]);
        }
    }
}