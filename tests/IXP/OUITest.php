<?php

require __DIR__ . '/../../library/Zend/Exception.php';
require __DIR__ . '/../../library/IXP/Exception.php';
require __DIR__ . '/../../library/IXP/OUI.php';

/**
 * PHPUnit test class to test the IXP_OUI class
 */
class OUITest extends PHPUnit_Framework_TestCase
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

    public function testParse()
    {
        $oui = new IXP_OUI();
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
     * @expectedException IXP_Exception
     */
    public function testBadFile()
    {
        $oui = new IXP_OUI( '/path/that/does/not/exist/I/hope.txt' );
        $oui->loadList();
    }

    public function testDownloadDefault()
    {
        $oui = new IXP_OUI();
        $this->assertInstanceOf( 'IXP_OUI', $oui->loadList() );
    }



}

