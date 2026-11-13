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

namespace Tests\Http\Requests\User;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Request as RequestFacade;
use IXP\Http\Requests\User\ResetTwoFactorLockoutRequest;
use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use Tests\TestCase;

class ResetTwoFactorLockoutRequestTest extends TestCase
{
    /**
     * Provide custom configuration early so 2FA routes are registered
     */
    public function postBootstrapLoadConfigurationHook( Application $app ): void
    {
        $app['config']->set( [ 'google2fa.enabled' => true ] );
    }

    public function testAuthorize(): void
    {
        // no params here, auth user is superuser..
        $request = new ResetTwoFactorLockoutRequest();
        $request->setUserResolver( fn() => $this->getSuperUser() );
        $this->assertTrue( $request->authorize() );

        // no params here, auth user is custuser..
        $request = new ResetTwoFactorLockoutRequest();
        $request->setUserResolver( fn() => $this->getCustUser() );
        $this->assertFalse( $request->authorize() );

        // no params here, auth user is null..
        $request = new ResetTwoFactorLockoutRequest();
        $request->setUserResolver( fn() => null );
        $this->assertFalse( $request->authorize() );

        // custadmin, custuser in same organization
        $request = $this->requestFor($this->getCustAdminUser(), $this->getCustUser());
        $this->assertTrue( $request->authorize() );

        // custadmin, custadmin in same organization
        $anotherCustAdmin = $this->linkedToCustomer($this->anotherUser(), $this->getCustAdminUser()->customer, User::AUTH_CUSTADMIN);
        $request = $this->requestFor($this->getCustAdminUser(), $anotherCustAdmin);
        $this->assertTrue( $request->authorize() );

        // custadmin, superadmin in same organization
        $anotherSuperUser = $this->linkedToCustomer($this->anotherUser(), $this->getCustAdminUser()->customer, User::AUTH_SUPERUSER);
        $request = $this->requestFor($this->getCustAdminUser(), $anotherSuperUser);
        $this->assertFalse( $request->authorize() );

        // custadmin, custuser in different organization
        $request = $this->requestFor($this->getCustAdminUser(), User::whereUsername('hecustuser')->first());
        $this->assertFalse( $request->authorize() );
    }

    private function anotherUser(): User
    {
        $user = new User();
        $user->save();
        return $user;
    }

    private function linkedToCustomer(User $user, Customer $cust, int $privs): User
    {
        $c2u = new CustomerToUser();
        $c2u->user_id = $user->id;
        $c2u->customer_id = $cust->id;
        $c2u->privs = $privs;
        $c2u->save();
        return $user;
    }

    private function requestFor( User $actor, User $subject ): ResetTwoFactorLockoutRequest
    {
        $route = ( new Route( 'POST', route('2fa@reset-lockout-state', ['user' => $subject->id] ) , [] ) )
            ->bind( RequestFacade::create( route('2fa@reset-lockout-state', [ 'user' => $subject->id] ), 'POST' ) );
        $route->setParameter( 'user', $subject );   // the bound model, as route model binding would give

        $request = new ResetTwoFactorLockoutRequest();
        $request->setUserResolver( fn() => $actor );
        $request->setRouteResolver( fn() => $route );

        return $request;
    }
}