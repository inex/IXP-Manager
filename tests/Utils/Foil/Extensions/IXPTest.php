<?php

namespace Tests\Utils\Foil\Extensions;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use IXP\Utils\Foil\Extensions\IXP as IXPFoilExtension;

use Tests\TestCase;

/**
 * PHPUnit test class to test the 'softwrap' Foil extension.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Utils\Foil\Extensions
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXPTest extends TestCase
{
    protected $ixp;

    protected function setUp(): void
    {
        $this->ixp = new IXPFoilExtension;
    }

    public function testEmptyArray(): void
    {
        $this->assertEquals( '', $this->ixp->softwrap( [], 2, ", ", ",", 0 ) );
    }

    public function testOneElementArray(): void
    {
        $this->assertEquals( 'a', $this->ixp->softwrap( ['a'], 2, ", ", ",", 0 ) );
        $this->assertEquals( 'a', $this->ixp->softwrap( ['a'], 2, ", ", ",", 10 ) );
    }

    public function testTwoElementArray(): void
    {
        $this->assertEquals( 'a, b', $this->ixp->softwrap( ['a','b'], 2, ", ", ",", 0 ) );
        $this->assertEquals( 'a, b', $this->ixp->softwrap( ['a','b'], 2, ", ", ",", 10 ) );
    }

    public function testThreeElementArray(): void
    {
        $this->assertEquals( "a, b,\nc",   $this->ixp->softwrap( ['a','b','c'], 2, ", ", ",", 0 ) );
        $this->assertEquals( "a, b,\n  c", $this->ixp->softwrap( ['a','b','c'], 2, ", ", ",", 2 ) );
    }

    public function testFourElementArray(): void
    {
        $this->assertEquals( "a, b,\nc, d",   $this->ixp->softwrap( ['a','b','c','d'], 2, ", ", ",", 0 ) );
        $this->assertEquals( "a, b, c,\nd",   $this->ixp->softwrap( ['a','b','c','d'], 3, ", ", ",", 0 ) );
        $this->assertEquals( "a, b,\n  c, d", $this->ixp->softwrap( ['a','b','c','d'], 2, ", ", ",", 2 ) );
    }

    public function testFiveElementArray(): void
    {
        $this->assertEquals( "a, b,\nc, d,\ne",     $this->ixp->softwrap( ['a','b','c','d','e'], 2, ", ", ",", 0 ) );
        $this->assertEquals( "a, b, c,\nd, e",      $this->ixp->softwrap( ['a','b','c','d','e'], 3, ", ", ",", 0 ) );
        $this->assertEquals( "a, b,\n  c, d,\n  e", $this->ixp->softwrap( ['a','b','c','d','e'], 2, ", ", ",", 2 ) );
    }
}