<?php

/**
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
            [ "Allied Telesyn",   'AlliedTel',     'alliedtel'                    ],
            [ "Allied Telesis",   'AlliedTelesis', 'alliedtelesis'                ],
            [ "Arista",           'Arista',        'arista',       'Port-channel' ],
            [ "Brocade",          'Brocade',       'brocade'                      ],
            [ "Cisco Systems",    'Cisco',         'cisco',        'Port-channel' ],
            [ "Cumulus Networks", 'Cumulus',       'cumulus',      'bond'         ],
            [ "Dell",             'Dell',          'dell'                         ],
            [ "Enterasys",        'Enterasys',     'enterasys'                    ],
            [ "Extreme Networks", 'Extreme',       'extreme'                      ],
            [ "Force10 Networks", 'Force10',       'force10'                      ],
            [ "Foundry Networks", 'Brocade',       'brocade'                      ],
            [ "Glimmerglass",     'Glimmerglass',  'glimmerglass'                 ],
            [ "Hewlett-Packard",  'HP',            'hp'                           ],
            [ "Hitachi Cable",    'Hitachi',       'hitachi'                      ],
            [ "Juniper Networks", 'Juniper',       'juniper'                      ],
            [ "Linux",            'Linux',         'linux'                        ],
            [ "MRV",              'MRV',           'mrv'                          ],
            [ "Transmode",        'Transmode',     'transmode'                    ],
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
