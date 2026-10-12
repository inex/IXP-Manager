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

namespace Tests\Utils;

use Illuminate\Foundation\Testing\TestCase;
use IXP\Utils\CountryUtils;
use Webpatser\Countries\CountriesFacade;

class CountryUtilsTest extends TestCase
{
    /**
     * Check each key has a unique value for each country
     * @return void
     */
    public function testKeysHaveNoDuplicates(): void
    {
        $countries = CountriesFacade::getList();

        foreach ( CountryUtils::UNIQUE_KEYS as $key) {
            $mapped = array_column($countries, null, $key);

            $this->assertCount( count($countries), $mapped, "key $key is not unique per country and should not be in the VALID_KEYS list" );
        }
    }

    /**
     * Check each key is defined for each country
     * @return void
     */
    public function testKeyIsDefinedForEachCountry(): void
    {
        $countries = CountriesFacade::getList();

        $keyedByKeys = array_flip(CountryUtils::UNIQUE_KEYS);

        foreach ($countries as $country) {
            $missingKeys = array_diff_key($keyedByKeys, $country);

            $this->assertEmpty(
                $missingKeys,
                "Missing keys [" . implode(', ', array_keys($missingKeys)) . "] in country " . ($country['name'] ?? 'unknown')
            );
        }
    }

    public function testUnknownKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Provided key is not a unique country identifier: gibberish");
        CountryUtils::isKnownBy("x", "gibberish");
    }

    public function testIsKnownBy()
    {
        // Test null is not known
        $this->assertFalse(CountryUtils::isKnownBy(null));

        // Test default which is iso_3166_2
        $this->assertTrue(CountryUtils::isKnownBy("IE"));
        $this->assertTrue(CountryUtils::isKnownBy("YT"));
        $this->assertFalse(CountryUtils::isKnownBy("XX"));

        // Test iso_3166_2 explicitly
        $this->assertTrue(CountryUtils::isKnownBy("IE", "iso_3166_2"));
        $this->assertTrue(CountryUtils::isKnownBy("YT", "iso_3166_2"));
        $this->assertFalse(CountryUtils::isKnownBy("XX", "iso_3166_2"));

        // Test country-code
        $this->assertTrue(CountryUtils::isKnownBy("270", "country-code"));
        $this->assertTrue(CountryUtils::isKnownBy("276", "country-code"));
        $this->assertFalse(CountryUtils::isKnownBy("9999", "country-code"));

        // Test name
        $this->assertTrue(CountryUtils::isKnownBy("Guyana", "name"));
        $this->assertTrue(CountryUtils::isKnownBy("Nigeria", "name"));
        $this->assertFalse(CountryUtils::isKnownBy("SomeUnknownCountry", "name"));
    }
}