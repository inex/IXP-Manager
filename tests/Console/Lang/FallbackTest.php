<?php

namespace Tests\Console\Lang;

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

use Tests\TestCase;

/**
 * The whole translation approach rests on one property: anything not
 * translated falls back to English rather than rendering a key, an empty
 * string, or an error.
 *
 * That is what makes it safe to wrap a string in __() before anyone has
 * translated it, and what makes a stale catalogue after an upstream upgrade a
 * cosmetic problem rather than an outage. These tests pin that behaviour down
 * for both kinds of translation file.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    Tests\Console\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FallbackTest extends TestCase
{
    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        app()->setLocale( config( 'app.locale' ) );

        parent::tearDown();
    }

    /**
     * A JSON key with no translation renders as the English source.
     */
    public function testUntranslatedJsonStringFallsBackToItsEnglishSource(): void
    {
        app()->setLocale( 'fr' );

        $english = 'A string that is deliberately not in lang/fr.json';

        $this->assertSame( $english, __( $english ) );
    }

    /**
     * ... and a translated one does not.
     */
    public function testTranslatedJsonStringIsTranslated(): void
    {
        app()->setLocale( 'fr' );

        $this->assertSame( 'Langue', __( 'Language' ) );
    }

    /**
     * Placeholders are still substituted in a translated string.
     */
    public function testPlaceholdersAreSubstituted(): void
    {
        app()->setLocale( 'fr' );

        $this->assertSame( 'Utiliser la langue par défaut (English)',
            __( 'Use the default (:language)', [ 'language' => 'English' ] ) );
    }

    /**
     * lang/fr/*.php is deliberately partial. Laravel must fall back per key to
     * lang/en/*.php, so a rule we have not translated shows Laravel's English
     * message rather than the raw key.
     */
    public function testPartialGroupFileFallsBackPerKey(): void
    {
        app()->setLocale( 'fr' );

        // present in lang/fr/validation.php
        $this->assertSame( 'Le champ langue est obligatoire.',
            trans( 'validation.required', [ 'attribute' => 'langue' ] ) );

        // a nested key that is present
        $this->assertSame( 'Le texte de nom ne peut pas dépasser 30 caractères.',
            trans( 'validation.max.string', [ 'attribute' => 'nom', 'max' => 30 ] ) );

        // NOT present in lang/fr/validation.php - must give the English text,
        // and in particular must not return the key 'validation.accepted_if'
        $accepted = trans( 'validation.accepted_if', [ 'attribute' => 'x', 'other' => 'y', 'value' => 'z' ] );

        $this->assertStringNotContainsString( 'validation.', $accepted );
        $this->assertStringContainsString( 'must be accepted', $accepted );
    }

    /**
     * English must be completely unaffected by the presence of a French
     * catalogue - this is the regression guard for the whole approach.
     */
    public function testEnglishIsUnchanged(): void
    {
        app()->setLocale( 'en' );

        $this->assertSame( 'Language', __( 'Language' ) );
        $this->assertSame( 'The language field is required.',
            trans( 'validation.required', [ 'attribute' => 'language' ] ) );
    }
}
