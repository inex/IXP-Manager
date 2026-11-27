<?php
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\Browser;

use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use IXP\Models\User2FA;
use IXP\Models\UserRememberToken;
use Laravel\Dusk\Browser;

use PragmaRX\Google2FALaravel\Google2FA;

use Tests\DuskTestCase;
use Tests\Trait\ModifiesEnv;

/**
 * Test User2FA Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class User2FAControllerTest extends DuskTestCase
{
    use ModifiesEnv;

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->replaceEnvAttr( '2FA_ENFORCE_FOR_USERS="1"', '2FA_ENFORCE_FOR_USERS="4"' );
        $this->replaceEnvAttr( '2FA_ENABLED=false', '2FA_ENABLED=true' );
        // changing the environment causes the server to restart
        // Environment modified. Restarting server...
        $this->awaitArtisanEnvReload();

    }

    /**
     * Test workflows involving the User2FAController
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function test(): void
    {
        $this->browse( function ( Browser $browser) {

            $userUsername = 'travis';
            $userPassword = 'travisci';

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit('/login')
                ->waitForLocation('/login')
                ->type('username', $userUsername )
                ->type('password', $userPassword )
                ->press('#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit('/profile')
                    ->assertPathIs('/profile');

            $browser->click("#configue-2fa")
                    ->waitForLocation('/2fa/configure');

            // Check if the 2FA object has been created
            $u2fa = User2FA::whereUserId( 1 )->first();

            $this->checkOtpAndPassword( $browser, $userPassword, $u2fa );

            /**
             * Logout and test that OTP is required
             */
            $browser->visit('/logout')
                    ->waitForLocation('/login');

            $browser->visit('/login')
                    ->waitForLocation('/login')
                    ->type('username', $userUsername)
                    ->type('password', $userPassword)
                    ->press('#login-btn')
                    ->waitForLocation('/2fa/authenticate')
                    ->waitForText('Enter the one time code from your authenticator app');

            /**
             * Try to access to a page without typing the OTP. This page will be marked
             * as url.intended, and after completing 2FA we'll be redirected there.
             */
            $browser->visit(route( 'customer@list' ) )
                    ->waitForLocation('/2fa/authenticate')
                    ->assertSee('Enter the one time code from your authenticator app');

            /**
             * Form performs validation - 6 characters required
             */
            $browser->type('one_time_password', 'wrongOTP')
                ->press('Authenticate')
                ->waitForLocation('/2fa/authenticate')
                ->assertSee('The one time password must be 6 characters.');

            /**
             * Try wrong OTP (has to be 6 characters)
             */
            $browser->type('one_time_password', '6chars')
                    ->press('Authenticate')
                    ->waitForLocation('/2fa/authenticate')
                    ->assertSee('The one time password entered was wrong.');

            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $u2fa->secret );

            /**
             * Try good OTP
             */
            $browser->type('one_time_password', $otp)
                ->press('Authenticate')
                ->waitForRoute( 'customer@list' )
                ->assertDontSee('The one time password entered was wrong.');


            /**
             * Trying disable 2FA with wrong password
             */
            $browser->visit('/profile')
                ->assertPathIs('/profile');

            $browser->click("#configue-2fa")
                ->waitForLocation('/2fa/configure')
                ->type('password', 'wrongPassword')
                ->press('Disable 2FA')
                ->waitForText('Incorrect user password - please check your password and try again.');

            /**
             * Trying disable 2FA with good password
             */
            $browser->type('password', $userPassword)
                ->press('Disable 2FA')
                ->waitForLocation('/profile')
                ->assertSee('2FA successfully disabled.');

            /**
             * Logout and set .env to force user to create 2fa
             */
            $browser->visit('/logout')
                ->waitForLocation('/login');

            //$this->overrideEnv( ["2FA_ENFORCE_FOR_USERS" => 1] );
            $this->replaceEnvAttr( '2FA_ENFORCE_FOR_USERS="4"','2FA_ENFORCE_FOR_USERS="1"' );
            // changing the environment causes the server to restart
            // Environment modified. Restarting server...
            sleep(2);

            $browser->visit('/login')
                    ->type('username', $userUsername )
                    ->type('password', $userPassword )
                    ->press('#login-btn' )
                    ->waitForLocation('/2fa/configure')
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

        $this->assertFalse( $u2fa->enabled  );
        $this->assertFalse( $u2fa->isEnabled()  );
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
                ->waitForLocation( '/2fa/configure')
                ->assertSee( 'Incorrect user password - please check your password and try again.',1);

        /**
         * Test to enable the 2FA with wrong OTP and good Password
         */
        $browser->type( '#one_time_password', 'wrongOTP' )
                ->type( 'password', $userPassword )
                ->press( 'Enable 2FA' )
                ->waitForLocation( '/2fa/configure')
                ->assertSee( 'Incorrect one time code - please check your code and try again.',1);

        /**
         * Test to enable the 2FA with good OTP and wrong Password
         */
        $browser->type( '#one_time_password', $otp )
                ->type( 'password', 'wrongPassword')
                ->press( 'Enable 2FA' )
                ->waitForLocation( '/2fa/configure')
                ->assertSee( 'Incorrect user password - please check your password and try again.',1);

        /**
         * Test to enable the 2FA with good OTP and good Password
         */
        $browser->type( '#one_time_password', $otp )
                ->type( 'password', $userPassword )
                ->press( 'Enable 2FA' )
                ->waitForLocation( '/admin/dashboard')
                ->assertSee( '2FA successfully enabled.',1);

        //Check 2fa is enabled
        $u2fa->refresh();
        $this->assertTrue( $u2fa->enabled );
        $this->assertTrue( $u2fa->isEnabled() );
    }


    /**
     * Test workflows involving the User2FAController
     *
     * @return void
     *
     * @throws
     */
    public function testWithRememberMe(): void
    {
        $this->browse( function ( Browser $browser) {

            $userUsername = 'travis';
            $userPassword = 'travisci';

            $user = User::whereUsername( $userUsername )->first();

            $this->assertCount( 0, UserRememberToken::all());
            $this->assertDatabaseMissing( 'user_remember_tokens', ['user_id' => $user->id] );

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit('/login')
                ->waitForLocation('/login')
                ->type('username', $userUsername )
                ->type('password', $userPassword )
                ->check('remember')
                ->press('#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $this->assertCount( 1, UserRememberToken::all());
            $this->assertDatabaseHas('user_remember_tokens', [
                'user_id' => $user->id,
                'is_2fa_complete' => false,
            ]);

            $browser->visit('/profile')
                ->assertPathIs('/profile');

            $browser->click("#configue-2fa")
                ->waitForLocation('/2fa/configure');

            // Check if the 2FA object has been created
            $u2fa = User2FA::whereUserId( 1 )->first();

            // enables 2FA:
            $this->checkOtpAndPassword( $browser, $userPassword, $u2fa );

            $this->assertCount( 1, UserRememberToken::all());
            $this->assertDatabaseHas('user_remember_tokens', [
                'user_id' => $user->id,
                'is_2fa_complete' => true,
            ]);

            /**
             * Logout and test that OTP is required
             */
            $browser->visit('/logout')
                ->waitForLocation('/login');

            $this->assertCount( 0, UserRememberToken::all());
            $this->assertDatabaseMissing('user_remember_tokens', [
                'user_id' => $user->id,
            ]);

            $browser->visit('/login')
                ->waitForLocation('/login')
                ->type('username', $userUsername)
                ->type('password', $userPassword)
                ->check('remember')
                ->press('#login-btn')
                ->waitForText('Enter the one time code from your authenticator app');

            $this->assertCount( 1, UserRememberToken::all());
            $this->assertDatabaseHas('user_remember_tokens', [
                'user_id' => $user->id,
                'is_2fa_complete' => false,
            ]);

            /**
             * Try to access to a page without typing the OTP. As 2FA is not complete,
             * the middleware will record this URL as url.intended and redirect there after 2FA.
             */
            $browser->visit(route( 'customer@list' ) )
                ->assertSee('Enter the one time code from your authenticator app');

            $browser->type('one_time_password', '6chars')
                ->press('Authenticate')
                ->waitForLocation('/2fa/authenticate')
                ->assertSee('The one time password entered was wrong.');

            $this->assertDatabaseHas('user_remember_tokens', [
                'user_id' => $user->id,
                'is_2fa_complete' => false,
            ]);

            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $u2fa->secret );

            /**
             * Try good OTP
             */
            $browser->type('one_time_password', $otp)
                ->press('Authenticate')
                ->waitForRoute( 'customer@list' )
                ->assertDontSee('The one time password entered was wrong.');

            $this->assertCount( 1, UserRememberToken::all());
            $this->assertDatabaseHas('user_remember_tokens', [
                'user_id' => $user->id,
                'is_2fa_complete' => true,
            ]);

            /**
             * Trying disable 2FA with wrong password
             */
            $browser->visit('/profile')
                ->assertPathIs('/profile');

            $browser->click("#configue-2fa")
                ->waitForLocation('/2fa/configure')
                ->type('password', 'wrongPassword')
                ->press('Disable 2FA')
                ->waitForText('Incorrect user password - please check your password and try again.');

            /**
             * Trying disable 2FA with good password
             */
            $browser->type('password', $userPassword)
                ->press('Disable 2FA')
                ->waitForLocation('/profile')
                ->assertSee('2FA successfully disabled.');

            /**
             * Logout and set .env to force user to create 2fa
             */
            $browser->visit('/logout')
                ->waitForLocation('/login');

            $this->assertCount( 0, UserRememberToken::all());
            $this->assertDatabaseMissing('user_remember_tokens', [
                'user_id' => $user->id,
            ]);

            //$this->overrideEnv( ["2FA_ENFORCE_FOR_USERS" => 1] );
            $this->replaceEnvAttr( '2FA_ENFORCE_FOR_USERS="4"','2FA_ENFORCE_FOR_USERS="1"' );
            // changing the environment causes the server to restart
            // Environment modified. Restarting server...
            sleep(2);

            $browser->visit('/login')
                ->type('username', $userUsername )
                ->type('password', $userPassword )
                ->press('#login-btn' )
                ->waitForLocation('/2fa/configure')
                ->assertPathIs('/2fa/configure')
                ->assertSee('You do not have two-factor authentication enabled but it is compulsory for your user account. Please configure and enable 2fa below to proceed.');

            // Check if the 2FA object has been created
            $u2fa2 = User2FA::whereUserId( 1 )->first();

            $this->checkOtpAndPassword( $browser, $userPassword, $u2fa2 );
        });
    }

    public function testDisabling2FAClearsRememberMe2faComplete(): void
    {
        $this->browse( function ( Browser $browser ) {

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit('/login')
                ->waitForLocation('/login')
                ->type('username', 'travis' )
                ->type('password', 'travisci' )
                ->check('remember')
                ->press('#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $user = User::whereUsername('travis')->first();
            $this->assertCount(1, $user->userRememberTokens);
            $token = $user->userRememberTokens[0];
            $this->assertFalse($token->is_2fa_complete);

            // enables 2FA:
            $browser->visit('/profile')
                ->assertPathIs('/profile')
                ->click("#configue-2fa")
                ->waitForLocation('/2fa/configure');

            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $user->user2FA->secret );

            $browser->type( '#one_time_password', $otp )
                ->type( 'password', 'travisci' )
                ->press( 'Enable 2FA' )
                ->waitForLocation( '/admin/dashboard')
                ->assertSee( '2FA successfully enabled.',1);

            // Session marked is_2fa_complete = true
            $token->refresh();
            $this->assertTrue($token->is_2fa_complete);

            // Disable 2FA - should reset is_2fa_complete
            $browser->visit('/profile')
                ->assertPathIs('/profile')
                ->click("#configue-2fa")
                ->waitForLocation('/2fa/configure')
                ->type('password', 'travisci')
                ->press('Disable 2FA')
                ->waitForLocation('/profile')
                ->assertSee('2FA successfully disabled.');

            $token->refresh();
            $this->assertFalse($token->is_2fa_complete);
        });
    }


    public function testSuperadminDisabling2FAClearsRememberMe2faComplete(): void
    {
        $this->browse( function ( Browser $browser0, Browser $browser1 ) {

            $imCustAdmin = User::whereUsername('imcustadmin')->first();
            $browser0->resize(1600, 1200)
                ->visit( '/logout' )
                ->visit('/login')
                ->waitForLocation('/login')
                ->type('username', 'imcustadmin' )
                ->type('password', 'travisci' )
                ->check('remember')
                ->press('#login-btn' )
                ->waitForLocation( '/dashboard' );

            $user = User::whereUsername('imcustadmin')->first();
            $this->assertCount(1, $user->userRememberTokens);
            $token = $user->userRememberTokens[0];
            $this->assertFalse($token->is_2fa_complete);

            // enables 2FA:
            $browser0->visit('/profile')
                ->assertPathIs('/profile')
                ->click("#configue-2fa")
                ->waitForLocation('/2fa/configure');

            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $user->user2FA->secret );

            $browser0->type( '#one_time_password', $otp )
                ->type( 'password', 'travisci' )
                ->press( 'Enable 2FA' )
                ->waitForLocation( '/dashboard')
                ->assertSee( '2FA successfully enabled.',1);

            // Session marked is_2fa_complete = true
            $token->refresh();
            $this->assertTrue($token->is_2fa_complete);

            // Super user deletes users 2FA, so is_2fa_complete should be reset
            $browser1->resize(1600, 1200)
                ->visit( '/logout' )
                ->visit('/login')
                ->waitForLocation('/login')
                ->type('username', 'travis' )
                ->type('password', 'travisci' )
                ->check('remember')
                ->press('#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser1->visit('/user/list')
                ->click('#d2f-more-options-' . $imCustAdmin->id)
                ->pause(100)
                ->click('#d2f-option-remove-2fa-' . $imCustAdmin->id)
                ->pause(100)
                ->waitForText('Do you really want to delete the 2FA for this user?')
                ->waitForText('Delete')
                ->press('Delete')
                ->waitForLocation('/user/list')
                ->assertSee('2FA successfully deleted for imcustadmin');

            $token->refresh();
            $this->assertFalse($token->is_2fa_complete);
        });
    }

    /**
     * Test end to end url.intended redirection handling when 2FA challenges happen
     *
     * @return void
     *
     * @throws
     */
    public function testUrlIntendedRedirectFlow(): void
    {
        $this->browse( function ( Browser $browser) {

            $userUsername = 'travis';
            $userPassword = 'travisci';

            $user = User::whereUsername( $userUsername )->first();

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit('/login')
                ->waitForLocation('/login')
                ->type('username', $userUsername )
                ->type('password', $userPassword )
                ->press('#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit('/profile')
                ->assertPathIs('/profile');

            $browser->click("#configue-2fa")
                ->waitForLocation('/2fa/configure');

            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $user->user2FA->secret );
            $browser->type( '#one_time_password', $otp )
                ->type( 'password', $userPassword )
                ->press( 'Enable 2FA' )
                ->waitForLocation( '/admin/dashboard')
                ->assertSee( '2FA successfully enabled.',1);

            /**
             * Logout and test that OTP is required
             */
            $browser->visit('/logout')
                ->waitForLocation('/login');

            $browser->visit('/admin/validation/start')
                ->waitForLocation('/login')
                ->type('username', $userUsername)
                ->type('password', $userPassword)
                ->press('#login-btn')
                ->waitForText('Enter the one time code from your authenticator app');

            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $user->user2FA->secret );

            $browser->type('one_time_password', $otp)
                ->press('Authenticate')
                ->waitForLocation( '/admin/validation/start' );
        } );
    }

    public function testReset2FALockoutState(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $targetUser = User::whereUsername('imcustadmin')->firstOrFail();
            $user2FA = User2FA::setupForUser($targetUser, 'secret');
            $user2FA->fail_count = config('auth.lockout.two_factor_attempts');
            $user2FA->save();

            $browser->visit('/user/list');
            $element = $browser->element('#lockout-state-' . $targetUser->id);
            $this->assertNotNull($element, "should have flag icon for lockout state");
            $this->assertEquals('2FA', $element->getText());

            $browser->click("#d2f-more-options-" . $targetUser->id)
                ->waitForText("Reset 2FA lockout state")
                ->click("#d2f-option-reset-2fa-lockout-state-" . $targetUser->id)
                ->waitForText("Do you really want to reset the 2FA failure count for this user?")
                ->press("Reset")
                ->waitForLocation("/user/list")
                ->assertSee("2FA lockout state reset for " . $targetUser->username);

            $targetUser->refresh();
            $this->assertEquals(0, $targetUser->password_fail_count);
        } );
    }

    public function testReset2FALockoutStateCustadmin(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );

            $targetUser = User::whereUsername('imcustuser')->firstOrFail();
            $user2FA = User2FA::setupForUser($targetUser, 'secret');
            $user2FA->fail_count = config('auth.lockout.two_factor_attempts');
            $user2FA->save();

            $browser->visit('/user/list');
            $element = $browser->element('#lockout-state-' . $targetUser->id);
            $this->assertNotNull($element, "should have flag icon for lockout state");
            $this->assertEquals('2FA', $element->getText());

            $browser->click("#d2f-more-options-" . $targetUser->id)
                ->waitForText("Reset 2FA lockout state")
                ->click("#d2f-option-reset-2fa-lockout-state-" . $targetUser->id)
                ->waitForText("Do you really want to reset the 2FA failure count for this user?")
                ->press("Reset")
                ->waitForLocation("/user/list")
                ->assertSee("2FA lockout state reset for " . $targetUser->username);

            $targetUser->refresh();
            $this->assertEquals(0, $targetUser->password_fail_count);
        } );
    }

    public function testCustAdminCannotReset2FAWhenSubjectHasMultipleCustomers(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );

            $targetUser = User::whereUsername('imcustuser')->firstOrFail();
            $user2FA = User2FA::setupForUser($targetUser, 'secret');
            $user2FA->fail_count = config('auth.lockout.two_factor_attempts');
            $user2FA->save();

            $otherCustomer = new Customer();
            $otherCustomer->save();

            $c2u = new CustomerToUser();
            $c2u->user_id = $targetUser->id;
            $c2u->customer_id = $otherCustomer->id;
            $c2u->privs = User::AUTH_CUSTUSER;
            $c2u->save();

            $browser->visit('/user/list');
            $element = $browser->element('#lockout-state-' . $targetUser->id);
            $this->assertNotNull($element, "should have flag icon for lockout state");
            $this->assertEquals('2FA', $element->getText());

            $browser->click("#d2f-more-options-" . $targetUser->id)
                ->waitForText("Reset 2FA lockout state")
                ->click("#d2f-option-reset-2fa-lockout-state-" . $targetUser->id)
                ->waitForText("Do you really want to reset the 2FA failure count for this user?")
                ->press("Reset")
                ->waitForLocation("/user/list")
                ->assertSee("{$targetUser->username} is associated with multiple customers - their 2FA can only be unlocked by support.");

            $targetUser->refresh();
            $this->assertEquals(0, $targetUser->password_fail_count);
        } );
    }

}