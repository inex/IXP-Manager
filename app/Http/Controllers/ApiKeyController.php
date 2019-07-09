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

use Auth, D2EM, Former, Redirect, Validator;

use Entities\{
    ApiKey  as ApiKeyEntity,
    User    as UserEntity
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\{
    Request,
    RedirectResponse
};



/**
 * ApiKey Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiKeyController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var ApiKeyEntity
     */
    protected $object = null;

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = UserEntity::AUTH_CUSTUSER;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->feParams         = (object)[

            'entity'            => ApiKeyEntity::class,
            'pagetitle'         => 'API Keys',

            'titleSingular'     => 'API Key',
            'nameSingular'      => 'an API key',

            'listOrderBy'       => 'created',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'api-key',

            'documentation'     => 'https://docs.ixpmanager.org/features/api/',

            'listColumns'    => [

                'id'           => [ 'title' => 'UID', 'display' => false ],
                'apiKey'       => [
                    'title'        => 'API Key',
                    'type'         => env( "IXP_FE_SECURITY_SHOW_API_KEYS" ) ? self::$FE_COL_TYPES[ 'TEXT' ] : self::$FE_COL_TYPES[ 'LIMIT' ],
                    'limitTo'      => 6
                ],

                'description'   => 'Description',

                'created'      => [
                    'title'        => 'Created',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'expires'      => [
                    'title'        => 'Expires',
                    'type'         => self::$FE_COL_TYPES[ 'STRING_TO_DATE' ]
                ],
                'lastseenAt'   => [
                    'title'        => 'Lastseen',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'lastseenFrom' => 'Lastseen From'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = $this->feParams->listColumns;

        // phpunit / artisan trips up here without the cli test:
        if( php_sapi_name() !== 'cli' ) {
            // custom access controls:
            switch( Auth::check() ? Auth::user()->getPrivs() : UserEntity::AUTH_PUBLIC ) {
                case UserEntity::AUTH_SUPERUSER:
                case UserEntity::AUTH_CUSTUSER || UserEntity::AUTH_CUSTADMIN:
                    break;

                default:
                    $this->unauthorized();
                    break;
            }

        }

    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     * @return array
     */
    protected function listGetData( $id = null ) {
        return D2EM::getRepository( ApiKeyEntity::class )->getAllForFeList( $this->feParams, Auth::user()->getId() );
    }

    /**w
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     */
    protected function addEditPrepareForm( $id = null ): array {
        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( ApiKeyEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            $old = request()->old();

            Former::populate([
                'key'               => array_key_exists( 'key',             $old ) ? $old['key']            : $this->object->getApiKey(),
                'description'       => array_key_exists( 'description',     $old ) ? $old['description']    : $this->object->getDescription(),
                'expires'           => array_key_exists( 'expires',         $old ) ? $old['expires']        : $this->object->getExpires() ? $this->object->getExpires()->format('Y-m-d') : null
            ]);
        }

        return [
            'object'          => $this->object,
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
        if( count( Auth::user()->getApiKeys() ) >= 10 ) {
            AlertContainer::push( "We currently have a limit of 10 API keys per user. Please contact us if you require more.", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        $validator = Validator::make( $request->all(), [
            'description'        => 'nullable|string|max:255',
            'expires'            => 'nullable|date',]
        );

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( ApiKeyEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new ApiKeyEntity;
            D2EM::persist( $this->object );
            $this->object->setUser(         Auth::user()    );
            $this->object->setCreated(      new \DateTime   );
            $this->object->setAllowedIPs(''    );
            $this->object->setLastseenFrom(''  );
            $this->object->setApiKey(  $key = str_random(48)  );
            Auth::user()->addApiKey( $this->object );
            AlertContainer::push( "Following your new API Key, keep it safe it will be the only time that you will be able to see it - <code>" . $key . "</code>", Alert::SUCCESS );
        }

        $this->object->setExpires(      new \DateTime( $request->input( 'expires' ) ) );
        $this->object->setDescription(  $request->input( 'description' )    );

        D2EM::flush($this->object);

        return true;
    }

}
