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

namespace Tests\Services\Validation\Enum;

use IXP\Services\Validation\Enums\Severity;
use PHPUnit\Framework\TestCase;

class SeverityUnitTest extends TestCase
{
    public function testValues()
    {
        $this->assertEquals( [ 'debug', 'info', 'suggestion', 'warning', 'error' ] , Severity::values());
    }

    public function testRank()
    {
        $this->assertEquals( 10 , Severity::Debug->rank());
        $this->assertEquals( 20 , Severity::Info->rank());
        $this->assertEquals( 30 , Severity::Suggestion->rank());
        $this->assertEquals( 40 , Severity::Warning->rank());
        $this->assertEquals( 50 , Severity::Error->rank());
    }

    public function testIsAtLeast()
    {
        // $a === $a is true
        $this->assertTrue(Severity::Debug->isAtLeast( Severity::Debug ));
        $this->assertTrue(Severity::Info->isAtLeast( Severity::Info ));
        $this->assertTrue(Severity::Suggestion->isAtLeast( Severity::Suggestion ));
        $this->assertTrue(Severity::Warning->isAtLeast( Severity::Warning ));
        $this->assertTrue(Severity::Error->isAtLeast( Severity::Error ));

        // $a >= $b is true
        $this->assertTrue(Severity::Info->isAtLeast( Severity::Debug ));
        $this->assertTrue(Severity::Suggestion->isAtLeast( Severity::Info ));
        $this->assertTrue(Severity::Warning->isAtLeast( Severity::Suggestion ));
        $this->assertTrue(Severity::Error->isAtLeast( Severity::Warning ));

        // $a < $b is false
        $this->assertFalse(Severity::Debug->isAtLeast( Severity::Info ));
        $this->assertFalse(Severity::Info->isAtLeast( Severity::Suggestion ));
        $this->assertFalse(Severity::Debug->isAtLeast( Severity::Suggestion ));

        $this->assertFalse(Severity::Suggestion->isAtLeast( Severity::Warning ));
        $this->assertFalse(Severity::Info->isAtLeast( Severity::Warning ));
        $this->assertFalse(Severity::Debug->isAtLeast( Severity::Warning ));

        $this->assertFalse(Severity::Warning->isAtLeast( Severity::Error ));
        $this->assertFalse(Severity::Suggestion->isAtLeast( Severity::Error ));
        $this->assertFalse(Severity::Info->isAtLeast( Severity::Error ));
        $this->assertFalse(Severity::Debug->isAtLeast( Severity::Error ));
    }
}