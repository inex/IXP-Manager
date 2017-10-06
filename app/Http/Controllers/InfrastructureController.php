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

use Auth, D2EM, Former, Redirect, Validator;

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

            'defaultAction'     => 'list',                        // OPTIONAL; defaults to 'list'
            'defaultController' => 'InfrastructureController',   // OPTIONAL; defaults to 'list'

            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'infrastructure'
        ];


        switch( Auth::user()->getPrivs() ) {

            case UserEntity::AUTH_SUPERUSER:
                $this->feParams->listColumns = [
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
                ];

                // display the same information in the view as the list
                $this->feParams->viewColumns = $this->feParams->listColumns;

                $this->feParams->defaultAction = 'list';

                break;

            default:
                abort( 'error/insufficient-permissions' );
        }

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
     * Display the form to edit a physical interface
     *
     * @param   int $id ID of the customer equipment
     *
     * @return View
     */
    public function addPrepareData( $id = null ) {
        /** @var InfrastructureEntity $inf */
        $inf = false;


        if( $id != null ) {
            if( !( $inf = D2EM::getRepository( InfrastructureEntity::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'name'                  => $inf->getName(),
                'sname'                 => $inf->getShortname(),
                'primary'               => $inf->getIsPrimary() ?? false,
                'ixp'                   => $this->multiIXP() ? $inf->getIXP() : 1 ,
            ]);
        }

        return [
            'data'                              => $this->data,
            'inf'                               => $inf,
            'multiIXP'                          => $this->multiIXP(),
            'listIXP'                           => $this->multiIXP() ? D2EM::getRepository( IXPEntity::class )->getNames( Auth::user() ) : 1,
        ];
    }

    public function storePrepareAction( Request $request ){

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'sname'                 => 'required|string|max:255',
            'ixp'                   => 'required|int|exists:Entities\IXP,id',

        ]);

        if ($validator->fails()) {
            return redirect::back()->withErrors($validator)->withInput();
        }

        /** @var InfrastructureEntity $inf  */
        if( $request->input( 'id', false ) ) {
            // get the existing Cust Kit object for that ID
            if( !( $inf = D2EM::getRepository( InfrastructureEntity::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown Infrastructure' );
                abort(404);
            }
        } else {
            $inf = new InfrastructureEntity;
            D2EM::persist( $inf );
        }

        $inf->setName(              $request->input( 'name'         ) );
        $inf->setShortname(         $request->input( 'sname'        ) );
        $inf->setIxfIxId(           $request->input( 'ixf_ix_id'    ) );
        $inf->setPeeringdbIxId(     $request->input( 'pdb_ixp'      ) );
        $inf->setIsPrimary(         $request->input( 'primary'      ) ?? false );
        $inf->setIXP(               D2EM::getRepository( IXPEntity::class )->find( $request->input( 'ixp' ) ) );


        D2EM::flush($inf);

        return true;
    }
}
