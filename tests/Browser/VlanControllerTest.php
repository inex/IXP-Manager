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

use IXP\Models\Vlan;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test Vlan Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'Vlan test', 'Vlan test2' ] as $name ) {
            if( $vlan = Vlan::whereName( $name )->first() ) {
                $vlan->delete();
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

            $browser->visit( '/vlan/list' )
                    ->assertSee( 'VLANs' );

            $browser->visit( '/vlan/create' )
                    ->assertSee( 'Create VLAN' );

            // 1. test add empty inputs
            $browser->press('Create')
                    ->assertPathIs('/vlan/create' )
                    ->assertSee( "The name field is required."              )
                    ->assertSee( "The number field is required."            )
                    ->assertSee( "The infrastructureid field is required."  )
                    ->assertSee( "The config name field is required."       );

            // 1. test add
            $browser->type(     'name',         'Vlan test')
                    ->type(     'number',    '10' )
                    ->select(   'infrastructureid',  1 )
                    ->type(     'config_name',    'vlan-test' )
                    ->check(    'private'           )
                    ->check(    'peering_matrix'    )
                    ->check(    'peering_manager'   )
                    ->type(     'notes',    'test notes' )
                    ->press('Create' )
                    ->assertPathIs('/vlan/list' )
                    ->assertSee( "VLAN created" );

            $vlan = Vlan::whereName( 'Vlan test' )->first();

            // 2. test added data in database against expected values
            $this->assertInstanceOf( Vlan::class,   $vlan                           );
            $this->assertEquals( 'Vlan test',       $vlan->name                     );
            $this->assertEquals( '10',              $vlan->number                   );
            $this->assertEquals( 1,                 $vlan->infrastructureid         );
            $this->assertEquals( 'vlan-test',       $vlan->config_name              );
            $this->assertEquals( true,              (bool)$vlan->private            );
            $this->assertEquals( true,              (bool)$vlan->peering_matrix     );
            $this->assertEquals( true,              (bool)$vlan->peering_manager    );
            $this->assertEquals( 'test notes',      $vlan->notes                    );

            // 3. browse to edit vlan object:
            $browser->click( '#e2f-list-edit-' .  $vlan->id );

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'name',      'Vlan test'    )
                    ->assertInputValue( 'number',    '10'           )
                    ->assertSelected(   'infrastructureid',  1      )
                    ->assertInputValue( 'config_name',    'vlan-test' )
                    ->assertChecked(    'private'           )
                    ->assertChecked(    'peering_matrix'    )
                    ->assertChecked(    'peering_manager'   )
                    ->assertInputValue( 'notes',    'test notes' );

            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'infrastructureid',     '2' )
                    ->uncheck('private'         )
                    ->uncheck('peering_matrix'  )
                    ->uncheck('peering_manager' )
                    ->press('Save Changes' )
                    ->assertPathIs('/vlan/list' )
                    ->assertSee( "VLAN updated" );


            // 6. repeat database load and database object check for new values (repeat 2)
            $vlan->refresh();

            $this->assertInstanceOf( Vlan::class,   $vlan                           );
            $this->assertEquals( 'Vlan test',       $vlan->name                     );
            $this->assertEquals( '10',              $vlan->number                   );
            $this->assertEquals( 2,                 $vlan->infrastructureid         );
            $this->assertEquals( 'vlan-test',       $vlan->config_name              );
            $this->assertEquals( false,             (bool)$vlan->private            );
            $this->assertEquals( false,             (bool)$vlan->peering_matrix     );
            $this->assertEquals( false,             (bool)$vlan->peering_manager    );
            $this->assertEquals( 'test notes',      $vlan->notes                    );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/vlan/edit/' .  $vlan->id )
                    ->assertSee( 'Edit VLAN' );

            $browser->assertInputValue( 'name',      'Vlan test'    )
                    ->assertInputValue( 'number',    '10'           )
                    ->assertSelected(   'infrastructureid',  2 )
                    ->assertInputValue( 'config_name',    'vlan-test' )
                    ->assertNotChecked( 'private'           )
                    ->assertNotChecked( 'peering_matrix'    )
                    ->assertNotChecked( 'peering_manager'   )
                    ->assertInputValue( 'notes',    'test notes' );


            // 8. submit with no changes and verify no changes in database
            $browser->press('Save Changes')
                    ->assertPathIs('/vlan/list');


            // 9. repeat database load and database object check for new values (repeat 2)
            $vlan->refresh();

            $this->assertInstanceOf( Vlan::class,   $vlan                           );
            $this->assertEquals( 'Vlan test',       $vlan->name                     );
            $this->assertEquals( '10',              $vlan->number                   );
            $this->assertEquals( 2,                 $vlan->infrastructureid         );
            $this->assertEquals( 'vlan-test',       $vlan->config_name              );
            $this->assertEquals( false,             (bool)$vlan->private            );
            $this->assertEquals( false,             (bool)$vlan->peering_matrix     );
            $this->assertEquals( false,             (bool)$vlan->peering_manager    );
            $this->assertEquals( 'test notes',      $vlan->notes                    );

            // 10. edit again and check all checkboxes and submit
            $browser->visit( '/vlan/edit/' .  $vlan->id )
                    ->assertSee( 'Edit VLAN' );

            $browser->check('private'           )
                    ->check('peering_matrix'    )
                    ->check('peering_manager'   )
                    ->press('Save Changes' )
                    ->assertPathIs('/vlan/list');


            // 11. verify checkbox bool elements in database are all true
            $vlan->refresh();

            $this->assertInstanceOf( Vlan::class,   $vlan                           );
            $this->assertEquals( 'Vlan test',       $vlan->name                     );
            $this->assertEquals( '10',              $vlan->number                   );
            $this->assertEquals( 2,                 $vlan->infrastructureid         );
            $this->assertEquals( 'vlan-test',       $vlan->config_name              );
            $this->assertEquals( true,              (bool)$vlan->private            );
            $this->assertEquals( true,              (bool)$vlan->peering_matrix     );
            $this->assertEquals( true,              (bool)$vlan->peering_manager    );
            $this->assertEquals( 'test notes',      $vlan->notes                    );

            // 12. edit all inputs
            $browser->visit( '/vlan/edit/' .  $vlan->id )
                    ->assertSee( 'Edit VLAN' );

            $browser->type(     'name',         'Vlan test2' )
                    ->type(     'number',    '11' )
                    ->select(   'infrastructureid',  1 )
                    ->type(     'config_name',    'vlan-test2' )
                    ->check(    'private'           )
                    ->check(    'peering_matrix'    )
                    ->check(    'peering_manager'   )
                    ->type(     'notes',    'test notes2' )
                    ->press('Save Changes')
                    ->assertPathIs('/vlan/list');

            $vlan->refresh();

            $this->assertInstanceOf( Vlan::class,   $vlan                           );
            $this->assertEquals( 'Vlan test2',      $vlan->name                     );
            $this->assertEquals( '11',              $vlan->number                   );
            $this->assertEquals( 1,                 $vlan->infrastructureid         );
            $this->assertEquals( 'vlan-test2',      $vlan->config_name              );
            $this->assertEquals( true,              (bool)$vlan->private            );
            $this->assertEquals( true,              (bool)$vlan->peering_matrix     );
            $this->assertEquals( true,              (bool)$vlan->peering_manager    );
            $this->assertEquals( 'test notes2',     $vlan->notes                    );

            // 13. Add duplicate vlan
            $browser->visit( '/vlan/create' )
                    ->assertSee( 'Create VLAN' )
                    ->type(     'name',         'Vlan test2'    )
                    ->type(     'number',       '11'            )
                    ->select(   'infrastructureid',  1 )
                    ->type(     'config_name',    'vlan-test2' )
                    ->check(    'private'           )
                    ->check(    'peering_matrix'    )
                    ->check(    'peering_manager'   )
                    ->type(     'notes',    'test notes2' )
                    ->press('Create' )
                    ->assertPathIs('/vlan/create')
                    ->assertSee( "The couple Infrastructure and config name already exist" )
                    ->visit( '/vlan/list' );

            // 14. delete the router in the UI and verify via success message text and location
            $browser->visit( '/vlan/list/' )
                    ->press('#e2f-list-delete-dd-' . $vlan->id      )
                    ->press('#e2f-list-delete-' . $vlan->id         )
                    ->waitForText( 'Do you really want to delete this VLAN' )
                    ->press('Delete' );

            $browser->assertSee( 'VLAN deleted.' );

            // 12. do a D2EM findOneBy and verify false/null
            $this->assertTrue( Vlan::whereName( 'Vlan test' )->doesntExist() );
        });
    }
}