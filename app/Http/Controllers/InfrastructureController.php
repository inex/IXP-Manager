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

use Countries, Former;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\{
    Infrastructure
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController as Eloquent2Frontend;

/**
 * Infrastructure Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class InfrastructureController extends Eloquent2Frontend
{
    /**
     * The object being created / edited
     *
     * @var Infrastructure
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             =>  Infrastructure::class,
            'pagetitle'         => 'Infrastructures',
            'titleSingular'     => 'Infrastructure',
            'nameSingular'      => 'infrastructure',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'infrastructure',
            'listColumns'       => [
                'name'      => 'Name',
                'shortname' => 'Shortname',
                'isPrimary' => [
                    'title' => 'Primary',
                    'type' => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'ixf_ix_id' => [
                    'title'    => 'IXF-ID',
                    'type'     => self::$FE_COL_TYPES[ 'REPLACE' ],
                    'subject'  => '<a href="' . config( 'ixp_api.IXPDB.ixp_www' ) . '/%%COL%%/" target="_blank">%%COL%%</a>',
                ],
                'peeringdb_ix_id' => [
                    'title'    => 'PeeringDB ID',
                    'type'     => self::$FE_COL_TYPES[ 'REPLACE' ],
                    'subject'  => '<a href="' . config( 'ixp_api.peeringDB.ixp_www' ) . '/%%COL%%" target="_blank">%%COL%%</a>',
                ],
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns, [
                'country'   => [
                    'title' => 'Country',
                    'type' => self::$FE_COL_TYPES[ 'COUNTRY' ]
                ],
                'notes'       => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ],
                'created_at'       => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'updated_at'       => [
                    'title'         => 'Updated',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        );
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
        return Infrastructure::when( $id , static function( Builder $q, $id ) {
            return $q->where('id', $id );
        } )->when( $feParams->listOrderBy , static function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
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
    protected function editPrepareForm( int $id ): array
    {
        $this->object = Infrastructure::findOrFail( $id );

        Former::populate([
            'name'             => request()->old( 'name',      $this->object->name          ),
            'shortname'        => request()->old( 'shortname', $this->object->shortname     ),
            'isPrimary'        => request()->old( 'isPrimary', $this->object->isPrimary     ),
            'country'          => request()->old( 'country', in_array( $this->object->country,  array_values( Countries::getListForSelect( 'iso_3166_2' ) ), false ) ? $this->object->country : null ),
            'notes'            => request()->old( 'notes', $this->object->notes             ),
        ]);

        return [
            'object'            => $this->object,
            'countries'         => Countries::getList('name' )
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $r ): bool|RedirectResponse
    {
        $this->checkForm( $r );
        $this->object = Infrastructure::create( $r->all() );
        $this->resetInfrastructures();
        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $r, int $id ): bool|RedirectResponse
    {
        $this->object = Infrastructure::findOrFail( $id );
        $this->checkForm( $r );
        $this->object->update( $r->all() );
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

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'name'          => 'required|string|max:255|unique:infrastructure,name' . ( $r->id ? ','. $r->id : '' ),
            'shortname'     => 'required|string|max:255',
            'country'       => 'required|string|max:2|in:' . implode( ',', array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ),
        ] );
    }
}