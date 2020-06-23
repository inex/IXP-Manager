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
use Cache, Countries, Former;

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\{
    Infrastructure,
    Ixp
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController as Eloquent2Frontend;

/**
 * Infrastructure Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 20202 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class InfrastructureController extends Eloquent2Frontend
{
    /**
     * The object being added / edited
     * @var Infrastructure
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'entity'            =>  Infrastructure::class,
            'pagetitle'         => 'Infrastructures',
            'titleSingular'     => 'Infrastructure',
            'nameSingular'      => 'infrastructure',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'infrastructure',
            'listColumns'       => [
                'id'        => [
                    'title' => 'DB ID' ,
                    'display' => true
                ],
                'name'      => 'Name',
                'shortname' => 'Shortname',
                'isPrimary' => [
                    'title' => 'Primary',
                    'type' => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'ixf_ix_id' => [
                    'title'    => 'IXF-ID',
                    'type'     => self::$FE_COL_TYPES[ 'REPLACE' ],
                    'subject'  => '<a href="https://db.ix-f.net/api/ixp/%%COL%%" target="_blank">%%COL%%</a>',
                ],
                'peeringdb_ix_id' => [
                    'title'    => 'PeeringDB ID',
                    'type'     => self::$FE_COL_TYPES[ 'REPLACE' ],
                    'subject'  => '<a href="https://www.peeringdb.com/api/ix/%%COL%%" target="_blank">%%COL%%</a>',
                ],
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns, [
                'country'   => [ 'title' => 'Country', 'type' => self::$FE_COL_TYPES[ 'COUNTRY' ] ],
            ]
        );
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`

     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        return Infrastructure::getFeList( $this->feParams, $id )->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'            => $this->object,
            'countries'         => Countries::getList('name' )
        ];
    }

    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function editPrepareForm( $id = null ): array
    {
        $this->object = Infrastructure::findOrFail( $id );

        Former::populate([
            'name'             => request()->old( 'name',      $this->object->name ),
            'shortname'        => request()->old( 'shortname', $this->object->shortname ),
            'isPrimary'        => request()->old( 'isPrimary', ( $this->object->isPrimary ? 1 : 0 ) ),
            'country'          => request()->old( 'country', in_array( $this->object->country,  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $this->object->country : null ),
        ]);

        return [
            'object'            => $this->object,
            'countries'         => Countries::getList('name' )
        ];
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     */
    public function checkForm( Request $request )
    {
        $request->validate( [
            'name'                  => 'required|string|max:255|unique:Entities\Infrastructure,name'. ( $request->id ? ','. $request->id : '' ),
            'shortname'             => 'required|string|max:255',
            'country'               => 'required|string|max:2|in:' . implode( ',', array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ),
        ] );
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
        $this->object = Infrastructure::create( array_merge( $request->except( [ 'ixp_id' ] ) ,
            [ 'ixp_id' => Ixp::default()->id ]
        ) );

        $this->resetInfrastructures();

        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     * @param int $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = Infrastructure::findOrFail( $request->id );
        $this->checkForm( $request );

        $this->object->update( array_merge( $request->except( [ 'ixp_id' ] ) ,
            [ 'ixp_id' =>  Ixp::default()->id ]
        ) );

        $this->resetInfrastructures();

        return true;
    }

    /**
     * Function that reset the other infrastructures (isPrimary = false)
     *
     * @return void
     */
    private function resetInfrastructures(): void
    {
        if( $this->object->isPrimary ) {
            // reset the rest:
            foreach( Infrastructure::where( 'id', '!=', $this->object->id )
                         ->where( 'isPrimary', true )->get() as $infra ) {
                $infra->isPrimary = false;
                $infra->save();
            }
        }
    }
    /**
     * Overriding optional method to clear cached entries:
     *
     * @param string $action Either 'add', 'edit', 'delete'
     *
     * @return bool
     */
    protected function postFlush( string $action ): bool
    {
        // wipe cached entries
        Cache::forget( Infrastructure::CACHE_KEY_PRIMARY );
        Cache::forget( Infrastructure::CACHE_KEY_ALL     );
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete(): bool
    {
        $okay = true;
        if( ( $cnt = $this->object->switchers()->count() ) ) {
            AlertContainer::push( "You cannot delete this infrastructure there are {$cnt} switch(es) associated with it. "
                . "You can view and then reassign or delete those switches <a href=\""
                . route("switch@list", [ "infra" => $this->object->id ] )
                . "\">by clicking here</a>.", Alert::DANGER
            );
            $okay = false;
        }

        if( $cnt = $this->object->vlans()->count() ) {
            AlertContainer::push( "You cannot delete this infrastructure there are {$cnt} VLAN(s) associated with it. "
                . "You can view and then reassign or delete those VLANs <a href=\""
                . route( "vlan@infra" , [ 'id' => $this->object->id ]  )
                . "\">by clicking here</a>.", Alert::DANGER
            );
            $okay = false;
        }

        return $okay;
    }
}