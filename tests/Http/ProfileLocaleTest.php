<?php

namespace Tests\Http;

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

use IXP\Models\User;

use Tests\TestCase;

/**
 * Tests for the per-user / per-customer user interface language preference.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    Tests\Http
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ProfileLocaleTest extends TestCase
{
    /**
     * Reset any preference left behind by a test so they do not leak into
     * each other or into the rest of the suite.
     */
    protected function tearDown(): void
    {
        $u = User::where( 'username', 'imcustuser' )->first();
        $u->setLocale( null );
        $u->save();

        $c = $u->customer;
        $c->setLocale( null );
        $c->save();

        parent::tearDown();
    }

    /**
     * The profile page should offer a language selector.
     */
    public function testProfilePageShowsLanguageSelector(): void
    {
        $this->actingAs( $this->getCustUser() );

        $this->get( route( 'profile@edit' ) )
            ->assertOk()
            ->assertSee( 'Set Language' )
            ->assertSee( 'name="locale"', false );
    }

    /**
     * Choosing a language should store it against the user.
     */
    public function testUserCanSetAndClearTheirLanguage(): void
    {
        $u = $this->getCustUser();
        $this->actingAs( $u );

        $this->post( route( 'profile@update-language' ), [ 'locale' => 'fr' ] )
            ->assertRedirect( route( 'profile@edit' ) );

        $this->assertSame( 'fr', $u->fresh()->locale() );

        // and clearing it again:
        $this->post( route( 'profile@update-language' ), [ 'locale' => '' ] )
            ->assertRedirect( route( 'profile@edit' ) );

        $this->assertNull( $u->fresh()->locale() );
    }

    /**
     * A locale we do not offer must be rejected.
     */
    public function testUnavailableLocaleIsRejected(): void
    {
        $u = $this->getCustUser();
        $this->actingAs( $u );

        $this->post( route( 'profile@update-language' ), [ 'locale' => 'xx' ] )
            ->assertSessionHasErrors( 'locale' );

        $this->assertNull( $u->fresh()->locale() );
    }

    /**
     * Setting a preference should actually change the language of the UI.
     */
    public function testChosenLanguageIsAppliedToTheUi(): void
    {
        $u = $this->getCustUser();
        $u->setLocale( 'fr' );
        $u->save();

        $this->actingAs( $u );

        $this->get( route( 'profile@edit' ) )
            ->assertOk()
            // from lang/fr.json:
            ->assertSee( 'Définir la langue' )
            ->assertSee( 'lang="fr"', false )
            ->assertDontSee( 'Set Language' );
    }

    /**
     * With no preference of their own, a user should get their customer's
     * default; their own preference must win over it.
     */
    public function testCustomerDefaultAppliesAndUserPreferenceWins(): void
    {
        $u = $this->getCustUser();
        $c = $u->customer;

        $c->setLocale( 'fr' );
        $c->save();

        $this->actingAs( $u );

        $this->get( route( 'profile@edit' ) )
            ->assertOk()
            ->assertSee( 'lang="fr"', false );

        // the user's own choice must take precedence:
        $u->setLocale( 'en' );
        $u->save();

        $this->get( route( 'profile@edit' ) )
            ->assertOk()
            ->assertSee( 'lang="en"', false )
            ->assertSee( 'Set Language' );
    }

    /**
     * Untranslated strings must fall back to the English source rather than
     * rendering a key or an empty string.
     */
    public function testUntranslatedStringsFallBackToEnglish(): void
    {
        $u = $this->getCustUser();
        $u->setLocale( 'fr' );
        $u->save();

        $this->actingAs( $u );

        // 'My Profile' is not in lang/fr.json and is not yet wrapped in __(),
        // so it must still appear, in English:
        $this->get( route( 'profile@edit' ) )
            ->assertOk()
            ->assertSee( 'My Profile' );
    }
}
