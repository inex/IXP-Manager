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

namespace Tests\Http\Middleware;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use IXP\Http\Middleware\Google2FA as Google2FAMiddleware;
use IXP\Models\User;
use IXP\Models\User2FA;
use PHPUnit\Framework\Attributes\DataProvider;
use PragmaRX\Google2FALaravel\Support\Constants;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Tests\TestCase;

class Google2FATest extends TestCase
{
    /** Data Provider with names of routes that are globally excluded from the 2FA redirect */
    public static function globallyExcludedRoutes(): array
    {
        return [
            ['routeName' => 'login@logout'],
            ['routeName' => '2fa@showForm'],
            ['routeName' => '2fa@authenticate'],
        ];
    }

    /** Data Provider with names of routes that are excluded when 2FA is not setup */
    public static function excludedRoutesWhen2FAIsNotSetup(): array
    {
        return [
            ['routeName' => 'login@logout'],
            ['routeName' => '2fa@showForm'],
            ['routeName' => '2fa@authenticate'],
            ['routeName' => '2fa@configure'],
            ['routeName' => '2fa@enable'],
        ];
    }

    /** Data Provider with names of routes which trigger 2FA redirect if 2FA is already setup */
    public static function notExcludedRoutesWhen2FAIsSetup(): array
    {
        return [
            ['routeName' => '2fa@configure'],
            ['routeName' => '2fa@enable'],
        ];
    }

    /** @var string Two Factor Secret for user generated during the test */
    private string $secret;

    /**
     * Provide custom configuration early so 2FA routes are registered
     */
    public function postBootstrapLoadConfigurationHook( Application $app ): void
    {
        $app['config']->set( [ 'google2fa.enabled' => true ] );
    }

    /**
     * When not logged in, the 2FA middleware will not redirect to the 2FA challenge.
     */
    public function testNotLoggedIn(): void
    {
        $request = RequestFacade::create('/');
        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * When logged in externally, the 2FA middleware should not redirect to the 2FA challenge.
     */
    public function testExternalAuthCompleted(): void
    {
        Auth::login( $this->getCustUser() );

        $request = $this->createRequestForNamedRoute('dashboard@index');
        $request->session()->put('ixpm_external_2fa_completed', true);

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test that these routes are globally excluded from the 2FA requirement (eg, logout), and do not redirect to the 2FA challenge.
     */
    #[DataProvider( 'globallyExcludedRoutes' )]
    public function testIsGloballyExcludedRoute(string $routeName): void
    {
        Auth::login( $this->userWith2fa( $this->getSuperUser() ) );

        $request = $this->createRequestForNamedRoute($routeName);

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test that -- when the user does not have 2FA setup -- that these routes are excluded from redirecting to the 2FA challenge.
     */
    #[DataProvider('excludedRoutesWhen2FAIsNotSetup')]
    public function testIsExcludedRouteDueToNo2FASetup(string $routeName): void
    {
        Auth::login( $this->getSuperUser() );

        $request = $this->createRequestForNamedRoute($routeName);

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test that -- when the user has 2FA setup -- that these routes WILL be redirected to the 2FA challenge
     */
    #[DataProvider('notExcludedRoutesWhen2FAIsSetup')]
    public function testIsNotExcludedWhen2FASetup(string $routeName): void
    {
        Auth::login( $this->userWith2fa( $this->getSuperUser() ) );

        $request = $this->createRequestForNamedRoute($routeName);

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/2fa/authenticate', $response->headers->get('Location'));
    }

    /**
     * When logged in via the switch user feature, the 2FA middleware should not redirect to the 2FA challenge.
     */
    public function testUsingSwitchFromUser(): void
    {
        Auth::login( $this->getSuperUser() );

        $request = $this->createRequestForNamedRoute('login@logout');
        $request->session()->put('switched_user_from', true);

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * If 2FA is required but the user hasn't set it up (and is not requesting an excluded route), the middleware
     * redirects them to the 2fa configure page.
     */
    public function testIs2FAEnforcedForUser(): void
    {
        Auth::login( $this->getSuperUser() );

        $request = $this->createRequestForNamedRoute('admin@dashboard');

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/2fa/configure', $response->headers->get('Location'));
    }

    /**
     * If 2FA is required but the user hasn't set it up, but their role is below the minimum mandatary 2FA level,
     * they are not redirected to the 2fa configure page.
     */
    public function testIs2FANotMandatoryForPrivilege(): void
    {
        Auth::login( $this->getCustUser() );
        config()->set('google2fa.ixpm_2fa_enforce_for_users', User::AUTH_CUSTADMIN);

        $request = $this->createRequestForNamedRoute('dashboard@index');

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * If 2FA is required but the user hasn't set it up, but their role is below the minimum mandatary 2FA level,
     * they are not redirected to the 2fa configure page.
     */
    public function testChallengeUserWith2FAButNotMandatory(): void
    {
        Auth::login( $this->userWith2fa( $this->getCustUser() ) );
        config()->set('google2fa.ixpm_2fa_enforce_for_users', User::AUTH_CUSTADMIN);

        $request = $this->createRequestForNamedRoute('dashboard@index');

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/2fa/authenticate', $response->headers->get('Location'));
    }

    /**
     * If a user has 2FA setup, and already completed the 2FA challenge, they are not redirected to the 2fa authenticate page.
     */
    public function testNoChallengeIfAlreadyCompleted2FA(): void
    {
        Auth::login( $this->userWith2fa( $this->getCustUser() ) );

        $request = $this->createRequestForNamedRoute('dashboard@index');
        $request->session()->put( config('google2fa.session_var') . "." . Constants::SESSION_AUTH_PASSED, true);

        $response = $this->sendRequestThroughMiddleware($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * If the user has 2FA setup, and has not completed the 2FA challenge, they are redirected to the 2fa authenticate page.
     */
    public function test2FAChallengeIfNotComplete(): void
    {
        Auth::login( $this->userWith2fa( $this->getCustUser() ) );

        $request = $this->createRequestForNamedRoute('dashboard@index');

        $middleware = new Google2FAMiddleware();
        $response = $middleware->handle($request, fn() => response('OK', 200));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/2fa/authenticate', $response->headers->get('Location'));
    }

    /**
     * Helper method for creating a request. Requests for this middleware need to have routes resolved (we use route
     * name to check if it's excluded from the 2FA check), and needs the session store also.
     */
    private function createRequestForNamedRoute( string $routeName, string $method = 'GET' ): Request
    {
        $request = RequestFacade::create(route($routeName));
        $request->setRouteResolver(function () use ($method, $request, $routeName) {
            $route = new Route($method, route($routeName), []);
            $route->bind($request);
            $route->name($routeName);
            return $route;
        });
        $request->setLaravelSession(app('session.store'));
        return $request;
    }

    /**
     * Helper method to deduplicate test boilerplate
     */
    private function sendRequestThroughMiddleware(Request $request): SymfonyResponse
    {
        $middleware = new Google2FAMiddleware();
        return $middleware->handle($request, fn() => response('OK', 200));
    }

    /**
     * Enable 2FA for the given user and return it
     */
    private function userWith2fa( User $user ): User
    {
        $this->secret = app( 'pragmarx.google2fa' )->generateSecretKey( 32 );
        User2FA::setupForUser( $user, $this->secret )->enable();
        return $user->fresh();
    }
}