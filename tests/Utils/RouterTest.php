<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Utils\Router as Router;
use IXP\Exceptions\Utils\RouterException;

use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * PHPUnit test class to test the configuration generation of router configurations
 * against known good configurations for IXP\Tasks\Router\ConfigurationGenerator
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Tests
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterTest extends TestCase
{
    public $rchandles = [ 'rc1-lan1-ipv4', 'rc1-lan1-ipv6', 'rc1-lan2-ipv4', 'rc1-lan2-ipv6',  ];
    public $rshandles = [ 'rs1-lan1-ipv4', 'rs1-lan1-ipv6', 'rs1-lan2-ipv4', 'rs1-lan2-ipv6',  ];

    public function testUnknownHandle() {
        $this->expectException( RouterException::class );
        $this->expectExceptionMessage( 'Router handle does not exist: does-not-exist' );
        $router = new Router( 'does-not-exist' );
    }

    public function testUnknownTemplate() {
        $this->expectException( RouterException::class );
        $this->expectExceptionMessage( 'Template does not exist' );
        $router = new Router( 'unknown-template' );
        $router->checkTemplate();
    }

    public function testAccessorHandle() {
        $router = new Router( 'rc1-lan1-ipv4' );
        $this->assertNotEquals( 'test', $router->handle() );
        $this->assertInstanceOf( Router::class, $router->setHandle('test') );
        $this->assertEquals( 'test', $router->handle() );
    }

    public function testAccessorRouter() {
        $router = new Router( 'rc1-lan1-ipv4' );
        $this->assertNotEquals( [], $router->router() );
        $this->assertInstanceOf( Router::class, $router->setRouter( [ 'test' => true ] ) );
        $this->assertEquals( [ 'test' => true ], $router->router() );
    }

    public function testSetRouterByHandleException() {
        $router = new Router( 'rc1-lan1-ipv4' );
        $this->expectException( RouterException::class );
        $this->expectExceptionMessage( 'Router handle does not exist: does-not-exist' );
        $router->setRouterByHandle('does-not-exist');
    }

}
