<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Entities\Router as RouterEntity;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use IXP\Tasks\Router\ConfigurationGenerator as RouterConfigurationGenerator;

use Tests\TestCase;

/**
 * PHPUnit test class to test the configuration generation of router configurations
 * against known good configurations for IXP\Tasks\Router\ConfigurationGenerator
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Tests
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class GenerateConfigurationBird2ServerTest extends TestCase
{
    public $rshandles    = [ 'b2-rs1-lan1-ipv4',   'b2-rs1-lan1-ipv6',   ];

    public function testRouteServerBirdConfigurationGeneration()
    {
        foreach( $this->rshandles as $handle )
        {
            $router = D2EM::getRepository( RouterEntity::class )->findOneBy( ['handle' => $handle] );
            $conf = ( new RouterConfigurationGenerator( $router ) )->render();

            $knownGoodConf = file_get_contents( base_path() . "/data/travis-ci/known-good/ci-apiv4-{$handle}.conf" );
            $this->assertFalse( $knownGoodConf === false, "RS Conf generation - could not load known good file ci-apiv4-{$handle}.conf" );

            // clean the configs to remove the comment lines and white space which are irrelevent
            $conf          = preg_replace( "/^#.*$/m", "", $conf          );
            $knownGoodConf = preg_replace( "/^#.*$/m", "", $knownGoodConf );
            $conf          = preg_replace( "/^\s+$/m", "", $conf          );
            $knownGoodConf = preg_replace( "/^\s+$/m", "", $knownGoodConf );

            $this->assertEquals( $knownGoodConf, $conf, "Known good and generated RS configuration for {$handle} do not match" );
        }
    }
}
