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

use Former, Redirect;

use Illuminate\Database\Eloquent\Builder;
use IXP\Models\{
    NetworkInfo,
    Router,
    Vlan
};

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * Network Information Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class NetworkInfoController extends EloquentController
{
    /**
     * The object being created / edited
     * @var NetworkInfo
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'entity'            => NetworkInfo::class,
            'pagetitle'         => 'Network Information',
            'titleSingular'     => 'Network Information',
            'nameSingular'      => 'Network Information',
            'listOrderBy'       => 'vlan_id',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'network-info',

            'listColumns'       => [
                'vlanname'  => [
                    'title'         => 'Vlan',
                    'type'          => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller'    => 'vlan',
                    'action'        => 'view',
                    'idField'       => 'vlan_id'
                ],
                'protocol'      => [
                    'title' => 'Protocol',
                    'type'      =>   self::$FE_COL_TYPES[ 'CONST' ],
                    'const'     =>   Router::$PROTOCOLS,
                ],
                'network'       => 'Network',
                'masklen'       => 'Masklen',
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     *
     * @throws
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return NetworkInfo::select( [ 'networkinfo.*', 'vlan.id AS vlan_id', 'vlan.name AS vlanname' ] )
            ->leftJoin( 'vlan', 'vlan.id','networkinfo.vlanid' )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where( 'networkinfo.id', $id );
            })
            ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })
            ->get()->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'                => $this->object,
            'vlans'                 => Vlan::publicOnly()->orderBy('number')->get(),
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( int $id ): array
    {
        $this->object = NetworkInfo::findOrFail( $id );

        Former::populate([
            'vlanid'                => request()->old( 'vlan',        $this->object->vlanid ),
            'protocol'              => request()->old( 'protocol',    $this->object->protocol ),
            'network'               => request()->old( 'network',     $this->object->network ),
            'masklen'               => request()->old( 'masklen',     $this->object->masklen ),
        ]);

        return [
            'object'                => $this->object,
            'vlans'                 => Vlan::publicOnly()->orderBy('number')->get(),
        ];
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     */
    public function checkForm( Request $request ): void
    {
        $rangeMasklen = $request->protocol == '4' ? 'min:16|max:29' : 'min:32|max:64';

        $request->validate( [
            'vlanid'                => [ 'required', 'integer',
                function ($attribute, $value, $fail) {
                    if( !Vlan::whereId( $value )->exists() ) {
                        return $fail( 'Vlan does not exist.' );
                    }
                } ],
            'protocol'              => 'required|integer|in:' . implode( ',', array_keys( Router::$PROTOCOLS ) ),
            'network'               => 'required|max:255|ipv' . $request->protocol ,
            'masklen'               => 'required|integer|' . $rangeMasklen ,
        ] );
    }

    /**
     * Check if there is a duplicate network info object with those values
     *
     * @param int|null       $objectid
     * @param Request   $request
     *
     * @return bool
     */
    private function checkIsDuplicate( Request $request, int $objectid = null  ): bool
    {
        $vlan = Vlan::find( $request->vlanid );

        if( $ni = NetworkInfo::where( "vlanid" , $vlan->id  )->where( 'protocol' , $request->protocol )->first() ) {
            if( $objectid !== $ni->id ) {
                AlertContainer::push( "Network information for this vlan (" . $vlan->name . ") and and protocol (ipv" . $request->protocol . ") already exists. Please edit that instead."   , Alert::DANGER );
                return true;
            }
        }
        return false;
    }
    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $request )
    {
        $this->checkForm( $request );

        if( $this->checkIsDuplicate( $request, null ) ) {
            return Redirect::back()->withInput();
        }

        $this->object = NetworkInfo::create( $request->all() );
        return true;
    }

    /**
     * Function to do the actual validation and updating of the submitted object.
     *
     * @param Request $request
     *
     * @param int $id
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = NetworkInfo::findOrFail( $id );
        $this->checkForm( $request );

        if( $this->checkIsDuplicate( $request, $this->object->id ) ) {
            return Redirect::back()->withInput();
        }

        $this->object->update( $request->all() );
        return true;
    }
}