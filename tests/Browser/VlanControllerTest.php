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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Tests\Browser;

use D2EM;

use Entities\{
    Vlan as VlanEntity
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class VlanControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        $vlan = D2EM::getRepository( VlanEntity::class )->findOneBy( [ 'name' => 'Vlan test2' ] );
        if( $vlan ) {
            D2EM::remove( $vlan );
            D2EM::flush();
        }

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testAdd()
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

            $browser->visit( '/vlan/add' )
                ->assertSee( 'Add VLAN' );

            // 1. test add empty inputs
            $browser->press('Add')
                ->assertPathIs('/vlan/add')
                ->assertSee( "The name field is required." )
                ->assertSee( "The number field is required." )
                ->assertSee( "The infrastructureid field is required." )
                ->assertSee( "The config name field is required." );

            // 1. test add
            $browser->type(     'name',         'Vlan test')
                    ->type(     'number',    '10' )
                    ->select(   'infrastructureid',  1 )
                    ->type(     'config_name',    'vlan-test' )
                    ->check(    'private')
                    ->check(    'peering_matrix')
                    ->check(    'peering_manager')
                    ->type(     'notes',    'test notes' )
                    ->press('Add')
                    ->assertPathIs('/vlan/list')
                    ->assertSee( "VLAN added" );

            /** @var VlanEntity $vlan */
            $vlan = D2EM::getRepository( VlanEntity::class )->findOneBy( [ 'name' => 'Vlan test' ] );

            // 2. test added data in database against expected values
            $this->assertInstanceOf( VlanEntity::class, $vlan );
            $this->assertEquals( 'Vlan test',   $vlan->getName() );
            $this->assertEquals( '10',          $vlan->getNumber()  );
            $this->assertEquals( 1,             $vlan->getInfrastructure()->getId() );
            $this->assertEquals( 'vlan-test',   $vlan->getConfigName() );
            $this->assertEquals( true,          $vlan->getPrivate() );
            $this->assertEquals( true,          $vlan->getPeeringMatrix() );
            $this->assertEquals( true,          $vlan->getPeeringManager() );
            $this->assertEquals( 'test notes',  $vlan->getNotes() );

            // 3. browse to edit infrastructure object:
            $browser->click( '#d2f-list-edit-' .  $vlan->getId() );

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'name',      'Vlan test')
                    ->assertInputValue( 'number',    '10' )
                    ->assertSelected(   'infrastructureid',  1 )
                    ->assertInputValue( 'config_name',    'vlan-test' )
                    ->assertChecked(    'private')
                    ->assertChecked(    'peering_matrix')
                    ->assertChecked(    'peering_manager')
                    ->assertInputValue( 'notes',    'test notes' );




            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'infrastructureid',     '2' )
                    ->uncheck('private')
                    ->uncheck('peering_matrix')
                    ->uncheck('peering_manager')
                    ->press('Save Changes')
                    ->assertPathIs('/vlan/list')
                    ->assertSee( "VLAN edited" );


            // 6. repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $vlan );

            $this->assertInstanceOf( VlanEntity::class, $vlan );
            $this->assertEquals( 'Vlan test',   $vlan->getName() );
            $this->assertEquals( '10',          $vlan->getNumber()  );
            $this->assertEquals( 2,             $vlan->getInfrastructure()->getId() );
            $this->assertEquals( 'vlan-test',   $vlan->getConfigName() );
            $this->assertEquals( false,         $vlan->getPrivate() );
            $this->assertEquals( false,         $vlan->getPeeringMatrix() );
            $this->assertEquals( false,         $vlan->getPeeringManager() );
            $this->assertEquals( 'test notes',  $vlan->getNotes() );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/vlan/edit/' .  $vlan->getId() )
                ->assertSee( 'Edit VLAN' );

            $browser->assertInputValue( 'name',      'Vlan test')
                    ->assertInputValue( 'number',    '10' )
                    ->assertSelected(   'infrastructureid',  2 )
                    ->assertInputValue( 'config_name',    'vlan-test' )
                    ->assertNotChecked( 'private')
                    ->assertNotChecked( 'peering_matrix')
                    ->assertNotChecked( 'peering_manager')
                    ->assertInputValue( 'notes',    'test notes' );


            // 8. submit with no changes and verify no changes in database
            $browser->press('Save Changes')
                ->assertPathIs('/vlan/list');


            // 9. repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $vlan );

            $this->assertInstanceOf( VlanEntity::class, $vlan );
            $this->assertEquals( 'Vlan test',   $vlan->getName() );
            $this->assertEquals( '10',          $vlan->getNumber()  );
            $this->assertEquals( 2,             $vlan->getInfrastructure()->getId() );
            $this->assertEquals( 'vlan-test',   $vlan->getConfigName() );
            $this->assertEquals( false,         $vlan->getPrivate() );
            $this->assertEquals( false,         $vlan->getPeeringMatrix() );
            $this->assertEquals( false,         $vlan->getPeeringManager() );
            $this->assertEquals( 'test notes',  $vlan->getNotes() );

            // 10. edit again and check all checkboxes and submit
            $browser->visit( '/vlan/edit/' .  $vlan->getId() )
                ->assertSee( 'Edit VLAN' );

            $browser->check('private')
                    ->check('peering_matrix')
                    ->check('peering_manager')
                    ->press('Save Changes')
                    ->assertPathIs('/vlan/list');


            // 11. verify checkbox bool elements in database are all true
            D2EM::refresh( $vlan );

            $this->assertInstanceOf( VlanEntity::class, $vlan );
            $this->assertEquals( 'Vlan test',   $vlan->getName() );
            $this->assertEquals( '10',          $vlan->getNumber()  );
            $this->assertEquals( 2,             $vlan->getInfrastructure()->getId() );
            $this->assertEquals( 'vlan-test',   $vlan->getConfigName() );
            $this->assertEquals( true,          $vlan->getPrivate() );
            $this->assertEquals( true,          $vlan->getPeeringMatrix() );
            $this->assertEquals( true,          $vlan->getPeeringManager() );
            $this->assertEquals( 'test notes',  $vlan->getNotes() );


            // 12. edit all inputs
            $browser->visit( '/vlan/edit/' .  $vlan->getId() )
                ->assertSee( 'Edit VLAN' );

            $browser->type(     'name',         'Vlan test2')
                    ->type(     'number',    '11' )
                    ->select(   'infrastructureid',  1 )
                    ->type(     'config_name',    'vlan-test2' )
                    ->check(    'private')
                    ->check(    'peering_matrix')
                    ->check(    'peering_manager')
                    ->type(     'notes',    'test notes2' )
                    ->press('Save Changes')
                    ->assertPathIs('/vlan/list');

            D2EM::refresh( $vlan );

            $this->assertInstanceOf( VlanEntity::class, $vlan );
            $this->assertEquals( 'Vlan test2',   $vlan->getName() );
            $this->assertEquals( '11',          $vlan->getNumber()  );
            $this->assertEquals( 1,             $vlan->getInfrastructure()->getId() );
            $this->assertEquals( 'vlan-test2',   $vlan->getConfigName() );
            $this->assertEquals( true,          $vlan->getPrivate() );
            $this->assertEquals( true,          $vlan->getPeeringMatrix() );
            $this->assertEquals( true,          $vlan->getPeeringManager() );
            $this->assertEquals( 'test notes2',  $vlan->getNotes() );


            // 13. Add duplicate vlan
            $browser->visit( '/vlan/add' )
                    ->assertSee( 'Add VLAN' )
                    ->type(     'name',         'Vlan test2')
                    ->type(     'number',    '11' )
                    ->select(   'infrastructureid',  1 )
                    ->type(     'config_name',    'vlan-test2' )
                    ->check(    'private')
                    ->check(    'peering_matrix')
                    ->check(    'peering_manager')
                    ->type(     'notes',    'test notes2' )
                    ->press('Add')
                    ->assertPathIs('/vlan/add')
                    ->assertSee( "The couple Infrastructure and config name already exist" )
                    ->visit( '/vlan/list' );

            // 14. delete the router in the UI and verify via success message text and location
            $browser->visit( '/vlan/list/' )
                ->press('#d2f-list-delete-dd-' . $vlan->getId() )
                ->press('#d2f-list-delete-' . $vlan->getId() )
                ->waitForText( 'Do you really want to delete this VLAN' )
                ->press('Delete' );

            $browser->assertSee( 'VLAN deleted.' );

            // 12. do a D2EM findOneBy and verify false/null
            $this->assertEquals( null, D2EM::getRepository( VlanEntity::class )->findOneBy( [ 'name' => 'Vlan test' ] ) );
        });

    }
}
