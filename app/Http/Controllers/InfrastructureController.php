<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Cache, Countries, D2EM, Former, Redirect, Validator;

use Entities\{
    Infrastructure      as InfrastructureEntity,
    IXP                 as IXPEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Models\Infrastructure;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Repositories\{
    Infrastructure as InfrastructureRepository
};


use IXP\Utils\Http\Controllers\Frontend\EloquentController as Eloquent2Frontend;


/**
 * Infrastructure Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class InfrastructureController extends Eloquent2Frontend
{

    /**
     * The object being added / edited
     * @var InfrastructureEntity
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
                'id'        => [ 'title' => 'DB ID' , 'display' => true ],
                'name'      => 'Name',
                'shortname' => 'Shortname',
                'isPrimary' => [ 'title' => 'Primary', 'type' => self::$FE_COL_TYPES[ 'YES_NO' ] ],

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
    protected function listGetData( $id = null )
    {
        return Infrastructure::when( $id, function ( $q ) use ( $id ) {
            return $q->where( 'id' , $id);
        })->orderBy( $this->feParams->listOrderBy, $this->feParams->listOrderByDir )->get()->toArray();
    }

    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array
    {
        if( $id ) {
            if( !( $this->object = Infrastructure::find( $id ) ) ) {
                abort(404);
            }

            Former::populate([
                'name'             => request()->old( 'name',      $this->object->name ),
                'shortname'        => request()->old( 'shortname', $this->object->shortname ),
                'primary'          => request()->old( 'primary', ( $this->object->isPrimary ? 1 : 0 ) ),
                'country'          => request()->old( 'country', in_array( $this->object->country,  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $this->object->country : null ),
            ]);
        }

        return [
            'object'            => $this->object,
            'countries'         => Countries::getList('name' )
        ];
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
        $validator = Validator::make( $request->all(), [
            'name'                  => 'required|string|max:255|unique:Entities\Infrastructure,name'. ( $request->id ? ','. $request->id : '' ),
            'shortname'             => 'required|string|max:255',
            'country'               => 'required|string|max:2|in:' . implode( ',', array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ),
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        if( $request->id ) {
            $this->object = Infrastructure::findOrFail( $request->id );
        } else {
            $this->object = new Infrastructure;
        }

        $this->object->name =               $request->name;
        $this->object->shortname =          $request->shortname;
        $this->object->country =            $request->country;
        $this->object->ixf_ix_id =          $request->ixf_ix_id ? $request->ixf_ix_id : null ;
        $this->object->peeringdb_ix_id =    $request->pdb_ixp ? $request->pdb_ixp : null ;
        $this->object->isPrimary =          $request->primary ?? 0 ;
        $this->object->ixp_id =             D2EM::getRepository( IXPEntity::class )->getDefault();

        $this->object->save();

        if( $this->object->isPrimary ) {
            // reset the rest:
            Infrastructure::all( function ( $i ) use ( $this->object ) {
                if( $i->getId() == $this->object->getId() || !$i->getIsPrimary() ) {
                    continue;
                }
                $i->setIsPrimary( false );
            });

            D2EM::flush();
        }

        return true;
    }

    /**
     * Overriding optional method to clear cached entries:
     *
     * @param string $action Either 'add', 'edit', 'delete'
     * @return bool
     */
    protected function postFlush( string $action ): bool
    {
        // wipe cached entries
        Cache::forget( InfrastructureRepository::CACHE_KEY_PRIMARY );
        Cache::forget( InfrastructureRepository::CACHE_KEY_ALL     );
        return true;
    }



    /**
     * @inheritdoc
     */
    protected function preDelete() : bool
    {
        $okay = true;
        if( ( $cnt = count( $this->object->getSwitchers() ) ) ) {
            AlertContainer::push( "You cannot delete this infrastructure there are {$cnt} switch(es) associated with it. "
                . "You can view and then reassign or delete those switches <a href=\""
                . route("switch@list", [ "infra" => $this->object->getId() ] )
                . "\">by clicking here</a>.", Alert::DANGER
            );
            $okay = false;
        }

        if( $cnt = count( $this->object->getVlans() ) ) {
            AlertContainer::push( "You cannot delete this infrastructure there are {$cnt} VLAN(s) associated with it. "
                . "You can view and then reassign or delete those VLANs <a href=\""
                . route( "vlan@infra" , [ 'id' => $this->object->getId() ]  )
                . "\">by clicking here</a>.", Alert::DANGER
            );
            $okay = false;
        }

        return $okay;
    }

}
