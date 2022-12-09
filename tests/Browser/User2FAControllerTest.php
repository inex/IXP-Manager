<?php

namespace Tests\Browser;

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

use IXP\Models\User2FA;
use Laravel\Dusk\Browser;

use PragmaRX\Google2FALaravel\Google2FA;

use Tests\DuskTestCase;

/**
 * Test User2FA Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class User2FAControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        if( $u2fa = User2FA::whereUserId( 1 )->first() ) {
            $u2fa->delete();
        }

        $this->deleteEnvValue( '2FA_ENFORCE_FOR_USERS="1"' );

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws
     */
    public function test(): void
    {
        $this->browse( function ( Browser $browser) {
            //$this->deleteEnvValue( ["2FA_ENFORCE_FOR_USERS" => 1] );

            $userUsername = 'travis';
            $userPassword = 'travisci';

            $browser->visit('/login')
                ->type('username', $userUsername )
                ->type('password', $userPassword )
                ->press('#login-btn'    )
                ->assertPathIs('/admin' );

            $browser->click('#my-account')
                    ->click("#profile")
                    ->assertPathIs('/profile');

            $browser->click("#configue-2fa")
                    ->assertPathIs('/2fa/configure');

            // Check if the 2FA object has been created
            $u2fa = User2FA::whereUserId( 1 )->first();

            $this->checkOtpAndPassword( $browser, $userPassword, $u2fa );

            /**
             * Logout and test that OTP is required
             */
            $browser->click('#my-account')
                    ->click("#logout")
                    ->assertPathIs('/login');

            $browser->visit('/login')
                    ->type('username', $userUsername)
                    ->type('password', $userPassword)
                    ->press('#login-btn')
                    ->assertSee('Enter the one time code from your authenticator app');

            /**
             * Try to access to a page without typing the OTP
             */
            $browser->visit('/customer/list')
                    ->assertSee('Enter the one time code from your authenticator app');

            /**
             * Try wrong OTP
             */
            $browser->type('one_time_password', 'wrongOTP')
                    ->press('Authenticate')
                    ->assertPathIs('/2fa/authenticate')
                    ->assertSee('The one time password entered was wrong.');

            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $u2fa->secret );

            /**
             * Try good OTP
             */
            $browser->type('one_time_password', $otp)
                ->press('Authenticate')
                ->assertDontSee('The one time password entered was wrong.')
                ->assertPathIs('/admin');

            /**
             * Trying disable 2FA with wrong password
             */
            $browser->click('#my-account')
                ->click("#profile")
                ->assertPathIs('/profile');

            $browser->click("#configue-2fa")
                ->assertPathIs('/2fa/configure')
                ->type('password', 'wrongPassword')
                ->press('Disable 2FA')
                ->assertSee('Incorrect user password - please check your password and try again.');

            /**
             * Trying disable 2FA with good password
             */
            $browser->type('password', $userPassword)
                ->press('Disable 2FA')
                ->assertPathIs('/profile')
                ->assertSee('2FA successfully disabled.');

            /**
             * Logout and set .env to force user to create 2fa
             */
            $browser->click('#my-account')
                ->click("#logout")
                ->assertPathIs('/login');

            $this->overrideEnv( ["2FA_ENFORCE_FOR_USERS" => 1] );

            $browser->visit('/login')
                    ->type('username', $userUsername)
                    ->type('password', $userPassword)
                    ->press('#login-btn')
                    ->assertPathIs('/2fa/configure')
                    ->assertSee('You do not have two-factor authentication enabled but it is compulsory for your user account. Please configure and enable 2fa below to proceed.');

            // Check if the 2FA object has been created
            $u2fa2 = User2FA::whereUserId( 1 )->first();

            $this->checkOtpAndPassword( $browser, $userPassword, $u2fa2 );
        });
    }

    /**
     * Test the form that enable the 2FA, checking that the OTP and Password is valid
     *
     * @param Browser       $browser
     * @param string        $userPassword
     * @param User2FA       $u2fa
     *
     * @return void
     * @throws
     */
    private function checkOtpAndPassword( Browser $browser, string $userPassword, User2FA $u2fa ): void
    {
        // Assert object is type User2FAEntity
        $this->assertInstanceOf(User2FA::class, $u2fa );

        $this->assertFalse( (bool)$u2fa->enabled  );
        $this->assertNotNull( $u2fa->secret );

        $browser->assertSee( $u2fa->secret  );

        $google2FA = new Google2FA( request() );
        $otp = $google2FA->getCurrentOtp( $u2fa->secret );

        /**
         * Test to enable the 2FA with wrong OTP and wrong Password
         */
        $browser->type( '#one_time_password', 'wrongOTP' )
                ->type( 'password', 'wrongPassword')
                ->press( 'Enable 2FA' )
                ->assertPathIs( '/2fa/configure')
                ->assertSee( 'Incorrect user password - please check your password and try again.');

        /**
         * Test to enable the 2FA with wrong OTP and good Password
         */
        $browser->type( '#one_time_password', 'wrongOTP' )
                ->type( 'password', $userPassword )
                ->press( 'Enable 2FA' )
                ->assertPathIs( '/2fa/configure')
                ->assertSee( 'Incorrect one time code - please check your code and try again.');

        /**
         * Test to enable the 2FA with good OTP and wrong Password
         */
        $browser->type( '#one_time_password', $otp )
                ->type( 'password', 'wrongPassword')
                ->press( 'Enable 2FA' )
                ->assertPathIs( '/2fa/configure')
                ->assertSee( 'Incorrect user password - please check your password and try again.');

        /**
         * Test to enable the 2FA with good OTP and wrong Password
         */
        $browser->type( '#one_time_password', $otp )
                ->type( 'password', $userPassword )
                ->press( 'Enable 2FA' )
                ->assertPathIs( '/admin')
                ->assertSee( '2FA successfully enabled.');

        //Check 2fa is enabled
        $u2fa->refresh();
        $this->assertTrue( (bool)$u2fa->enabled );
    }
}