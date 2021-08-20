<?php

namespace Tests\Services\Grapher\Graph\Renderer\Extensions;

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

use IXP\Services\Grapher\Renderer\Extensions\Grapher as GrapherFoilExtension;

use Tests\TestCase;

/**
 * PHPUnit test class to test the Grapher Foil extension.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Services\Grapher\Graph\Renderer\Extensions
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class GrapherTest extends TestCase
{

    protected $g;

    protected function setUp(): void
    {
        $this->g = new GrapherFoilExtension;
    }

    public function testNormalCommunity(): void
    {
        $this->assertEquals( 'qwerty123', $this->g->escapeCommunityForMrtg( 'qwerty123' ) );
    }

    public function testCommunityNeedingEscape1(): void
    {
        $this->assertEquals( 'qwe\\ rty\@123', $this->g->escapeCommunityForMrtg( 'qwe rty@123' ) );
    }

    public function testCommunityNeedingEscape2(): void
    {
        $this->assertEquals( '\\ qwerty123', $this->g->escapeCommunityForMrtg( ' qwerty123' ) );
    }

    public function testCommunityNeedingEscape3(): void
    {
        $this->assertEquals( 'qwe\\ rty123', $this->g->escapeCommunityForMrtg( 'qwe rty123' ) );
    }

    public function testCommunityNeedingEscape4(): void
    {
        $this->assertEquals( 'qwerty123\\ ', $this->g->escapeCommunityForMrtg( 'qwerty123 ' ) );
    }

    public function testCommunityNeedingEscape5(): void
    {
        $this->assertEquals( 'qwe\@rty123', $this->g->escapeCommunityForMrtg( 'qwe@rty123' ) );
    }
}