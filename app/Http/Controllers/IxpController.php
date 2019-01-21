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

use Cache, Countries, D2EM, Former, Redirect, Route, Validator;

use Entities\{
    IXP   as IXPEntity
};

use Repositories\IXP as IXPRepository;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


/**
 * IXP Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IxpController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var IXPEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->feParams = (object)[
            'entity'            => IXPEntity::class,

            'pagetitle'         => 'IXPs',

            'titleSingular'     => 'IXP',
            'nameSingular'      => 'a IXP',

            'viewFolderName'    => 'ixp',
        ];
    }

    public static function routes() {

        Route::group( [ 'prefix' => 'ixp' ], function() {
            Route::get(  'edit/{id}',   'IxpController@edit'    )->name( 'ixp@edit'    );
            Route::post( 'store',       'IxpController@store'   )->name( 'ixp@store'   );
        });
    }


    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( IXPEntity::class )->getAllForFeList( $this->feParams, $id );
    }


    private function getCountries(): array
    {
        $countries = [];
        foreach( Countries::getList( 'name' ) as $c ) {
            $countries[ $c['iso_3166_2'] ] = $c['name'];
        }

        return $countries;
    }

    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        if( $id === null || !( $this->object = D2EM::getRepository( IXPEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $old = request()->old();

        Former::populate([
            'name'          => array_key_exists( 'name',        $old ) ? $old[ 'name' ]         : $this->object->getName(),
            'shortname'     => array_key_exists( 'shortname',   $old ) ? $old[ 'shortname' ]    : $this->object->getShortname(),
            'address1'      => array_key_exists( 'address1',    $old ) ? $old[ 'address1' ]     : $this->object->getAddress1(),
            'address2'      => array_key_exists( 'address2',    $old ) ? $old[ 'address2' ]     : $this->object->getAddress2(),
            'address3'      => array_key_exists( 'address3',    $old ) ? $old[ 'address3' ]     : $this->object->getAddress3(),
            'address4'      => array_key_exists( 'address4',    $old ) ? $old[ 'address4' ]     : $this->object->getAddress4(),
            'country'       => array_key_exists( 'country',     $old ) ? $old[ 'country' ]      : $this->object->getCountry(),
        ]);

        return [
            'object'            => $this->object,
            'countries'         => $this->getCountries(),
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
            'name'              => 'required|string|max:255',
            'shortname'         => 'required|string|max:255',
            'country'           => 'required|string|in:' . implode( ',', array_keys( $this->getCountries() ) ),

        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        if( !$request->input( 'id', false ) || !( $this->object = D2EM::getRepository( IXPEntity::class )->find( $request->input( 'id' ) ) ) ) {
            abort(404);
        }

        $this->object->setName(             $request->input( 'name'          ) );
        $this->object->setShortname(        $request->input( 'shortname'     ) );
        $this->object->setAddress1(         $request->input( 'address1'      ) );
        $this->object->setAddress2(         $request->input( 'address2'      ) );
        $this->object->setAddress3(         $request->input( 'address3'      ) );
        $this->object->setAddress4(         $request->input( 'address4'      ) );
        $this->object->setCountry(          $request->input( 'country'       ) );

        D2EM::flush( $this->object );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function postStoreRedirect() {
        return route( 'infrastructure@list' );
    }

    /**
     * Overriding optional method to clear cached entries:
     *
     * @param string $action Either 'add', 'edit', 'delete'
     * @return bool
     */
    protected function postFlush( string $action  ): bool{
        // wipe cached entries
        Cache::forget( IXPRepository::CACHE_KEY_DEFAULT_IXP );
        Cache::forget( "ixp_{$this->object->getId()}" );

        return true;
    }

}