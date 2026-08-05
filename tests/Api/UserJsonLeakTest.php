<?php

namespace Tests\Api;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use IXP\Models\User;
use Tests\TestCase;

/**
 * A serialised user must never carry the password hash.
 *
 * `GET admin/api/v4/user/json` answered with `User::byPrivs()->get()->toArray()`, and the
 * model declared no $hidden - so the endpoint returned the bcrypt hash of every user of every
 * customer. It sits in routes/apiv4-ext-auth-superuser.php: reachable with an API key, with
 * no CSRF token in the way.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserJsonLeakTest extends TestCase
{
    public function test_the_user_endpoint_does_not_return_password_hashes(): void
    {
        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get( '/admin/api/v4/user/json' );

        $response->assertStatus( 200 );

        $users = $response->json();

        $this->assertNotEmpty( $users, 'ohne Benutzer beweist dieser Test nichts' );

        foreach( $users as $user ) {
            $this->assertArrayNotHasKey( 'password', $user );
        }

        // and not anywhere in the raw body either - a differently named key would still leak
        $this->assertStringNotContainsString( '$2y$', $response->getContent(), 'bcrypt-Hash im Antworttext' );
    }

    /**
     * The guard belongs on the model, so that anything serialising a user is covered - not
     * only the one endpoint which was found to leak.
     */
    public function test_serialising_a_user_never_includes_the_password(): void
    {
        $user = User::firstOrFail();

        $this->assertNotEmpty( $user->password, 'der Testbenutzer hat kein Passwort gesetzt' );

        $this->assertArrayNotHasKey( 'password', $user->toArray() );
        $this->assertStringNotContainsString( '$2y$', $user->toJson() );
        $this->assertStringNotContainsString( '$2y$', json_encode( [ 'user' => $user ] ) );
    }

    /**
     * Direct access must keep working: authentication reads it through getAuthPassword(), and
     * the console server and RADIUS templates under resources/views/api/v4/user/formatted/
     * emit it deliberately. $hidden affects serialisation only - this asserts that boundary.
     */
    public function test_direct_access_to_the_password_still_works(): void
    {
        $user = User::firstOrFail();

        $this->assertNotEmpty( $user->password );
        $this->assertSame( $user->password, $user->getAuthPassword() );
    }

    /**
     * The formatted endpoint exists to hand password hashes to console servers. It must not
     * have been broken by hiding the attribute.
     */
    public function test_the_formatted_endpoint_still_emits_what_it_is_for(): void
    {
        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get( '/admin/api/v4/user/formatted' );

        $response->assertStatus( 200 );
        $this->assertNotEmpty( $response->getContent() );
    }
}
