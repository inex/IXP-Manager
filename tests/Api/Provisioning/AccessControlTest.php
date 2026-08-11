<?php

namespace Tests\Api\Provisioning;

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

use IXP\Http\Middleware\Provisioning\RestrictSourceAddress;
use Tests\TestCase;

/**
 * The guards around the provisioning endpoints.
 *
 * These are the only endpoints in IXP Manager which let an external caller create business
 * objects, so what keeps them shut matters as much as what they do.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AccessControlTest extends TestCase
{
    private const string URL = '/admin/api/v4/provisioning/ping';

    /**
     * Reach into the address matcher without going through the HTTP stack: the request IP is
     * awkward to vary in a feature test, and the logic is worth testing directly.
     */
    private function permits( string $source, string $list ): bool
    {
        $method = new \ReflectionMethod( RestrictSourceAddress::class, 'permitted' );
        $method->setAccessible( true );

        return $method->invoke( new RestrictSourceAddress(), $source, $list );
    }

    // ------------------------------------------------------------- the key requirement

    /**
     * A logged-in superuser's browser must not reach these endpoints on its session cookie.
     *
     * This is the point of RequireApiKey. `apibase` starts a session and ApiAuthenticate only
     * looks for a key when Auth::check() is false - so without this guard, any page able to
     * make an administrator's browser issue a request could create a member. There is no CSRF
     * token in the way either, because the group deliberately omits `web`.
     */
    public function test_a_browser_session_is_not_accepted_instead_of_a_key(): void
    {
        $this->actingAs( $this->getSuperUser() );

        $response = $this->get( self::URL );

        $response->assertStatus( 401 );
        $response->assertJsonPath( 'message', fn( $m ) => str_contains( $m, 'API key' ) );
    }

    public function test_a_key_is_still_accepted(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get( self::URL )
            ->assertStatus( 200 );
    }

    /**
     * The guard can be switched off for an installation which wants the old behaviour.
     */
    public function test_the_key_requirement_can_be_disabled(): void
    {
        config( [ 'ixp_api.provisioning.require_api_key' => false ] );

        $this->actingAs( $this->getSuperUser() );

        // Now the session is enough - ApiAuthenticate accepts an already-authenticated user.
        $this->get( self::URL )->assertStatus( 200 );
    }

    // ------------------------------------------------------------- source addresses

    public function test_an_empty_allow_list_permits_everything(): void
    {
        $this->assertTrue( $this->permits( '192.0.2.10', '' ) );
        $this->assertTrue( $this->permits( '2001:db8::1', '   ' ) );
    }

    public function test_a_single_address_matches_exactly(): void
    {
        $this->assertTrue(  $this->permits( '192.0.2.10', '192.0.2.10' ) );
        $this->assertFalse( $this->permits( '192.0.2.11', '192.0.2.10' ) );
    }

    public function test_several_entries_are_alternatives(): void
    {
        $list = '192.0.2.10, 198.51.100.5 ,203.0.113.7';

        $this->assertTrue(  $this->permits( '198.51.100.5', $list ) );
        $this->assertFalse( $this->permits( '198.51.100.6', $list ) );
    }

    public function test_ipv4_cidr_ranges(): void
    {
        $this->assertTrue(  $this->permits( '198.51.100.1',   '198.51.100.0/24' ) );
        $this->assertTrue(  $this->permits( '198.51.100.255', '198.51.100.0/24' ) );
        $this->assertFalse( $this->permits( '198.51.101.1',   '198.51.100.0/24' ) );

        // a boundary which needs the partial-byte mask to be right
        $this->assertTrue(  $this->permits( '192.0.2.127', '192.0.2.0/25' ) );
        $this->assertFalse( $this->permits( '192.0.2.128', '192.0.2.0/25' ) );
    }

    public function test_ipv6_cidr_ranges(): void
    {
        $this->assertTrue(  $this->permits( '2001:db8:0:1::5', '2001:db8::/32' ) );
        $this->assertFalse( $this->permits( '2001:db9::5',     '2001:db8::/32' ) );

        // notation must not matter
        $this->assertTrue( $this->permits( '2001:0db8:0000:0001:0000:0000:0000:0005', '2001:db8::/32' ) );
    }

    /**
     * An IPv4 address must never match an IPv6 range or the reverse - the packed forms differ
     * in length, and a naive comparison would produce a false positive.
     */
    public function test_address_families_do_not_cross(): void
    {
        $this->assertFalse( $this->permits( '192.0.2.1',  '2001:db8::/32' ) );
        $this->assertFalse( $this->permits( '2001:db8::1', '192.0.2.0/24' ) );
    }

    public function test_a_malformed_entry_does_not_match(): void
    {
        $this->assertFalse( $this->permits( '192.0.2.1', 'not-an-address' ) );
        $this->assertFalse( $this->permits( '192.0.2.1', '192.0.2.0/99' ) );
        $this->assertFalse( $this->permits( '192.0.2.1', '192.0.2.0/-1' ) );
    }

    /**
     * A malformed entry must not open the door for everything else either.
     */
    public function test_a_malformed_entry_alongside_a_valid_one(): void
    {
        $this->assertTrue(  $this->permits( '192.0.2.1', 'nonsense, 192.0.2.0/24' ) );
        $this->assertFalse( $this->permits( '203.0.113.1', 'nonsense, 192.0.2.0/24' ) );
    }

    // ------------------------------------------------------------- the feature flag

    /**
     * The routes exist only because the test configuration switches the feature on.
     *
     * There is no way to assert the "off" case from inside the suite - routes are registered
     * once at boot - so this asserts the inverse: that the flag is what put them there, and
     * that an installation leaving it alone has nothing to secure.
     */
    public function test_the_feature_flag_is_what_registers_the_routes(): void
    {
        $this->assertTrue(
            (bool)config( 'ixp_api.provisioning.enabled' ),
            'die Testumgebung muss die Provisioning-API einschalten, sonst prüft diese Suite nichts'
        );

        $this->assertTrue( \Route::has( 'api-v4-provisioning@ping' ) );
    }

    /**
     * Without the environment variable the feature must stay off.
     *
     * Read straight from the config file with the variable removed, because the test
     * environment sets it - and an installation which never heard of this feature must not
     * end up with endpoints that create members.
     */
    public function test_the_default_is_off(): void
    {
        $previous = getenv( 'IXP_API_PROVISIONING_ENABLED' );

        putenv( 'IXP_API_PROVISIONING_ENABLED' );
        unset( $_ENV[ 'IXP_API_PROVISIONING_ENABLED' ], $_SERVER[ 'IXP_API_PROVISIONING_ENABLED' ] );

        try {
            $config = require base_path( 'config/ixp_api.php' );

            $this->assertFalse(
                $config[ 'provisioning' ][ 'enabled' ],
                'die Provisioning-API muss ohne ausdrückliche Freischaltung aus sein'
            );
        } finally {
            if( $previous !== false ) {
                putenv( "IXP_API_PROVISIONING_ENABLED={$previous}" );
                $_ENV[ 'IXP_API_PROVISIONING_ENABLED' ] = $previous;
            }
        }
    }
}
