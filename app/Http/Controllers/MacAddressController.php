<?php

namespace IXP\Http\Controllers;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use IXP\Models\MacAddress;

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Mac address Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MacAddressController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var MacAddress
     */
    protected $object = null;

    /**
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => MacAddress::class,
            'pagetitle'         => 'Discovered MAC Addresses',
            'titleSingular'     => 'MAC Address',
            'nameSingular'      => 'a MAC address',
            'listOrderBy'       => 'abbreviatedName',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'mac-address',
            'readonly'          => self::$read_only,
            'documentation'     => 'https://docs.ixpmanager.org/features/layer2-addresses/',
            'listColumns'       => [
                'customer'       => 'Customer',
                'switchport'     => 'Interface(s)',
                'ip4'            => 'IPv4',
                'ip6'            => 'IPv6',
                'mac'            => 'MAC Address',
                'organisation'   => 'Manufacturer'
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`

     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return MacAddress::selectRaw( "m.mac,
            ANY_VALUE(m.id) AS id,
            m.virtualinterfaceid,
            m.created_at,
            m.updated_at,
            vi.id AS viid,
            c.id AS customerid, c.abbreviatedName AS customer,
            s.name AS switchname, 
            GROUP_CONCAT( DISTINCT sp.name ) AS switchport,
            GROUP_CONCAT( DISTINCT ipv4.address ) AS ip4,
            GROUP_CONCAT( DISTINCT ipv6.address ) AS ip6,
            COALESCE( o.organisation, 'Unknown' ) AS organisation"
        )
            ->from( 'macaddress AS m' )
            ->join( 'virtualinterface AS vi', 'vi.id', 'm.virtualinterfaceid' )
            ->join( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', 'vli.ipv4addressid' )
            ->leftjoin( 'ipv6address AS ipv6', 'ipv6.id', 'vli.ipv6addressid' )
            ->join( 'cust AS c', 'c.id', 'vi.custid' )
            ->leftjoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->leftjoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftjoin( 'switch AS s', 's.id', 'sp.switchid' )
            ->leftjoin( 'oui AS o', 'o.oui', '=', DB::raw("SUBSTRING( m.mac, 1, 6 )") )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('m.id', $id );
            } )->groupBy( 'm.mac', 'vi.id',
                'c.id', 'c.abbreviatedName', 's.name', 'o.organisation'
            )
            ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->get()->toArray();
    }
}