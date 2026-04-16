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

namespace Tests\Utils\View\Alert;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container;
use Tests\TestCase;

/**
 * PHPUnit test class to test the configuration generation of IX-F Member Exports
 * against known good configurations for IXP\Utils\Export\JsonSchema
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Thomas Keri      <thoas@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Utils
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContainerTest extends TestCase
{
    public function testPush()
    {
        // We can push an alert..
        Container::push("It worked");

        $this->assertTrue(session()->has('ixp.utils.view.alerts'));
        $alerts = session('ixp.utils.view.alerts');
        $this->assertCount( 1, $alerts );
        $this->assertInstanceOf(Alert::class, $alerts[0]);
        $this->assertEquals("It worked", $alerts[0]->message());
        $this->assertEquals(Alert::INFO, $alerts[0]->class());

        // We can push several alerts
        Container::push("With more info...", Alert::WARNING);
        $this->assertTrue(session()->has('ixp.utils.view.alerts'));
        $alerts = session('ixp.utils.view.alerts');
        $this->assertCount( 2, $alerts );
        $this->assertInstanceOf(Alert::class, $alerts[0]);
        $this->assertEquals("It worked", $alerts[0]->message());
        $this->assertEquals(Alert::INFO, $alerts[0]->class());

        $this->assertInstanceOf(Alert::class, $alerts[1]);
        $this->assertEquals("With more info...", $alerts[1]->message());
        $this->assertEquals(Alert::WARNING, $alerts[1]->class());

        // We can clear alerts when pushing
        Container::push("Something else entirely.", Alert::DANGER, clear: true);
        $this->assertTrue(session()->has('ixp.utils.view.alerts'));
        $alerts = session('ixp.utils.view.alerts');
        $this->assertCount( 1, $alerts );
        $this->assertInstanceOf(Alert::class, $alerts[0]);
        $this->assertEquals("Something else entirely.", $alerts[0]->message());
        $this->assertEquals(Alert::DANGER, $alerts[0]->class());

        /// Or opt not to clear
        Container::push("And maybe another", Alert::DANGER, clear: false);
        $this->assertTrue(session()->has('ixp.utils.view.alerts'));
        $alerts = session('ixp.utils.view.alerts');
        $this->assertCount( 2, $alerts );
        $this->assertInstanceOf(Alert::class, $alerts[0]);
        $this->assertEquals("Something else entirely.", $alerts[0]->message());
        $this->assertEquals(Alert::DANGER, $alerts[0]->class());

        $this->assertInstanceOf(Alert::class, $alerts[1]);
        $this->assertEquals("And maybe another", $alerts[1]->message());
        $this->assertEquals(Alert::DANGER, $alerts[1]->class());
    }

    public function testPop()
    {
        Container::push("First", Alert::INFO);
        Container::push("Second", Alert::SUCCESS);

        $this->assertCount( 2, session('ixp.utils.view.alerts') );
        $alert = Container::pop();
        $this->assertEquals("Second", $alert->message());
        $this->assertEquals(Alert::SUCCESS, $alert->class());
        $this->assertCount( 1, session('ixp.utils.view.alerts') );
        $alert = Container::pop();
        $this->assertEquals("First", $alert->message());
        $this->assertEquals(Alert::INFO, $alert->class());
        $this->assertCount(0, session('ixp.utils.view.alerts') );
        $alert = Container::pop();
        $this->assertNull($alert);
    }

    public function testHtml()
    {
        Container::push("First", Alert::INFO);
        $info = Container::html();
        $this->assertStringContainsString('fa-info-circle', $info); // icon
        $this->assertStringContainsString('tw-bg-blue-100', $info); // colour
        $this->assertStringContainsString('tw-border-blue-500', $info); // border
        $this->assertStringContainsString('tw-text-blue-700', $info); // text
        $this->assertStringContainsString('First', $info); // alert message
        $this->assertCount(0, session('ixp.utils.view.alerts') ); // html drains all alerts

        Container::push("Second", Alert::SUCCESS);
        $success = Container::html();
        $this->assertStringContainsString('fa-check-circle', $success); // icon
        $this->assertStringContainsString('tw-bg-green-100', $success); // colour
        $this->assertStringContainsString('tw-border-green-500', $success); // border
        $this->assertStringContainsString('tw-text-green-700', $success); // text
        $this->assertStringContainsString('Second', $success); // alert message
        $this->assertCount(0, session('ixp.utils.view.alerts') ); // html drains all alerts

        Container::push("Third", Alert::WARNING);
        $warning = Container::html();
        $this->assertStringContainsString('fa-exclamation-circle', $warning); // icon
        $this->assertStringContainsString('tw-bg-orange-100', $warning); // colour
        $this->assertStringContainsString('tw-border-orange-500', $warning); // border
        $this->assertStringContainsString('tw-text-orange-700', $warning); // text
        $this->assertStringContainsString('Third', $warning); // alert message
        $this->assertCount(0, session('ixp.utils.view.alerts') ); // html drains all alerts

        Container::push("Fourth", Alert::DANGER);
        $danger = Container::html();
        $this->assertStringContainsString('fa-exclamation-triangle', $danger); // icon
        $this->assertStringContainsString('tw-bg-red-100', $danger); // colour
        $this->assertStringContainsString('tw-border-red-500', $danger); // border
        $this->assertStringContainsString('tw-text-red-700', $danger); // text
        $this->assertStringContainsString('Fourth', $danger); // alert message
        $this->assertCount(0, session('ixp.utils.view.alerts') ); // html drains all alerts

        Container::push("Multi first", Alert::INFO);
        Container::push("Multi second", Alert::SUCCESS);
        $multi = Container::html();
        $this->assertStringContainsString('fa-check-circle', $multi); // icon
        $this->assertStringContainsString('fa-info-circle', $multi); // icon
        $this->assertStringContainsString('Multi first', $multi); // icon
        $this->assertStringContainsString('Multi second', $multi); // icon
        $this->assertCount(0, session('ixp.utils.view.alerts') ); // html drains all alerts

    }
}