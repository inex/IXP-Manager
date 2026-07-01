<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, Former, Hash, Redirect, Route, Str;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Models\{ApiKey, AppPassword, User};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * ApiKey Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiKeyController extends EloquentController
{
    /**
     * The object being created / updated
     *
     * @var ApiKey
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
    public static $minimum_privilege = User::AUTH_CUSTUSER;

    /**
     * This function sets up the frontend controller
     */
    #[\Override]
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => ApiKey::class,
            'pagetitle'         => 'API Keys',
            'titleSingular'     => 'API Key',
            'nameSingular'      => 'API key',
            'listOrderBy'       => 'created_at',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'api-key',
            'documentation'     => 'https://docs.ixpmanager.org/latest/features/api/',
            'listColumns'    => [
                'id'           => [ 'title' => 'UID', 'display' => false ],
                'api_key'      => [
                    'title'        => 'API Key',
                    'type'         => self::$FE_COL_TYPES[ 'LIMIT' ],
                    'limitTo'      => 6
                ],
                'created_at'   => [
                    'title'        => 'Created',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'expires'      => [
                    'title'        => 'Expires',
                    'type'         => self::$FE_COL_TYPES[ 'DATE' ]
                ],
                'last_seen_at' => [
                    'title'        => 'Last Seen',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'last_seen_from' => 'Last Seen From'
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = [
            'id'               => [ 'title' => 'UID', 'display' => false ],
            'api_key'          => [
                'title'          => 'API Key',
                'type'           => self::$FE_COL_TYPES[ 'LIMIT' ],
                'limitTo'        => 6
            ],
            'token_identifier' => 'Token Identifier',
            'description'      => 'Description',
            'created_at'       => [
                'title'          => 'Created',
                'type'           => self::$FE_COL_TYPES[ 'DATETIME' ]
            ],
            'expires'          => [
                'title'          => 'Expires',
                'type'           => self::$FE_COL_TYPES[ 'DATE' ]
            ],
            'last_seen_at'     => [
                'title'          => 'Last Seen',
                'type'           => self::$FE_COL_TYPES[ 'DATETIME' ]
            ],
            'last_seen_from' => 'Last Seen From'
        ];

        // phpunit / artisan trips up here without the cli test:
        if( PHP_SAPI !== 'cli' ) {
            /** @var User $user */
            $user = Auth::getUser();
            // custom access controls:
            switch( Auth::check() ? $user->privs() : User::AUTH_PUBLIC ) {
                case User::AUTH_SUPERUSER:
                case User::AUTH_CUSTUSER || User::AUTH_CUSTADMIN:
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
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    #[\Override]
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return ApiKey::where( 'user_id', Auth::id() )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('id', $id );
            } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->get()->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return ApiKey[]
     *
     * @psalm-return array{object: ApiKey}
     */
    #[\Override]
    protected function createPrepareForm(): array
    {
        return [
            'object'          => $this->object
        ];
    }

    /**
     * Display the form to edit an object
     *
     * @param int $id ID of the row to edit
     *
     * @return array
     *
     * @psalm-return array{object: mixed}
     */
    #[\Override]
    protected function editPrepareForm( int $id ): array
    {
        $this->object = ApiKey::whereId($id)->whereUserId( Auth::id() )->firstOrFail();
        
        Former::populate( [
            'description'       => request()->old( 'description',       $this->object->description ),
        ] );

        return [
            'object'          => $this->object,
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return RedirectResponse|true
     *
     * @throws
     */
    #[\Override]
    public function doStore( Request $r )
    {
        if( $r->user()->apiKeys()->count() >= config( 'ixp_fe.api_keys.max_keys' ) ) {
            AlertContainer::push( "We currently have a limit of " . config( 'ixp_fe.api_keys.max_keys' ) . " API keys per user. Please contact us if you require more.", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        $this->checkForm( $r );

        // modern API key format:

        // ixpm_ident1234567_sec87654321098765432109876543210crc321
        // └──┘ └──────────┘ └──────────────────────────────┘└────┘
        // Prefix   Identifier                 Secret          Checksum
        // (4 ch)    (12 ch)                   (32 ch)          (6 ch)

        $identifier = Str::random(12); // Base62 string
        $secret     = Str::random(32); // Base62 string

        // 1. Build the payload string
        $payload = ApiKey::PREFIX . "{$identifier}_{$secret}";

        // 2. Calculate CRC32 (ensuring it's an unsigned integer format)
        $checksum = base62_encode( crc32( $payload ) );

        // 3. Assemble the final raw token given to the user
        $rawToken = $payload . $checksum;

        $this->object = new ApiKey;

        $this->object->user_id          = $r->user()->id;

        $this->object->token_identifier = $identifier;
        $this->object->token_hash       = hash( 'sha256', $secret );

        // this needs to be removed in the future, max 12 months from v7.3 release
        $this->object->api_key          = null;

        $this->object->expires          = $r->expires;
        $this->object->description      = $r->description;
        $this->object->save();

        AlertContainer::push( "API key created: <code>" . $rawToken . "</code>", Alert::SUCCESS );
        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return true
     *
     * @throws
     */
    #[\Override]
    public function doUpdate( Request $r, int $id )
    {
        $this->object = ApiKey::findOrFail( $id );

        if( $this->object->user_id !== $r->user()->id ) {
            abort( 403, 'Unauthorized' );
        }

        $r->validate( [
            'description'        => 'required|string|max:255',
        ] );

        $this->object->description = $r->description;
        $this->object->save();
        return true;
    }


    /**
     * Check if the form is valid
     *
     * @param $r
     *
     * @return void
     */
    #[\Override]
    public function checkForm( Request $r ): void
    {
        $max_duration = config('ixp_fe.api_keys.max_expires_duration' );
        $max_date = now()->add($max_duration)->format('Y-m-d');

        $r->validate( [
            'description'        => 'required|string|max:255',
            'expires'            => 'required|date|after:' . now()->format( "Y-m-d" ) . '|before_or_equal:' . $max_date,
        ] );
    }
}
