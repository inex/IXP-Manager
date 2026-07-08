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

use Auth, DB, Former, Hash, Redirect, Route, Str;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Random\RandomException;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Models\{
    AppPassword,
    User
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

/**
 * AppPassword Controller
 * @category   IXP
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AppPasswordController extends EloquentController
{
    /**
     * The object being created / updated
     *
     * @var AppPassword
     */
    protected $object = null;
    
    protected static bool $is_admin_route = true;
    
    /**
     * The minimum privileges required to access this controller.
     *
     * @var int
     */
    public static $minimum_privilege = User::AUTH_SUPERUSER;

    /**
     * This function sets up the frontend controller
     */
    #[\Override]
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => AppPassword::class,
            'pagetitle'         => 'Application-Specific Passwords',
            'titleSingular'     => 'Application-Specific Password',
            'nameSingular'      => 'app password',
            'documentation'     => 'https://docs.ixpmanager.org/latest/features/app-passwords/',
            'listOrderBy'       => 'created_at',
            'listOrderByDir'    => 'DESC',
            'viewFolderName'    => 'app-password',
            'listColumns'    => [
                'id'           => [ 'title' => 'UID', 'display' => false ],
                'user'  => [
                    'title'      => 'User',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'user',
                    'action'     => 'view',
                    'idField'    => 'user_id',
                ],
                'description'  => [
                    'title'        => 'Description',
                    'type'         => self::$FE_COL_TYPES[ 'TEXT' ],
                ],
                'expires'      => [
                    'title'        => 'Expires',
                    'type'         => self::$FE_COL_TYPES[ 'DATE' ],
                ],
                'last_seen_at'   => [
                    'title'        => 'Last Seen At',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ],
                ],
                'last_seen_from'   => [
                    'title'        => 'Last Seen From',
                    'type'         => self::$FE_COL_TYPES[ 'TEXT' ],
                ],
            ],
        ];
        
        if( !( (bool)request()->query( 'all', 0 ) ) ) {
            unset( $this->feParams->listColumns['user'] );
        }
        
        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge( $this->feParams->listColumns, [
            'created_at'      => [
                'title'        => 'Created At',
                'type'         => self::$FE_COL_TYPES[ 'DATETIME' ],
            ],
        ] );

        // phpunit / artisan trips up here without the cli test:
        if( PHP_SAPI !== 'cli' ) {
            /** @var User $user */
            $user = Auth::getUser();
            // custom access controls:
            switch( Auth::check() ? $user->privs() : User::AUTH_PUBLIC ) {
                case User::AUTH_SUPERUSER:
                    break;
                
                default:
                    $this->unauthorized();
            }
        }
    }

    /**
     * Additional routes
     *
     * @param string $route_prefix
     *
     * @return void
     */
    #[\Override]
    protected static function additionalRoutes( string $route_prefix ): void
    {
        Route::group( [  'prefix' => 'admin/' . $route_prefix ], static function() use ( $route_prefix ) {
            Route::get(   'history/{id}',            [self::class, 'history'] )->name( $route_prefix . '@history' );
        });
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
        
        return AppPassword::where( 'user_id', Auth::id() )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('id', $id );
            } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->get()->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return AppPassword[]
     *
     * @psalm-return array{object: AppPassword}
     */
    #[\Override]
    protected function createPrepareForm(): array
    {
        return [
            'object'          => $this->object,
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
        $this->object = AppPassword::whereId($id)->whereUserId( Auth::id() )->firstOrFail();

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
     *
     * @return RedirectResponse|true
     * @throws RandomException
     */
    #[\Override]
    public function doStore( Request $r )
    {
        if( $r->user()->appPasswords()->count() >= config( 'ixp_fe.app_passwords.max_passwords' ) ) {
            AlertContainer::push( "We currently have a limit of " . config( 'ixp_fe.app_passwords.max_passwords' ) . " app passwords per user. Please contact us if you require more.", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        $this->checkForm( $r );
        
        $this->object = new AppPassword;
        $pass = $this->generateReadablePassword();
        
        if( $r->algorithm === 'sha256' ) {
            $this->object->salt     = bin2hex( random_bytes( 32 ) );
            $this->object->password = hash( 'sha256', $pass . $this->object->salt );
        } else {
            $this->object->salt     = null;
            $this->object->password = Hash::driver($r->algorithm)->make($pass);
        }
        
        $this->object->expires     = $r->expires;
        $this->object->description = $r->description;
        $this->object->user_id     = $r->user()->id;
        $this->object->save();

        AlertContainer::push( "App password created: <code>" . $pass . "</code><br><br>"
            . "<b>NB: this is the only time you will be able to see this password.</b>", Alert::SUCCESS );
        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     * @param int $id
     *
     * @return true
     */
    #[\Override]
    public function doUpdate( Request $r, int $id )
    {
        $this->object = AppPassword::findOrFail( $id );

        if( $this->object->user_id !== $r->user()->id ) {
            abort( 403, 'Unauthorized' );
        }

        $r->validate( [
            'description'        => 'required|string|max:255',
        ] );
        
        // $validatedData ONLY contains what we validated above
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
        $max_duration = config('ixp_fe.app_passwords.max_expires_duration' );
        $max_date = now()->add($max_duration)->format('Y-m-d');
        
        $algo = $r->input( 'algorithm', config('ixp_fe.app_passwords.encryption.default_algorithm' ) );
        if( !array_key_exists( $algo, config('ixp_fe.app_passwords.encryption.available_algorithms' ) )
            || !config( 'ixp_fe.app_passwords.encryption.user_can_change' )
        ) {
            $algo = config('ixp_fe.app_passwords.encryption.default_algorithm' );
        }
        
        $r->merge( [ 'algorithm' => $algo ] );
        
        $r->validate( [
            'description'        => 'required|string|max:255',
            'expires'            => 'required|date|after:' . now()->format( "Y-m-d" ) . '|before_or_equal:' . $max_date,
        ] );
    }
    
    
    /**
     * Generates a secure, highly readable password.
     * @param int $numBlocks Number of character groupings (default: 4)
     * @param int $blockSize Number of characters per group (default: 4)
     * @param string $delimiter The separator between blocks (default: '-')
     * @return string The generated password
     */
    private function generateReadablePassword( int $numBlocks = 4, int $blockSize = 4, string $delimiter = '-' ): string
    {
        // A customized alphabet excluding confusing characters:
        // Excluded: 0, o, O (zeros / ohs)
        // Excluded: 1, i, I, l, L (ones / eyes / ells)
        $allowedChars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $maxIndex = strlen( $allowedChars ) - 1;
        $passwordBlocks = [];
        
        for( $i = 0; $i < $numBlocks; $i++ ) {
            $block = '';
            for( $j = 0; $j < $blockSize; $j++ ) {
                // random_int is cryptographically secure
                $randomIndex = random_int( 0, $maxIndex );
                $block .= $allowedChars[ $randomIndex ];
            }
            $passwordBlocks[] = $block;
        }
        
        return implode( $delimiter, $passwordBlocks );
    }


    /**
     * Show login history for a specific app password
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function history( Request $r, int $id): View|RedirectResponse
    {
        $this->object = AppPassword::where('id', $id )->firstOrFail();

        if( $this->object->user_id !== $r->user()->id ) {
            abort( 403, 'Unauthorized' );
        }

        $this->listIncludeTemplates();
        
        $history = DB::table('app_passwords_last_logins')
            ->where('app_password_id', $id)
            ->orderBy('last_seen_at', 'DESC')
            ->get()->toArray();

        return view('app-password.history', [
            'object'   => $this->object,
            'feParams' => $this->feParams,
            'data'     => [ 'rows' => $history ],
        ]);
    }
}
