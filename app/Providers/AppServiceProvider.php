<?php

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Providers;

use Auth, Former;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use IXP\Models\{
    DocstoreCustomerDirectory,
    DocstoreDirectory
};
use IXP\Observers\DocstoreCustomerDirectoryObserver;
use IXP\Observers\DocstoreDirectoryObserver;
use IXP\Utils\Former\Framework\TwitterBootstrap4;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * App Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        view()->composer( [ 'telescope::layout' ], function ( $view ) {
            $view->with( 'telescopeScriptVariables', [
                'path'      => config( 'telescope.url_path' ),
                'timezone'  => config('app.timezone'),
                'recording' => !cache('telescope:pause-recording'),
            ]);
        });

        Former::framework( TwitterBootstrap4::class );
        // observer for docstore directory
        DocstoreDirectory::observe( DocstoreDirectoryObserver::class );
        // observer for docstore customer directory
        DocstoreCustomerDirectory::observe( DocstoreCustomerDirectoryObserver::class );

        Paginator::useBootstrap();

        RateLimiter::for('2fa-authenticate', function ( Request $request ) {
            // These limits are applied to the 2fa-authenticate POST endpoint.
            // When successful, the user is redirected away. If unsuccessful, there's no
            // redirect and the user is shown the form to try again.
            $identifier = request()->user()?->getAuthIdentifier() ?? ixp_get_client_ip();

            return [
                Limit::perMinute( 5, decayMinutes: 2 )
                    ->by( '2fa:by-user:' . $identifier )
                    ->after( function ( SymfonyResponse $response): bool {
                        // Only count against the quota when authentication did NOT succeed. If the middleware receives
                        // an invalid token, it doesn't redirect it just displays the challenge form
                        return !$response->isRedirect();
                    } )
                    ->response( function ( Request $request, array $headers ): RedirectResponse {
                        $retryAfter = $headers['Retry-After'] ?? 120;
                        return redirect()
                            ->to( '/' )
                            ->withErrors( [
                                'one_time_password' => "Too many failed 2FA attempts. Please try again in {$retryAfter} seconds.",
                            ] );
                    } ),
            ];
        });
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    #[\Override]
    public function register(): void
    {

    }
}
