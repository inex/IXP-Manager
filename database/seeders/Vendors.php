<?php

namespace Database\Seeders;

/**
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\Vendor;

/**
 * Seed the database vendors table with common vendors
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Database\Seeds
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Vendors extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
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

        foreach( $vendors as $vendor ) {
            Vendor::create([
                'name'          => $vendor[0],
                'shortname'     => $vendor[1],
                'nagios_name'   => $vendor[2],
                'bundle_name'   => $vendor[3] ?? null,
            ]);
        }
    }
}
