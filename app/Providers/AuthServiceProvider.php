<?php
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

declare(strict_types=1);

namespace IXP\Providers;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use IXP\Services\Auth\EloquentUserProvider;
use IXP\Services\Auth\SessionGuard;

/**
 * Auth Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // autoload model policies
        Gate::guessPolicyNamesUsing( function ( string|object $modelClass ): string {
            return 'IXP\\Policies\\' . class_basename( $modelClass ) . 'Policy';
        });

        Auth::extend('session', function ( Application $app, string $name, array $config ): SessionGuard {
            $provider = $app[ 'auth' ]->createUserProvider( $config['provider'] ?? null );

            $guard = new SessionGuard( $name, $provider, $app[ 'session.store' ], request() );

            if( $config[ 'expire' ] ) {
                $guard->setRememberDuration( $config[ 'expire' ] );
            }

            if( method_exists( $guard, 'setCookieJar' ) ) {
                $guard->setCookieJar( $app['cookie'] );
            }

            if ( method_exists( $guard, 'setDispatcher' ) ) {
                $guard->setDispatcher( $app['events'] );
            }

            if (method_exists( $guard, 'setRequest' ) ) {
                $guard->setRequest( $app->refresh( 'request', $guard, 'setRequest' ) );
            }

            return $guard;
        });

        Auth::provider('eloquent', function ( Application $app, array $config ): EloquentUserProvider {
            return new EloquentUserProvider( $app['hash'], $config['model'] );
        } );
    }
}
