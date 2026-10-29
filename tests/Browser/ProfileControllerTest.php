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

use IXP\Models\User;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DuskTestCase;

class ProfileControllerTest extends DuskTestCase
{
    public static function getChangePasswordTestUsers(): array
    {
        return [
            ['username' => 'travis',       'currentPassword' => 'travisci'],
            ['username' => 'imcustadmin',  'currentPassword' => 'travisci'],
            ['username' => 'imcustuser',   'currentPassword' => 'travisci'],
        ];
    }

    /**
     * Tests user change password feature
     * @throws \Throwable
     */
    #[DataProvider('getChangePasswordTestUsers')]
    public function testChangePasswordForm(string $username, string $currentPassword): void
    {
        $this->browse( function( Browser $browser1, Browser $browser2 ) use ($username, $currentPassword): void {

            $browser2->resize( 1600, 1400 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', $username )
                ->type( 'password', $currentPassword )
                ->check('remember')
                ->press( '#login-btn' );

            $user = User::where('username', $username)->first();

            $browser1->resize( 1600, 1400 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', $username )
                ->type( 'password', $currentPassword )
                ->press( '#login-btn' );

            if ($user->isSuperUser()) {
                $browser1->waitForLocation( '/admin/dashboard' );
            } else {
                $browser1->waitForLocation( '/dashboard' );
            }

            $browser1->click('#my-account')
                ->click('#profile')
                ->waitForLocation( route('profile@edit') )
                ->assertSee("My Profile")
                ->assertSee("Change Your Password");

            $this->assertEquals("true", $browser1->element('#current_password')->getAttribute('required'));
            $this->assertEquals("255", $browser1->element('#current_password')->getAttribute('maxlength'));
            $this->assertEquals("true", $browser1->element('#new_password')->getAttribute('required'));
            $this->assertEquals("255", $browser1->element('#new_password')->getAttribute('maxlength'));
            $this->assertEquals("true", $browser1->element('#confirm_password')->getAttribute('required'));
            $this->assertEquals("255", $browser1->element('#confirm_password')->getAttribute('maxlength'));

            $browser1
                ->type("#current_password",   "wrongpassword")
                ->type("#new_password",       "H510p@9.Pz[z")
                ->type("#confirm_password",   "H510p@9.Pz[z")
                ->press("Update Password")
                ->waitForLocation( route('profile@edit') )
                ->assertSee("The current password is incorrect.");

            $browser1
                ->type("#current_password",  $currentPassword)
                ->type("#new_password",      "H510p@9.Pz[z")
                ->type("#confirm_password",  "H510p@9.Pz[z1111111")
                ->press("Update Password")
                ->waitForLocation( route('profile@edit') )
                ->assertSee("The confirm password and new password must match.");

            $browser1
                ->type("#current_password",  "travisci")
                ->type("#new_password",      "H510p@9.Pz[z")
                ->type("#confirm_password",  "H510p@9.Pz[z")
                ->press("Update Password")
                ->waitForLocation( route('profile@edit') )
                ->assertSee("Password updated.");

            $user->refresh();
            $this->assertTrue(password_verify("H510p@9.Pz[z", $user->password));

            $browser2->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee("Your login session has expired or was terminated from another device. Please log in again.");
        } );
    }

    /**
     * Tests member notes settings for super user
     * @throws \Throwable
     */
    public function testMemberNotesSuperUser(): void
    {
        $this->browse( function( Browser $browser ): void {
            $user = User::where('username', 'travis')->first();

            $browser->resize( 1600, 1400 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->click('#my-account')
                ->click('#profile')
                ->waitForLocation( route('profile@edit') )
                ->assertSee("My Profile")
                ->assertSee(ucfirst(config('ixp_fe.lang.customer.one') . " Notes"));

            $this->assertNull($user->prefs, "DB seed user has no prefs");
            $browser->assertChecked('#notify-none', "The controller populates form with 'none' selected if no preferences");
            $browser->assertNotChecked('#notify-watched');
            $browser->assertNotChecked('#notify-all');

            // Select: all
            $browser->click('#notify-all')
                ->press('Set Notification Preference')
                ->waitForLocation( route('profile@edit') )
                ->assertSee('Notification preference updated.');

            $user->refresh();
            $this->assertUserGlobalNotifs( 'all', $user );

            // Select: none
            $browser->click('#notify-none')
                ->press('Set Notification Preference')
                ->waitForLocation( route('profile@edit') )
                ->assertSee('Notification preference updated.');

            $user->refresh();
            $this->assertUserGlobalNotifs( 'none', $user );

            $browser->assertChecked('#notify-none');

            // Select: watched
            $browser->click('#notify-watched')
                ->press('Set Notification Preference')
                ->waitForLocation( route('profile@edit') )
                ->assertSee('Notification preference updated.');

            $user->refresh();
            $this->assertUserGlobalNotifs( 'watched', $user );
        } );
    }

    /**
     * cust user can't doesn't get member notes preferences form
     * @throws \Throwable
     */
    public function testMemberNotesCustUser(): void
    {
        $this->browse( function( Browser $browser ): void {
            $browser->resize( 1600, 1400 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustuser' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );

            $browser->click('#my-account')
                ->click('#profile')
                ->waitForLocation( route('profile@edit') )
                ->assertSee("My Profile")
                ->assertDontSee(ucfirst(config('ixp_fe.lang.customer.one') . " Notes"));
        } );
    }

    /**
     * cust admin can't doesn't get member notes preferences form
     * @throws \Throwable
     */
    public function testMemberNotesCustAdmin(): void
    {
        $this->browse( function( Browser $browser ): void {
            $browser->resize( 1600, 1400 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustuser' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );

            $browser->click('#my-account')
                ->click('#profile')
                ->waitForLocation( route('profile@edit') )
                ->assertSee("My Profile")
                ->assertDontSee(ucfirst(config('ixp_fe.lang.customer.one') . " Notes"));
        } );
    }


    /**
     * Test updating own profile
     * @throws \Throwable
     */
    public function testUpdateYourProfile(): void
    {
        $this->browse( function( Browser $browser ): void {
            $browser->resize( 1600, 1400 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->click('#my-account')
                ->click('#profile')
                ->waitForLocation( route('profile@edit') )
                ->assertSee("My Profile")
                ->assertSee("Update Your Profile");

            $user = User::where('username', 'travis')->first();

            $this->assertEquals($user->username, $browser->element("#infos input[name=username]")->getAttribute("value"));
            $this->assertEquals($user->name, $browser->element("#infos input[name=name]")->getAttribute("value"));
            $this->assertEquals($user->email, $browser->element("#infos input[name=email]")->getAttribute("value"));
            $this->assertEquals($user->authorisedMobile, $browser->element("#infos input[name=authorisedMobile]")->getAttribute("value"));

            $browser->type("username", "vagrant-rules")
                ->type("name", "Vagrant Is The Best")
                ->type("email", "vagrant@vagrant.local")
                ->type("authorisedMobile", "07777777777")
                ->type("actual_password", "travisci")
                ->press("Update Profile");

            $browser
                ->waitForLocation( route('profile@edit') )
                ->assertSee("Profile details updated.");

            $user->refresh();
            $this->assertEquals("vagrant-rules", $user->username);
            $this->assertEquals("Vagrant Is The Best", $user->name);
            $this->assertEquals("vagrant@vagrant.local", $user->email);
            $this->assertEquals("07777777777", $user->authorisedMobile);

            $this->assertEquals($user->username, $browser->element("#infos input[name=username]")->getAttribute("value"));
            $this->assertEquals($user->name, $browser->element("#infos input[name=name]")->getAttribute("value"));
            $this->assertEquals($user->email, $browser->element("#infos input[name=email]")->getAttribute("value"));
            $this->assertEquals($user->authorisedMobile, $browser->element("#infos input[name=authorisedMobile]")->getAttribute("value"));

        } );
    }

    private function assertUserGlobalNotifs( string $preference , User $user ): void
    {
        $this->assertNotNull( $user->prefs );
        $this->assertArrayHasKey( 'notes', $user->prefs );
        $this->assertArrayHasKey( 'global_notifs', $user->prefs['notes'] );
        $this->assertEquals($preference, $user->prefs['notes']['global_notifs'] );
    }
}