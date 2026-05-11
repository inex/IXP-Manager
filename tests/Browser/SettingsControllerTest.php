<?php

namespace Tests\Browser;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;
use Throwable;

/**
 * Settings Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SettingsControllerTest extends DuskTestCase
{
    
    /**
     * The expected .env file contents before we go messing with it
     * @var string|null
     */
    private ?string $test_env = null;

    /**
     * The user's existing .env file that we want to preserve
     * @var string|null
     */
    private ?string $user_env = null;


    /**
     * @throws
     */
    public function setUp(): void
    {
        $this->user_env = file_get_contents( __DIR__ . '/../../.env' );
        $this->test_env = file_get_contents( __DIR__ . '/../../.env.ci' );

        file_put_contents( __DIR__ . '/../../.env', $this->test_env );
        usleep( 1_000_000 );
        parent::setUp();
    }

    /**
     * @throws
     */
    public function tearDown(): void
    {
        file_put_contents( __DIR__ . '/../../.env', $this->user_env );
        usleep( 1_000_000 );
        parent::tearDown();
    }
    
    /**
     * @throws Throwable
     */
    public function testSettings(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit( route( 'settings@index' ) )
                ->assertSee( 'IXP Manager Settings' );
            
            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );
            
            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'settings@index' ) );
            
            // ensure a save with no changes yields no changes in the .env file
            $this->assertEquals( $this->test_env, file_get_contents( __DIR__ . '/../../.env' ));
            
            // change some values to check validation
            $browser->press( 'Route Servers' )
                ->waitForText( 'Minimum IPv4 Subnet Size' )
                ->type( 'rs_rpki_rtr1_port', 'this_is_not_an_integer' )
                ->driver->executeScript( 'window.scrollTo(0, 3000);' );
            
            $browser->press( 'Save Changes' )
                ->waitForText( 'There were validation errors with your settings' )
                ->assertSee( 'The rs rpki rtr1 port must be an integer' );
            
            // ensure still no changes
            $this->assertEquals( $this->test_env, file_get_contents( __DIR__ . '/../../.env' ));
            
            // change some values to check validation
            $browser->press( 'Route Servers' )
                ->waitForText( 'Minimum IPv4 Subnet Size' )
                ->type( 'rs_rpki_rtr1_port', 12345 )
                ->driver->executeScript( 'window.scrollTo(0, 3000);' );
            
            $browser->press( 'Save Changes' )
                ->waitForText( 'Settings have been successfully updated' );
            
            $nenv = file_get_contents( __DIR__ . '/../../.env' );
            
            // now we have a change
            $this->assertNotEquals( $this->test_env, $nenv );

            // we substituted 3323 for 12345
            $this->assertEquals( strlen( $nenv ), strlen( $this->test_env ) + 1 );
            
            $this->assertStringContainsString( 'IXP_RPKI_RTR1_PORT=3323', $this->test_env );
            $this->assertStringNotContainsString( 'IXP_RPKI_RTR1_PORT=12345', $this->test_env );
            
            $this->assertStringNotContainsString( 'IXP_RPKI_RTR1_PORT=3323', $nenv );
            $this->assertStringContainsString( 'IXP_RPKI_RTR1_PORT=12345', $nenv );
            
        } );
    }
}