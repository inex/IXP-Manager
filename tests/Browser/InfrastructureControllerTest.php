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
    Infrastructure as InfrastructureEntity,
    IXP            as IXPEntity
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class InfrastructureControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        if( $infra = D2EM::getRepository( InfrastructureEntity::class )->findOneBy( [ 'name' => 'Infrastructure PHPUnit' ] ) ) {
            D2EM::remove( $infra );
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

            $browser->visit( '/infrastructure/list' )
                ->assertSee( 'Infrastructures' )
                ->assertSee( 'represents a collection of switches which form an IXP\'s peering LAN' );

            $browser->visit( '/infrastructure/add' )
                ->assertSee( 'Add Infrastructure' )
                ->waitForText( "Choose the matching IX-F IXP" )
                ->waitForText( "Choose the matching PeeringDB IXP" );

            // 1. test add empty inputs
            $browser->press('Add')
                ->assertPathIs('/infrastructure/add')
                ->waitForText( "Choose the matching IX-F IXP" )
                ->waitForText( "Choose the matching PeeringDB IXP" )
                ->assertSee( "The name field is required." )
                ->assertSee( "The shortname field is required." );

            // 1. test add
            $browser->type( 'name',         'Infrastructure #1')
                    ->type( 'shortname',    'phpunit' )
                    ->check('primary')
                    ->select( 'ixf_ix_id',  1 )
                    ->select( 'pdb_ixp',    1 )
                    ->press('Add')
                    ->assertPathIs('/infrastructure/add')
                    ->assertSee( "The name has already been taken" )
                    ->type( 'name', 'Infrastructure PHPUnit')
                    ->waitForText( "LINX LON1" )
                    ->waitForText( "Equinix Ashburn" )
                    ->press('Add')
                    ->assertPathIs('/infrastructure/list')
                    ->assertSee( "Infrastructure added" )
                    ->assertSee( "Infrastructure PHPUnit" )
                    ->assertSee( "phpunit" );

            /** @var InfrastructureEntity $infra */
            $infra = D2EM::getRepository( InfrastructureEntity::class )->findOneBy( [ 'name' => 'Infrastructure PHPUnit' ] );

            // 2. test added data in database against expected values
            $this->assertInstanceOf( InfrastructureEntity::class, $infra );
            $this->assertEquals( 'Infrastructure PHPUnit',   $infra->getName() );
            $this->assertEquals( 'phpunit',                  $infra->getShortname() );
            $this->assertEquals( true,                       $infra->getIsPrimary() );
            $this->assertEquals( '1',                        $infra->getIxfIxId() );
            $this->assertEquals( '1',                        $infra->getPeeringdbIxId() );
            $this->assertEquals( D2EM::getRepository( IXPEntity::class )->getDefault()->getId() , $infra->getIXP()->getId() );

            // 3. browse to edit infrastructure object:
            $browser->click( '#d2f-list-edit-' .  $infra->getId() );

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue('name',      'Infrastructure PHPUnit')
                    ->assertInputValue('shortname', 'phpunit')
                    ->assertChecked( 'primary' )
                    ->waitForText( "LINX LON1" )
                    ->waitForText( "Equinix Ashburn" )
                    ->assertSelected('ixf_ix_id', '1')
                    ->assertSelected('pdb_ixp', '1');


            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'ixf_ix_id', '2' )
                    ->select( 'pdb_ixp',   '2' )
                    ->uncheck('primary')
                    ->press('Save Changes')
                    ->assertPathIs('/infrastructure/list')
                    ->assertSee( "Infrastructure edited" );


            // 6. repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $infra );

            $this->assertInstanceOf( InfrastructureEntity::class, $infra );
            $this->assertEquals( 'Infrastructure PHPUnit',   $infra->getName() );
            $this->assertEquals( 'phpunit',                  $infra->getShortname() );
            $this->assertEquals( false,                      $infra->getIsPrimary() );
            $this->assertEquals( '2',                        $infra->getIxfIxId() );
            $this->assertEquals( '2',                        $infra->getPeeringdbIxId() );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/infrastructure/edit/' .  $infra->getId() )
                ->assertSee( 'Edit Infrastructure' )
                ->waitForText( "AMS-IX" )
                ->waitForText( "Equinix Chicago" );

            $browser->assertInputValue('name',      'Infrastructure PHPUnit')
                ->assertInputValue('shortname', 'phpunit')
                ->assertNotChecked( 'primary' )
                ->assertSelected('ixf_ix_id', '2')
                ->assertSelected('pdb_ixp', '2');


            // 8. submit with no changes and verify no changes in database
            $browser->press('Save Changes');


            // 6. repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $infra );

            $this->assertInstanceOf( InfrastructureEntity::class, $infra );
            $this->assertEquals( 'Infrastructure PHPUnit',   $infra->getName() );
            $this->assertEquals( 'phpunit',        $infra->getShortname() );
            $this->assertEquals( false,          $infra->getIsPrimary() );
            $this->assertEquals( '2',           $infra->getIxfIxId() );
            $this->assertEquals( '2',           $infra->getPeeringdbIxId() );


            // 9. edit again and check all checkboxes and submit
            $browser->visit( '/infrastructure/edit/' .  $infra->getId() )
                ->assertSee( 'Edit Infrastructure' )
                ->waitForText( "AMS-IX" )
                ->waitForText( "Equinix Chicago" )
                ->check('primary')
                ->press('Save Changes')
                ->assertPathIs('/infrastructure/list');


            // 10. verify checkbox bool elements in database are all true
            D2EM::refresh( $infra );

            $this->assertEquals( true, $infra->getIsPrimary() );

            // 11. delete the router in the UI and verify via success message text and location
            $browser->visit( '/infrastructure/list/' )
                ->press('#d2f-list-delete-' . $infra->getId() )
                ->waitForText( 'Do you really want to delete this infrastructure' )
                ->press('Delete' );

            $browser->assertSee( 'Infrastructure deleted.' );

            // 12. do a D2EM findOneBy and verify false/null
            $this->assertEquals( null, D2EM::getRepository( InfrastructureEntity::class )->findOneBy( [ 'name' => 'Infrastructure PHPUnit' ] ) );
        });

    }
}
