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

use IXP\Tasks\Router\ConfigurationGenerator as RouterConfigurationGenerator;

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
class GenerateConfigurationTest extends TestCase
{
    public $rchandles = [ 'rc1-lan1-ipv4', 'rc1-lan1-ipv6', 'rc1-lan2-ipv4', 'rc1-lan2-ipv6',  ];
    public $rshandles = [ 'rs1-lan1-ipv4', 'rs1-lan1-ipv6', 'rs1-lan2-ipv4', 'rs1-lan2-ipv6',  ];

    public function testRouteCollectorBirdConfigurationGeneration()
    {
        foreach( $this->rchandles as $handle )
        {
            $conf = ( new RouterConfigurationGenerator( $handle ) )->render();

            $knownGoodConf = file_get_contents( base_path() . "/data/travis-ci/known-good/ci-apiv4-{$handle}.conf" );
            $this->assertFalse( $knownGoodConf === false, "RC Conf generation - could not load known good file ci-apiv4-{$handle}.conf" );

            // clean the configs to remove the comment lines which are irrelevent
            $conf          = preg_replace( "/^#.*$/m", "", $conf          );
            $knownGoodConf = preg_replace( "/^#.*$/m", "", $knownGoodConf );

            $this->assertEquals( $knownGoodConf, $conf, "Known good and generated RC configuration for {$handle} do not match" );
        }
    }

    public function testRouteServerBirdConfigurationGeneration()
    {
        foreach( $this->rshandles as $handle )
        {
            $conf = ( new RouterConfigurationGenerator( $handle ) )->render();

            $knownGoodConf = file_get_contents( base_path() . "/data/travis-ci/known-good/ci-apiv4-{$handle}.conf" );
            $this->assertFalse( $knownGoodConf === false, "RS Conf generation - could not load known good file ci-apiv4-{$handle}.conf" );

            // clean the configs to remove the comment lines which are irrelevent
            $conf          = preg_replace( "/^#.*$/m", "", $conf          );
            $knownGoodConf = preg_replace( "/^#.*$/m", "", $knownGoodConf );

            $this->assertEquals( $knownGoodConf, $conf, "Known good and generated RS configuration for {$handle} do not match" );
        }
    }

    public function testUnknownHandle() {
        $this->expectException( IXP\Exceptions\GeneralException::class );
        $this->expectExceptionMessage( 'Router handle does not exist: does-not-exist' );
        $conf = ( new RouterConfigurationGenerator( 'does-not-exist' ) )->render();
    }

    public function testUnknownVlan() {
        $this->expectException( IXP\Exceptions\GeneralException::class );
        $this->expectExceptionMessage( 'Invalid/missing vlan_id in router object' );
        $conf = ( new RouterConfigurationGenerator( 'unknown-vlan' ) )->render();
    }

    public function testUnknownTemplate() {
        $this->expectException( IXP\Exceptions\GeneralException::class );
        $this->expectExceptionMessage( 'Template does not exist' );
        $conf = ( new RouterConfigurationGenerator( 'unknown-template' ) )->render();
    }

    public function testAccessorHandle() {
        $rcg = new RouterConfigurationGenerator( 'rc1-lan1-ipv4' );
        $this->assertNotEquals( 'test', $rcg->handle() );
        $this->assertInstanceOf( RouterConfigurationGenerator::class, $rcg->setHandle('test') );
        $this->assertEquals( 'test', $rcg->handle() );
    }

    public function testAccessorRouter() {
        $rcg = new RouterConfigurationGenerator( 'rc1-lan1-ipv4' );
        $this->assertNotEquals( [], $rcg->router() );
        $this->assertInstanceOf( RouterConfigurationGenerator::class, $rcg->setRouter( [ 'test' => true ] ) );
        $this->assertEquals( [ 'test' => true ], $rcg->router() );
    }

    public function testSetRouterByHandleException() {
        $rcg = new RouterConfigurationGenerator( 'rc1-lan1-ipv4' );
        $this->expectException( IXP\Exceptions\GeneralException::class );
        $this->expectExceptionMessage( 'Router handle does not exist: does-not-exist' );
        $rcg->setRouterByHandle('does-not-exist');
    }

}
