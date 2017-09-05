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
 * Seed the database vendors table with common vendors
 */
class Vendors extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendors = [
            [ "Cisco Systems", 'Cisco', 'cisco', 'Port-channel' ],
            [ "Foundry Networks", 'Brocade', 'brocade' ],
            [ "Extreme Networks", 'Extreme', 'extreme' ],
            [ "Force10 Networks", 'Force10', 'force10' ],
            [ "Glimmerglass", 'Glimmerglass', 'glimmerglass' ],
            [ "Allied Telesyn", 'AlliedTel', 'alliedtel' ],
            [ "Enterasys", 'Enterasys', 'enterasys' ],
            [ "Dell", 'Dell', 'dell' ],
            [ "Hitachi Cable", 'Hitachi', 'hitachi' ],
            [ "MRV", 'MRV', 'mrv' ],
            [ "Transmode", 'Transmode', 'transmode' ],
            [ "Brocade", 'Brocade', 'brocade' ],
            [ "Juniper Networks", 'Juniper', 'juniper' ],
            [ "Linux", 'Linux', 'linux' ],
            [ "Hewlett-Packard", 'HP', 'hp' ],
            [ "Arista", 'Arista', 'arista', 'Port-channel' ],
            [ "Cumulus Networks", 'Cumulus', 'cumulus', 'bond' ],
        ];

        foreach( $vendors as $vendor )
        {
            $e = new \Entities\Vendor();
            $e->setName(       $vendor[0] );
            $e->setShortname(  $vendor[1] );
            $e->setNagiosName( $vendor[2] );
            
            if( isset( $vendor[3] ) ) {
                $e->setBundleName( $vendor[3] );
            }
            
            D2EM::persist( $e );
        }

        D2EM::flush();
    }
}
