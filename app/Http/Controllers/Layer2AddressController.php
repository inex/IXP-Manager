<?php

namespace IXP\Http\Controllers;

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
use Auth, Route;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use IXP\Models\{
    Layer2Address,
    User,
    VlanInterface
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Layer2Address Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Layer2AddressController extends EloquentController
{
    /**
     * The object being created / edited
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
    public function feInit()
    {
        $this->feParams         = (object)[
            'entity'            => Layer2Address::class,
            'pagetitle'         => 'Configured MAC Addresses',
            'titleSingular'     => 'Configured MAC Address',
            'nameSingular'      => 'a configured MAC address',
            'listOrderBy'       => 'abbreviatedName',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'layer2-address',
            'readonly'          => self::$read_only,
            'documentation'     => 'https://docs.ixpmanager.org/features/layer2-addresses/',

            'listColumns'       => [
                'id'                => [
                    'title' => 'DB ID',
                    'display' => false
                ],
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
        if( php_sapi_name() !== 'cli' ) {
            // custom access controls:
            switch( Auth::check() ? Auth::user()->getPrivs() : User::AUTH_PUBLIC ) {
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
            Route::get(  'vlan-interface/{vli}', 'Layer2AddressController@forVlanInterface' )->name( "layer2-address@forVlanInterface" );
        });
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`

     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        return Layer2Address::getFeList( $this->feParams, $id );
    }

    /**
     * Display all the layer2addresses for a VlanInterface
     *
     * @param   VlanInterface $vli VlanInterface the VlanInterface
     *
     * @return  View|RedirectResponse
     */
    public function forVlanInterface( VlanInterface $vli )
    {
        if( Auth::getUser()->isSuperUser() ) {
            return view( 'layer2-address/vlan-interface' )->with( [ 'vli' => $vli ] );
        }

        if( config( 'ixp_fe.layer2-addresses.customer_can_edit' ) && Auth::getUser()->getCustomer()->getId() === $vli->virtualInterface->customer->id ) {
            return view( 'layer2-address/vlan-interface-cust' )->with( [ 'vli' => $vli ] );
        }

        return redirect('');
    }
}
