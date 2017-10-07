<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM, Former, Redirect, Route, Validator;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use Entities\{
    Infrastructure      as InfrastructureEntity,
    User                as UserEntity,
    IXP                 as IXPEntity
};
use Illuminate\Http\Request;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};


/**
 * CustKit Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class InfrastructureController extends Doctrine2Frontend {

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){
        //$this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );

        $this->data[ 'feParams' ] =  $this->feParams = (object)[

            'entity'            => InfrastructureEntity::class,
            'pagetitle'         => 'Infrastructures',

            'titleSingular'     => 'Infrastructure',
            'nameSingular'      => 'an infrastructure',

            'defaultAction'     => 'list',
            'defaultController' => 'InfrastructureController',

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
                    'subject'  => '<a href="https://db.ix-f.net/api/ixp/%%COL%%" target="_blank">%%COL%%</a>'
                ],

                'peeringdb_ix_id' => [
                    'title'    => 'PeeringDB ID',
                    'type'     => self::$FE_COL_TYPES[ 'REPLACE' ],
                    'subject'  => '<a href="https://www.peeringdb.com/api/ix/%%COL%%" target="_blank">%%COL%%</a>'
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;


    }


    /**
     * Provide array of users for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( InfrastructureEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {

        /** @var InfrastructureEntity $inf */
        $inf = false;

        if( $id !== null ) {

            if( !( $inf = D2EM::getRepository( InfrastructureEntity::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'name'             => $inf->getName(),
                'shortname'        => $inf->getShortname(),
                'isPrimary'        => $inf->getIsPrimary() ?? false,
            ]);
        }

        return [
            'inf'          => $inf
        ];
    }


    /**
     * Function to do the actual validation and storing of the submitted object.
     * @param Request $request
     * @return bool|RedirectResponse
     */
    public function doStore( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'name'                  => 'required|string|max:255',
            'shortname'             => 'required|string|max:255',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $inf = D2EM::getRepository( InfrastructureEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $inf = new InfrastructureEntity;
            D2EM::persist( $inf );
        }

        $inf->setName(              $request->input( 'name'         ) );
        $inf->setShortname(         $request->input( 'shortname'    ) );
        $inf->setIxfIxId(           $request->input( 'ixf_ix_id'    ) ? $request->input( 'ixf_ix_id'    ) : null );
        $inf->setPeeringdbIxId(     $request->input( 'pdb_ixp'      ) ? $request->input( 'pdb_ixp'      ) : null );
        $inf->setIsPrimary(         $request->input( 'primary'      ) ?? false );
        $inf->setIXP(               D2EM::getRepository( IXPEntity::class )->getDefault() );

        D2EM::flush($inf);

        if( $inf->getIsPrimary() ) {
            // reset the rest:
            foreach( D2EM::getRepository( InfrastructureEntity::class )->findAll() as $i ) {
                if( $i->getId() == $inf->getId() || !$i->getIsPrimary() ) {
                    continue;
                }
                $i->setIsPrimary( false );
            }
            D2EM::flush();
        }

        $this->object = $inf;
        return true;
    }
}
