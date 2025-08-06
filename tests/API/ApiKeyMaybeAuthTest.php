<?php

namespace Tests\API;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Config;

use Tests\Services\Grapher\Graph\Access\Access;
use Tests\TestCase;

/**
 * Test access via API keys
 *
 * NB: MaybeAuth passes to the controller and the controller must handle what to do if auth'd/not auth'd.
 *
 * Class ApiKeyTest
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Tests\API
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiKeyMaybeAuthTest extends TestCase
{

    private const array TEST_KEYS = [
        // user id => key
        1 => 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9',
        2 => 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC8',
        3 => 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC7',
    ];

    public function testApiMaybeAuthAccessNoKey(): void
    {
        // this should be the default
        $response = $this->get( '/api/v4/test' );
        $response->assertStatus( 200 )
            ->assertSee( 'Authenticated: No' );
    }

    public function testApiMaybeAuthAccessKeyViaGet(): void
    {
        // this should be the default
        $response = $this->get( '/api/v4/test?apikey=' . self::TEST_KEYS[ 1 ] );
        $response->assertStatus( 200 )
            ->assertSee( 'Authenticated: Yes' );
    }

    public function testApiMaybeAuthAccessNoKeyViaGetJson(): void
    {
        // this should be the default
        $response = $this->get( '/api/v4/test?format=json' );
        $response->assertStatus( 200 )
            ->assertJson( [ 'authenticated' => false ] );
    }

    public function testApiMaybeAuthAccessKeyViaGetJson(): void
    {
        // this should be the default
        $response = $this->get( '/api/v4/test?format=json&apikey=' . self::TEST_KEYS[ 1 ] );
        $response->assertStatus( 200 )
            ->assertJson( [ 'authenticated' => true ] );
    }

    public function testApiMaybeAuthAccessNoKeyViaPost(): void
    {
        // this should be the default
        $response = $this->post( '/api/v4/test' );

        $response->assertStatus( 200 )
            ->assertSee( 'Authenticated: No' );
    }

    public function testApiMaybeAuthAccessKeyViaPost(): void
    {
        // this should be the default
        $response = $this->withHeaders([
                'X-IXP-Manager-API-Key' =>  self::TEST_KEYS[ 1 ]
            ])->post( '/api/v4/test' );

        $response->assertStatus( 200 )
            ->assertSee( 'Authenticated: Yes' );
    }



}