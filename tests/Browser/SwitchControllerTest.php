<?php

namespace Tests\Browser;

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

use IXP\Models\Switcher;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test Switch Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'phpunit', 'phpunit2' ] as $name ) {
            if( $infra = Switcher::whereName( $name )->first() ) {
                $infra->delete();
            }
        }

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws
     */
    public function testAdd(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                    ->visit('/login')
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( '#login-btn' )
                    ->assertPathIs( '/admin' );

            $browser->visit( '/switch/list' )
                ->assertSee( 'switch1' )
                ->assertSee( 'switch2' );

            $browser->visit( '/switch/create-by-snmp' )
                ->assertSee( 'Create Switch via SNMP' )
                ->assertSee( 'Hostname' )
                ->assertSee( 'SNMP Community' );


            // 1. test add by snmp and flow to next step:
            $browser->type( 'hostname', 'phpunit.test.example.com'  )
                ->type( 'snmppasswd', 'mXPOSpC52cSFg1qN'            )
                ->press('Next ≫' )
                ->assertPathIs('/switch/store-by-snmp' )
                ->assertInputValue( 'name',       'phpunit' )
                ->assertInputValue( 'hostname',   'phpunit.test.example.com'    )
                ->assertInputValue( 'snmppasswd', 'mXPOSpC52cSFg1qN'            )
                ->assertInputValue( 'model',      'FESX648' )
                ->assertSelected(   'vendorid',   '12'      )
                ->assertChecked( 'active'   )
                ->assertChecked( 'poll'     );

            // 2. test add step 2
            $browser->select( 'cabinetid', 1        )
                    ->select( 'infrastructure', 1   )
                    ->type( 'ipv4addr', '192.0.2.1' )
                    ->type( 'ipv6addr', '2001:db8::999'             )
                    ->type( 'mgmt_mac_address', 'AA:00.11:BB.22-87' )
                    ->type( 'asn', '65512'              )
                    ->type( 'loopback_ip', '127.0.0.1'  )
                    ->type( 'loopback_name', 'lo0'      )
                    ->type( 'notes', 'Test note'        );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->press( 'Create' )
                    ->assertPathIs('/switch/list' )
                    ->assertSee( 'phpunit' )
                    ->assertSee( 'FESX648' );

            // get the switch:
            $switch = Switcher::whereName( 'phpunit' )->first();

            // test the values:
            $this->assertEquals( 'phpunit',                  $switch->name              );
            $this->assertEquals( 'phpunit.test.example.com', $switch->hostname          );
            $this->assertEquals( 'mXPOSpC52cSFg1qN',         $switch->snmppasswd        );
            $this->assertEquals( 1,                          $switch->cabinetid         );
            $this->assertEquals( 1,                          $switch->infrastructure    );
            $this->assertEquals( 12,                         $switch->vendorid          );
            $this->assertEquals( 'FESX648',                  $switch->model             );
            $this->assertEquals( true,                       $switch->active            );
            $this->assertEquals( true,                       $switch->poll              );
            $this->assertEquals( '192.0.2.1',                $switch->ipv4addr          );
            $this->assertEquals( '2001:db8::999',            $switch->ipv6addr          );
            $this->assertEquals( 'aa0011bb2287',             $switch->mgmt_mac_address  );
            $this->assertEquals( 65512,                      $switch->asn               );
            $this->assertEquals( '127.0.0.1',                $switch->loopback_ip       );
            $this->assertEquals( 'lo0',                      $switch->loopback_name     );
            $this->assertEquals( 'Test note',                $switch->notes             );

            // test that editing while not making any changes and saving changes nothing
            $browser->visit( '/switch/edit/' . $switch->id          )
                    ->assertPathIs('/switch/edit/' . $switch->id    );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->press( 'Save Changes'     )
                    ->assertPathIs('/switch/list' )
                    ->assertSee( 'phpunit' )
                    ->assertSee( 'FESX648' );

            // test the values:
            $switch->refresh();

            $this->assertEquals( 'phpunit',                  $switch->name              );
            $this->assertEquals( 'phpunit.test.example.com', $switch->hostname          );
            $this->assertEquals( 'mXPOSpC52cSFg1qN',         $switch->snmppasswd        );
            $this->assertEquals( 1,                          $switch->cabinetid         );
            $this->assertEquals( 1,                          $switch->infrastructure    );
            $this->assertEquals( 12,                         $switch->vendorid          );
            $this->assertEquals( 'FESX648',                  $switch->model             );
            $this->assertEquals( true,                       $switch->active            );
            $this->assertEquals( true,                       $switch->poll              );
            $this->assertEquals( '192.0.2.1',                $switch->ipv4addr          );
            $this->assertEquals( '2001:db8::999',            $switch->ipv6addr          );
            $this->assertEquals( 'aa0011bb2287',             $switch->mgmt_mac_address  );
            $this->assertEquals( 65512,                      $switch->asn               );
            $this->assertEquals( '127.0.0.1',                $switch->loopback_ip       );
            $this->assertEquals( 'lo0',                      $switch->loopback_name     );
            $this->assertEquals( 'Test note',                $switch->notes             );


            // now test that editing while making changes works

            $browser->visit( '/switch/edit/' . $switch->id                     )
                    ->assertPathIs('/switch/edit/' . $switch->id              )
                    ->type( 'name', 'phpunit2'                          )
                    ->type( 'hostname', 'phpunit2.test.example.com'     )
                    ->type( 'snmppasswd', 'newpassword'                 )
                    ->select( 'infrastructure', 2                       )
                    ->select( 'vendorid', 11                            )
                    ->type( 'model', 'TI24X'                            )
                    ->uncheck( 'active' )
                    ->uncheck( 'poll'   )
                    ->type( 'ipv4addr', '192.0.2.2'                     )
                    ->type( 'ipv6addr', '2001:db8::9999'                )
                    ->type( 'mgmt_mac_address', 'AA:00.11:BB.22-88'     )
                    ->type( 'asn', '65513'                              )
                    ->type( 'loopback_ip', '127.0.0.2'                  )
                    ->type( 'loopback_name', 'lo1'                      )
                    ->type( 'notes', 'Test note 2'                      );

            $browser->driver->executeScript('window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes'        )
                    ->assertPathIs('/switch/list'   )
                    ->assertSee( 'phpunit2'         )
                    ->assertSee( 'TI24X'            );

            $switch->refresh();

            // test the values:
            $this->assertEquals( 'phpunit2',                  $switch->name             );
            $this->assertEquals( 'phpunit2.test.example.com', $switch->hostname         );
            $this->assertEquals( 'newpassword',               $switch->snmppasswd       );
            $this->assertEquals( 1,                           $switch->cabinetid        );
            $this->assertEquals( 2,                           $switch->infrastructure   );
            $this->assertEquals( 11,                          $switch->vendorid         );
            $this->assertEquals( 'TI24X',                     $switch->model            );
            $this->assertEquals( false,                       $switch->active           );
            $this->assertEquals( false,                       $switch->poll             );
            $this->assertEquals( '192.0.2.2',                 $switch->ipv4addr         );
            $this->assertEquals( '2001:db8::9999',            $switch->ipv6addr         );
            $this->assertEquals( 'aa0011bb2288',              $switch->mgmt_mac_address );
            $this->assertEquals( 65513,                       $switch->asn              );
            $this->assertEquals( '127.0.0.2',                 $switch->loopback_ip      );
            $this->assertEquals( 'lo1',                       $switch->loopback_name    );
            $this->assertEquals( 'Test note 2',               $switch->notes            );


            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)

            $browser->visit( '/switch/edit/' . $switch->id      )
                ->assertPathIs('/switch/edit/' . $switch->id    );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->press( 'Save Changes'        )
                    ->assertPathIs('/switch/list'   )
                    ->assertSee( 'phpunit2'         )
                    ->assertSee( 'TI24X'            );

            // test the values:
            $this->assertEquals( 'phpunit2',                  $switch->name             );
            $this->assertEquals( 'phpunit2.test.example.com', $switch->hostname         );
            $this->assertEquals( 'newpassword',               $switch->snmppasswd       );
            $this->assertEquals( 1,                           $switch->cabinetid        );
            $this->assertEquals( 2,                           $switch->infrastructure   );
            $this->assertEquals( 11,                          $switch->vendorid         );
            $this->assertEquals( 'TI24X',                     $switch->model            );
            $this->assertEquals( false,                       $switch->active           );
            $this->assertEquals( false,                       $switch->poll             );
            $this->assertEquals( '192.0.2.2',                 $switch->ipv4addr         );
            $this->assertEquals( '2001:db8::9999',            $switch->ipv6addr         );
            $this->assertEquals( 'aa0011bb2288',              $switch->mgmt_mac_address );
            $this->assertEquals( 65513,                       $switch->asn              );
            $this->assertEquals( '127.0.0.2',                 $switch->loopback_ip      );
            $this->assertEquals( 'lo1',                       $switch->loopback_name    );
            $this->assertEquals( 'Test note 2',               $switch->notes            );

            // delete this switch
            $browser->press( '#d2f-list-delete-' . $switch->id      )
                ->waitForText( 'Do you really want to delete this'    )
                ->press( 'Delete' )
                ->assertPathIs('/switch/list'   )
                ->assertDontSee( 'phpunit2'     )
                ->assertDontSee( 'TI24X'        );
        });

    }
}