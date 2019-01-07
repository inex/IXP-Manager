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

use D2EM, Former, Redirect, Validator;

use Entities\{
    NetworkInfo     as NetworkInfoEntity,
    Router          as RouterEntity,
    Vlan            as VlanEntity
};

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};



/**
 * Network Infor Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class NetworkInfoController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var NetworkInfoEntity
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit() {

        $this->feParams         = (object)[

            'entity'            => NetworkInfoEntity::class,
            'pagetitle'         => 'Network Information',

            'titleSingular'     => 'Network Information',
            'nameSingular'      => 'Network Information',

            'listOrderBy'       => 'vlanid',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'network-info',

            'listColumns'       => [
                'vlanname'  => [
                    'title'         => 'Vlan',
                    'type'          => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller'    => 'vlan',
                    'action'        => 'view',
                    'idField'       => 'vlanid'
                ],

                'protocol'      => [
                    'title' => 'Protocol',
                    'type'      =>   self::$FE_COL_TYPES[ 'CONST' ],
                    'const'     =>   RouterEntity::$PROTOCOLS,
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
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     *
     * @throws
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( NetworkInfoEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     * @param   int $id ID of the row to edit
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        $old = request()->old();

        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( NetworkInfoEntity::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'vlanid'                => array_key_exists( 'vlan',        $old ) ? $old[ 'vlan' ]        : $this->object->getVlan()->getId(),
                'protocol'              => array_key_exists( 'protocol',    $old ) ? $old[ 'protocol' ]    : $this->object->getProtocol(),
                'network'               => array_key_exists( 'network',     $old ) ? $old[ 'network' ]     : $this->object->getNetwork(),
                'masklen'               => array_key_exists( 'masklen',     $old ) ? $old[ 'masklen' ]     : $this->object->getMasklen(),
            ]);
        }

        return [
            'object'                => $this->object,
            'vlans'                 => D2EM::getRepository( VlanEntity::class )->getNames( ),
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
        $rangeMasklen = $request->input( 'protocol' ) == '4' ? 'min:16|max:29' : 'min:32|max:64';

        $validator = Validator::make( $request->all(), [
            'vlanid'                => 'required|integer|exists:Entities\Vlan,id',
            'protocol'              => 'required|integer|in:' . implode( ',', array_keys( RouterEntity::$PROTOCOLS ) ),
            'network'               => 'required|max:255|ipv' . $request->input( 'protocol' ) ,
            'masklen'               => 'required|integer|' . $rangeMasklen ,
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( NetworkInfoEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new NetworkInfoEntity;
            D2EM::persist( $this->object );
        }

        $vlan = D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlanid' ) );

        if( $ni = D2EM::getRepository( NetworkInfoEntity::class )->findOneBy( [ "Vlan" => $vlan->getId() , "protocol" => $request->input( 'protocol' ) ] ) ) {
            if( $this->object->getId() != $ni->getId() ) {
                AlertContainer::push( "Network information for this vlan (" . $vlan->getName() . ") and and protocol (ipv" . $request->input( 'protocol' ) . ") already exists. Please edit that instead."   , Alert::DANGER );
                return Redirect::back()->withErrors( $validator )->withInput();
            }
        }

        $this->object->setVlan(         $vlan );
        $this->object->setProtocol(     $request->input( 'protocol' ) );
        $this->object->setNetwork(      $request->input( 'network'  ) );
        $this->object->setMasklen(      $request->input( 'masklen'  ) );
        $this->object->setRs1address(       null );
        $this->object->setRs2address(       null );
        $this->object->setDnsfile(             null );

        D2EM::flush($this->object);

        return true;
    }

}
