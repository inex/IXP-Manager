<?php
declare(strict_types=1);

namespace Tests\Utils\DotEnv;


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


use IXP\Exceptions\Utils\DotEnvInvalidSettingException;
use IXP\Utils\DotEnv\DotEnvContainer;
use PHPUnit\Framework\TestCase;

final class DotEnvContainerTest extends TestCase
{
    private function makeContainer(array $settings = []): DotEnvContainer
    {
        return new DotEnvContainer($settings);
    }

    public function testSettingsGetterAndSetter(): void
    {
        $initial = [
            ['key' => 'FOO', 'value' => 'bar', 'comment' => null],
        ];
        $c = $this->makeContainer($initial);
        $this->assertSame($initial, $c->settings());

        $new = [
            ['key' => 'X', 'value' => '1', 'comment' => 'c'],
        ];
        $c->setSettings($new);
        $this->assertSame($new, $c->settings());
    }

    public function testGetIssetIndexOf(): void
    {
        $settings = [
            ['key' => 'ALPHA', 'value' => 'a', 'comment' => null],
            ['key' => 'BETA',  'value' => 42,  'comment' => 'num'],
            ['key' => 'GAMMA', 'value' => true, 'comment' => null],
        ];

        $c = $this->makeContainer($settings);

        $this->assertTrue($c->isset('ALPHA'));
        $this->assertTrue($c->isset('BETA'));
        $this->assertTrue($c->isset('GAMMA'));
        $this->assertFalse($c->isset('DELTA'));

        $this->assertSame('a', $c->getValue('ALPHA'));
        $this->assertSame(42, $c->getValue('BETA'));
        $this->assertSame(true, $c->getValue('GAMMA'));
        $this->assertNull($c->getValue('DELTA'));

        $this->assertSame(0, $c->indexOf('ALPHA'));
        $this->assertSame(1, $c->indexOf('BETA'));
        $this->assertSame(2, $c->indexOf('GAMMA'));
        $this->assertNull($c->indexOf('DELTA'));
    }

    public function testUnsetReturnsSliceAndMutates(): void
    {
        $settings = [
            ['key' => 'K1', 'value' => 'v1', 'comment' => null],
            ['key' => 'K2', 'value' => 'v2', 'comment' => null],
            ['key' => 'K3', 'value' => 'v3', 'comment' => null],
            ['key' => 'K4', 'value' => 'v4', 'comment' => null],
        ];

        $c = $this->makeContainer($settings);

        $removed = $c->unset('K2');

        $this->assertCount(1, $removed);
        $this->assertSame([['key' => 'K2', 'value' => 'v2', 'comment' => null]], $removed);

        // Confirms container settings are changed and reindexed
        $this->assertFalse($c->isset('K2'));
        $this->assertTrue($c->isset('K3'));
        $this->assertSame(1, $c->indexOf('K3'));
    }
    
    /**
     * @throws DotEnvInvalidSettingException
     */
    public function testSetWithValidKeyStoresParsedValue(): void
    {
        $c = $this->makeContainer();

        // "true" should become boolean true via DotEnvParser::parseValue
        $c->set('BOOL_TRUE', 'true' );
        $this->assertTrue($c->isset('BOOL_TRUE'));
        $this->assertSame(true, $c->getValue('BOOL_TRUE'));

        // integer string should become int
        $c->set('PORT', '8080', 'port');
        $this->assertSame(8080, $c->getValue('PORT'));

        // single word stays unquoted string
        $c->set('NAME', 'alpha', '# c');
        $this->assertSame('alpha', $c->getValue('NAME'));

        // multi-word becomes quoted string
        $c->set('TITLE', 'hello world' );
        $this->assertSame('"hello world"', $c->getValue('TITLE'));
    }

    public function testSetThrowsWhenValueWithoutKey(): void
    {
        $this->expectException(DotEnvInvalidSettingException::class);
        $this->expectExceptionMessage('Cannot set a value without a key');

        $c = $this->makeContainer();
        // value provided but key null
        $c->set(null, 'x' );
    }

    public function testSetThrowsOnInvalidKey(): void
    {
        $this->expectException(DotEnvInvalidSettingException::class);
        $this->expectExceptionMessage('Invalid key exception');

        $c = $this->makeContainer();
        // dash is not allowed by /^[\w_]+$/
        $c->set('BAD-KEY', 'x' );
    }

    public function testSetThrowsOnDuplicateKey(): void
    {
        $this->expectException(DotEnvInvalidSettingException::class);
        $this->expectExceptionMessage('Duplicate key exception');

        $c = $this->makeContainer([
            ['key' => 'EXISTS', 'value' => 'one', 'comment' => null],
        ]);
        $c->set('EXISTS', 'two' );
    }
    
    /**
     * @throws DotEnvInvalidSettingException
     */
    public function testReplace(): void
    {
        $c = $this->makeContainer([
            ['key' => 'FOO', 'value' => 'bar', 'comment' => null],
        ]);

        $c->replace('FOO', 'true', 'now boolean');
        $this->assertSame(true, $c->getValue( 'FOO'));
        $this->assertSame('now boolean', $c->getComment( 'FOO'));
        $this->assertSame(0, $c->indexOf('FOO'));
    }

    public function testUpdateThrowsForMissingKey(): void
    {
        $this->expectException(DotEnvInvalidSettingException::class);
        $this->expectExceptionMessage('Cannot update a key that does not exist');

        $c = $this->makeContainer();
        $c->updateValue('MISSING', 'x');
    }

}
