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

use Auth, Route;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use IXP\Models\{
    Layer2Address,
    User,
    VlanInterface
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Layer2Address Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Layer2AddressController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var Layer2Address
     */
    protected $object = null;

    /**
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = User::AUTH_CUSTUSER;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => Layer2Address::class,
            'pagetitle'         => 'Configured MAC Addresses',
            'titleSingular'     => 'Configured MAC Address',
            'nameSingular'      => 'a configured MAC address',
            'listOrderBy'       => 'abbreviatedName',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'layer2-address',
            'readonly'          => self::$read_only,
            'documentation'     => 'https://docs.ixpmanager.org/features/layer2-addresses/',
            'listColumns'       => [
                'customer'          => 'Customer',
                'switchport'        => 'Interface(s)',
                'vlan'              => 'VLAN',
                'ip4'               => 'IPv4',
                'ip6'               => 'IPv6',
                'mac'               => 'MAC Address',
                'organisation'      => 'Manufacturer'
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;

        // phpunit / artisan trips up here without the cli test:
        if( PHP_SAPI !== 'cli' ) {
            // custom access controls:
            switch( Auth::check() ? Auth::getUser()->privs() : User::AUTH_PUBLIC ) {
                case User::AUTH_SUPERUSER:
                    break;
                case User::AUTH_CUSTUSER || User::AUTH_CUSTADMIN:
                    switch( Route::current()->getName() ) {
                        case 'layer2-address@forVlanInterface':
                            break;
                        default:
                            $this->unauthorized();
                    }
                    break;
                default:
                    $this->unauthorized();
            }
        }
    }

    /**
     * Additional routes
     *
     * @param string $route_prefix
     *
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix ): Void
    {
        // NB: this route is marked as 'read-only' to disable normal CRUD operations. It's not really read-only.
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::get(  'vlan-interface/{vli}', 'Layer2AddressController@forVlanInterface' )->name( $route_prefix . '@forVlanInterface' );
        });
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null t $id The `id` of the row to load for `view` action`. `null` if `listAction`

     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return Layer2Address::selectRaw( "l.*,
            vi.id AS viid,
            c.id AS customerid, c.abbreviatedName AS customer,
            s.name AS switchname,
            vl.name as vlan, vl.id as vlanid, 
            vli.id as vliid,
            GROUP_CONCAT( sp.name ) AS switchport,
            GROUP_CONCAT( DISTINCT ipv4.address ) AS ip4,
            GROUP_CONCAT( DISTINCT ipv6.address ) AS ip6,
            COALESCE( o.organisation, 'Unknown' ) AS organisation"
        )
            ->from( 'l2address AS l' )
            ->join( 'vlaninterface AS vli', 'vli.id', 'l.vlan_interface_id' )
            ->join( 'vlan AS vl', 'vl.id', 'vli.vlanid' )
            ->leftjoin( 'ipv4address AS ipv4', 'ipv4.id', 'vli.ipv4addressid' )
            ->leftjoin( 'ipv6address AS ipv6', 'ipv6.id', 'vli.ipv6addressid' )
            ->join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->join( 'cust AS c', 'c.id', 'vi.custid' )
            ->leftjoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->leftjoin( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->leftjoin( 'switch AS s', 's.id', 'sp.switchid' )
            ->leftjoin( 'oui AS o', 'o.oui', '=', DB::raw("SUBSTRING( l.mac, 1, 6 )") )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('l.id', $id );
            } )->groupBy( 'l.mac', 'vi.id', 'l.id', 'l.firstseen',
                'l.lastseen', 'c.id', 'c.abbreviatedName', 's.name',
                'vl.name', 'vl.id', 'vli.id', 'o.organisation'
            )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->get()->toArray();
    }

    /**
     * Display all the layer2addresses for a VlanInterface
     *
     * @param   VlanInterface $vli VlanInterface the VlanInterface
     *
     * @return  View|RedirectResponse
     */
    public function forVlanInterface( VlanInterface $vli ): View|RedirectResponse
    {
        if( Auth::getUser()->isSuperUser() ) {
            return view( 'layer2-address/vlan-interface' )->with( [ 'vli' => $vli ] );
        }

        if( config( 'ixp_fe.layer2-addresses.customer_can_edit' ) && Auth::getUser()->custid === $vli->virtualInterface->customer->id ) {
            return view( 'layer2-address/vlan-interface-cust' )->with( [ 'vli' => $vli ] );
        }
        return redirect('');
    }
}