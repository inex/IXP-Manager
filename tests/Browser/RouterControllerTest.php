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
use IXP\Models\Router;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;
use Throwable;

/**
 * Test router Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        if( $router = Router::whereHandle( 'dusk-ci-test' )->get()->first() ) {
            $router->delete();
        }

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testAdd(): void
    {
        $this->browse( function ( Browser $browser ) {
            $browser->resize( 1600,1200 )
                    ->visit('/login')
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( '#login-btn' )
                    ->assertPathIs( '/admin' );

            $browser->visit( '/router/create' )
                    ->assertSee( 'Handle' );

            // 1. test add
            $browser->type('handle',  'dusk-ci-test')
                    ->select( 'vlan_id',     '2'    )
                    ->select( 'protocol', '6' )
                    ->select( 'type', '1'     )
                    ->type( 'name', 'Travis CI Test'    )
                    ->type( 'shortname', 'citest'       )
                    ->type( 'router_id', '192.0.2.1'    )
                    ->type( 'peering_ip', '2001:db8::1' )
                    ->type( 'asn', '65544'  )
                    ->select( 'software', 1 )
                    ->type( 'software_version', '2.0.4'             )
                    ->type( 'operating_system', 'Ubuntu Linux'      )
                    ->type( 'operating_system_version', '18.04 LTS' )
                    ->type( 'mgmt_host', '192.0.2.1'            )
                    ->select( 'api_type', 1                     )
                    ->type( 'api', 'https://api.example.com'    )
                    ->select( 'lg_access', 2 )
                    ->check('quarantine'    )
                    ->check('bgp_lc'        );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->check('rpki'          )
                    ->check('skip_md5'      )
                    ->type( 'template', 'api/v4/router/server/bird/standard' )
                    ->press('Create'     )
                    ->assertPathIs('/router/list' )
                    ->assertSee( 'Router created' );

            /** @var Router $router */
            $router = Router::whereHandle( 'dusk-ci-test' )->get()->first();

            // 2. test added data in database against expected values
            $this->assertInstanceOf( Router::class,         $router                             );
            $this->assertEquals( 'dusk-ci-test',            $router->handle                     );
            $this->assertEquals( '2',                       $router->vlan_id                    );
            $this->assertEquals( '6',                       $router->protocol                   );
            $this->assertEquals( '1',                       $router->type                       );
            $this->assertEquals( 'Travis CI Test',          $router->name                       );
            $this->assertEquals( 'citest',                  $router->shortname                  );
            $this->assertEquals( '192.0.2.1',               $router->router_id                  );
            $this->assertEquals( '2001:db8::1',             $router->peering_ip                 );
            $this->assertEquals( '65544',                   $router->asn                        );
            $this->assertEquals( '1',                       $router->software                   );
            $this->assertEquals( '2.0.4',                   $router->software_version           );
            $this->assertEquals( 'Ubuntu Linux',            $router->operating_system           );
            $this->assertEquals( '18.04 LTS',               $router->operating_system_version   );
            $this->assertEquals( '192.0.2.1',               $router->mgmt_host                  );
            $this->assertEquals( '1',                       $router->api_type                   );
            $this->assertEquals( 'https://api.example.com', $router->api                        );
            $this->assertEquals( '2',                       $router->lg_access                  );
            $this->assertEquals( true,                      $router->quarantine                 );
            $this->assertEquals( true,                      $router->bgp_lc                     );
            $this->assertEquals( true,                      $router->rpki                       );
            $this->assertEquals( true,                      $router->skip_md5                   );
            $this->assertEquals( 'api/v4/router/server/bird/standard', $router->template        );

            // 3. browse to edit router object: $browser->visit( '/router/edit/' . $router->getId() )
            $browser->visit( '/router/edit/' .  $router->id );

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue('handle',    'dusk-ci-test'  )
                    ->assertSelected(   'vlan_id',     '2'          )
                    ->assertSelected(   'protocol', '6'             )
                    ->assertSelected(   'type',     '1'             )
                    ->assertInputValue( 'name',     'Travis CI Test')
                    ->assertInputValue( 'shortname','citest'        )
                    ->assertInputValue( 'router_id', '192.0.2.1'    )
                    ->assertInputValue( 'peering_ip','2001:db8::1'  )
                    ->assertInputValue( 'asn',       '65544'        )
                    ->assertSelected(   'software',  '1'            )
                    ->assertInputValue( 'software_version', '2.0.4'             )
                    ->assertInputValue( 'operating_system', 'Ubuntu Linux'      )
                    ->assertInputValue( 'operating_system_version', '18.04 LTS' )
                    ->assertInputValue( 'mgmt_host', '192.0.2.1'            )
                    ->assertSelected(   'api_type', '1'                     )
                    ->assertInputValue( 'api', 'https://api.example.com'    )
                    ->assertSelected(   'lg_access', '2'                    )
                    ->assertChecked(    'quarantine'    )
                    ->assertChecked(    'bgp_lc'        )
                    ->assertChecked(    'rpki'          )
                    ->assertChecked(    'skip_md5'      )
                    ->assertInputValue( 'template', 'api/v4/router/server/bird/standard' );

            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select(   'vlan_id',     '1'  )
                    ->select(   'protocol',  '4'    )
                    ->select(   'type', '2'         )
                    ->type(     'name', 'Travis CI Test 2'  )
                    ->type(     'shortname', 'citest2'      )
                    ->type(     'router_id', '192.0.2.20'   )
                    ->type(     'peering_ip', '192.0.2.21'  )
                    ->type(     'asn', '65545'              )
                    ->select(   'software', '2'             )
                    ->type(     'software_version', '1.6.4' )
                    ->type(     'operating_system', 'FreeBSD'       )
                    ->type(     'operating_system_version', '11.4'  )
                    ->type(     'mgmt_host', '192.0.2.10'           )
                    ->select(   'api_type', '0' )
                    ->type(     'api', 'https://api2.example.com'   )
                    ->select(   'lg_access', '1' );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->uncheck(  'quarantine')
                    ->uncheck(  'bgp_lc'    )
                    ->uncheck(  'rpki'      )
                    ->uncheck(  'skip_md5'  )
                    ->type(     'template', 'api/v4/router/as112/bird/standard' )
                    ->press(   'Save Changes')
                    ->assertPathIs('/router/list');


            // 6. repeat database load and database object check for new values (repeat 2)
            $router->refresh();

            $this->assertInstanceOf( Router::class, $router );
            $this->assertEquals( 'dusk-ci-test',                $router->handle                     );
            $this->assertEquals( '1',                           $router->vlan_id                    );
            $this->assertEquals( '4',                           $router->protocol                   );
            $this->assertEquals( '2',                           $router->type                       );
            $this->assertEquals( 'Travis CI Test 2',            $router->name                       );
            $this->assertEquals( 'citest2',                     $router->shortname                  );
            $this->assertEquals( '192.0.2.20',                  $router->router_id                  );
            $this->assertEquals( '192.0.2.21',                  $router->peering_ip                 );
            $this->assertEquals( '65545',                       $router->asn                        );
            $this->assertEquals( '2',                           $router->software                   );
            $this->assertEquals( '1.6.4',                       $router->software_version           );
            $this->assertEquals( 'FreeBSD',                     $router->operating_system           );
            $this->assertEquals( '11.4',                        $router->operating_system_version   );
            $this->assertEquals( '192.0.2.10',                  $router->mgmt_host                  );
            $this->assertEquals( '0',                           $router->api_type                   );
            $this->assertEquals( 'https://api2.example.com',    $router->api                        );
            $this->assertEquals( '1',                           $router->lg_access                  );
            $this->assertEquals( false,                         $router->quarantine                 );
            $this->assertEquals( false,                         $router->bgp_lc                     );
            $this->assertEquals( false,                         $router->rpki                       );
            $this->assertEquals( false,                         $router->skip_md5                   );
            $this->assertEquals( 'api/v4/router/as112/bird/standard', $router->template             );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/router/edit/' .  $router->id )
                    ->assertSee( 'Handle' );

            $browser->assertSelected('vlan_id', '1'     )
                    ->assertSelected('protocol', '4'    )
                    ->assertSelected('type', '2'        )
                    ->assertSelected('software', '2'    )
                    ->assertSelected('api_type', '0'    )
                    ->assertSelected( 'lg_access', '1'  )
                    ->assertNotChecked( 'quarantine'    )
                    ->assertNotChecked( 'bgp_lc'        )
                    ->assertNotChecked( 'rpki'          )
                    ->assertNotChecked( 'skip_md5'      );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            // 8. submit with no changes and verify no changes in database
            $browser->press('Save Changes')
                    ->assertPathIs('/router/list');

            // . repeat database load and database object check for new values (repeat 2)
            $router->refresh();

            $this->assertInstanceOf( Router::class, $router );
            $this->assertEquals( 'dusk-ci-test',                $router->handle                     );
            $this->assertEquals( '1',                           $router->vlan_id                    );
            $this->assertEquals( '4',                           $router->protocol                   );
            $this->assertEquals( '2',                           $router->type                       );
            $this->assertEquals( 'Travis CI Test 2',            $router->name                       );
            $this->assertEquals( 'citest2',                     $router->shortname                  );
            $this->assertEquals( '192.0.2.20',                  $router->router_id                  );
            $this->assertEquals( '192.0.2.21',                  $router->peering_ip                 );
            $this->assertEquals( '65545',                       $router->asn                        );
            $this->assertEquals( '2',                           $router->software                   );
            $this->assertEquals( '1.6.4',                       $router->software_version           );
            $this->assertEquals( 'FreeBSD',                     $router->operating_system           );
            $this->assertEquals( '11.4',                        $router->operating_system_version   );
            $this->assertEquals( '192.0.2.10',                  $router->mgmt_host                  );
            $this->assertEquals( '0',                           $router->api_type                   );
            $this->assertEquals( 'https://api2.example.com',    $router->api                        );
            $this->assertEquals( '1',                           $router->lg_access                  );
            $this->assertEquals( false,                         $router->quarantine                 );
            $this->assertEquals( false,                         $router->bgp_lc                     );
            $this->assertEquals( false,                         $router->rpki                       );
            $this->assertEquals( false,                         $router->skip_md5                   );
            $this->assertEquals( 'api/v4/router/as112/bird/standard', $router->template             );

            // 9. edit again and check all checkboxes and submit
            $browser->visit( '/router/edit/' .  $router->id )
                    ->assertSee( 'Handle' );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->check('quarantine'    )
                    ->check('bgp_lc'        )
                    ->check('rpki'          )
                    ->check('skip_md5'      )
                    ->press('Save Changes' )
                    ->assertPathIs('/router/list');


            // 10. verify checkbox bool elements in database are all true
            $router->refresh();

            $this->assertEquals( true, $router->quarantine  );
            $this->assertEquals( true, $router->bgp_lc      );
            $this->assertEquals( true, $router->rpki        );
            $this->assertEquals( true, $router->skip_md5    );

            // 11. delete the router in the UI and verify via success message text and location
            $browser->visit( '/router/list/' )
                    ->press('#btn-delete-' . $router->id )
                    ->waitForText( 'Do you want to delete this router' )
                    ->press('Delete' );

            $browser->assertSee( 'Router deleted.' );

            // 12. do a D2EM findOneBy and verify false/null
            $this->assertEquals( null, Router::whereHandle( 'dusk-ci-test' )->get()->first());
        });
    }
}