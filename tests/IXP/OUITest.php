<?php

namespace Tests\IXP;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use IXP\Exceptions\GeneralException;

use IXP\Utils\OUI as OUIUtil;

use Tests\TestCase;

/**
 * PHPUnit test class to test the IXP_OUI class
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\IXP
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class OUITest extends TestCase
{
    public $sampleRawData = <<<END_DATA

  OUI/MA-L          Organization
  company_id            Organization
                Address


  00-00-00   (hex)      XEROX CORPORATION
  000000     (base 16)      XEROX CORPORATION
                M/S 105-50C
                800 PHILLIPS ROAD
                WEBSTER NY 14580
                UNITED STATES

  00-00-01   (hex)      XEROX CORPORATION
  000001     (base 16)      XEROX CORPORATION
                ZEROX SYSTEMS INSTITUTE
                M/S 105-50C 800 PHILLIPS ROAD
                WEBSTER NY 14580
                UNITED STATES

  00-00-4C   (hex)      NEC CORPORATION
  00004C     (base 16)      NEC CORPORATION
                7-1 SHIBA  5-CHOME
                MINATO-KU
                TOKYO 108-01
                JAPAN

END_DATA;

    public function testParse(): void
    {
        $oui = new OUIUtil();
        $parsed = $oui->processRawData( $this->sampleRawData );
        $this->assertTrue( is_array( $parsed ) );
        $this->assertArrayHasKey( '000000', $parsed );
        $this->assertArrayHasKey( '000001', $parsed );
        $this->assertArrayHasKey( '00004c', $parsed );

        $this->assertEquals( $parsed[ '000000' ], 'XEROX CORPORATION' );
        $this->assertEquals( $parsed[ '000001' ], 'XEROX CORPORATION' );
        $this->assertEquals( $parsed[ '00004c' ], 'NEC CORPORATION' );

        $this->assertEquals( 3, count( $parsed ) );
    }

    /**
     */
    public function testBadFile(): void
    {
        $this->expectException( GeneralException::class );
        $oui = new OUIUtil( '/path/that/does/not/exist/I/hope.txt' );
        $oui->loadList();
    }

    // disabled 20161017 by barryo as d/l takes >1hr
    // public function testDownloadDefault()
    // {
    //     $oui = new IXP_OUI();
    //     $this->assertInstanceOf( 'IXP_OUI', $oui->loadList() );
    // }

}