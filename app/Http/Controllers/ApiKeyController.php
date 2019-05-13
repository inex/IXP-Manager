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

use Auth, D2EM, Redirect, Route;

use Entities\{
    ApiKey      as ApiKeyEntity,
    User        as UserEntity
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\{
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
                'apiKey'       => 'API Key',
                'created'      => [
                    'title'        => 'Created',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'expires'      => [
                    'title'        => 'Expires',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
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
     * @inheritdoc
     */
    public static function routes() {
        Route::group( [ 'prefix' => 'api-key' ], function() {
            Route::get(  'list',    'ApiKeyController@list'     )->name( 'api-key@list'     );
            Route::get(  'add',     'ApiKeyController@add'      )->name( 'api-key@add'      );
            Route::post( 'delete',  'ApiKeyController@delete'   )->name( 'api-key@delete'   );
        });
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

    /**
     * Add Api Key to the current user
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function add() : RedirectResponse {
        if( count( Auth::user()->getApiKeys() ) >= 10 ) {
            AlertContainer::push( "We currently have a limit of 10 API keys per user. Please contact us if you require more.", Alert::DANGER );
            return Redirect::back();
        }

        $key = new ApiKeyEntity;

        $key->setUser(          Auth::user()    );
        $key->setCreated(       new \DateTime   );
        $key->setAllowedIPs(    ''              );
        $key->setExpires(       null            );
        $key->setLastseenFrom(  ''              );
        $key->setApiKey(        str_random(48)  );

        D2EM::persist( $key );
        Auth::user()->addApiKey( $key );
        D2EM::flush();

        AlertContainer::push( "Your new API key has been created - <code>" . $key->getApiKey() . "</code>", Alert::SUCCESS );
        return Redirect::back();
    }

}
