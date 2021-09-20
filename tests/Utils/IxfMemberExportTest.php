<?php

namespace Tests\Utils;

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

use Illuminate\Foundation\Testing\WithoutMiddleware;

use IXP\Utils\Export\JsonSchema as JsonSchemaExporter;

use Tests\TestCase;

/**
 * PHPUnit test class to test the configuration generation of IX-F Member Exports
 * against known good configurations for IXP\Utils\Export\JsonSchema
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Utils
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IxfMemberExportTest extends TestCase
{
    public $versions     = [ '1.0', '0.7', '0.6' ];

    public function testIxfMemberExportGeneration(): void
    {
        $exporter = new JsonSchemaExporter;

        foreach( $this->versions as $v ) {
            foreach( [ false, true ] as $auth ) {
                $json = $exporter->get( $v, false, $auth, true );
                $a = ( $auth ? '' : 'un' ) . 'auth';

                $knownGoodConf = file_get_contents( base_path() . "/data/ci/known-good/api-v4-member-export-{$v}-{$a}.json" );
                $this->assertFalse( $knownGoodConf === false, "IX-F Member Export - could not load known good file api-v4-member-export-{$v}-{$a}.json" );

                // clean the exports to set timestamp the same
                $json = preg_replace( '/^\s+\"timestamp\": \"[0-9:\-TZ]+\",$/m', "", $json );
                $knownGoodConf = preg_replace( '/^\s+\"timestamp\": \"[0-9:\-TZ]+\",$/m', "", $knownGoodConf );

                $this->assertEquals( $knownGoodConf, $json, "Known good and generated IX-F Member Export for {$v}-{$a} do not match" );
            }
        }
    }
}