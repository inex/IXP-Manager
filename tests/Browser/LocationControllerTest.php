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
    Location as LocationEntity
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LocationControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        $location = D2EM::getRepository( LocationEntity::class )->findOneBy( [ 'name' => 'Infrastructure Test' ] );
        if( $location ) {
            D2EM::remove( $location );
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

            $browser->visit( '/facility/list' )
                ->assertSee( 'Facilities' );

            $browser->visit( '/facility/add' )
                ->assertSee( 'Add Facility' )
                ->waitForText( "Choose the matching PeeringDB Facility..." );

            // 1. test add empty inputs
            $browser->press('Add')
                ->assertPathIs('/facility/add')
                ->assertSee( "The name field is required." )
                ->assertSee( "The shortname field is required." )
                ->assertSee( "The tag field is required." );

            // 1. test add
            $browser->type( 'name',         'Infrastructure Test')
                ->type( 'shortname',        'l1' )
                ->type( 'tag',              'test tag' )
                ->select( 'pdb_facility_id', 4 )
                ->type( 'address',          'test address' )
                ->type( 'nocphone',         '0831862961' )
                ->type( 'nocfax',           '0831862963' )
                ->type( 'nocemail',         'nocemail' )
                ->type( 'officephone',      '0831862970' )
                ->type( 'officefax',        '0831862971' )
                ->type( 'officeemail',      'officeemail' )
                ->type( 'notes',            'test notes' )
                ->press('Add')
                ->assertPathIs('/facility/add')
                ->assertSee( "The shortname has already been taken" )
                ->assertSee( "The nocemail must be a valid email address." )
                ->assertSee( "The officeemail must be a valid email address." )
                ->type( 'shortname',         'test')
                ->type( 'nocemail',    'nocemail@test.com' )
                ->type( 'officeemail',    'officeemail@test.com' )
                ->press('Add')
                ->assertPathIs('/facility/list')
                ->assertSee( "Facility added" );

            /** @var LocationEntity $location */
            $location = D2EM::getRepository( LocationEntity::class )->findOneBy( [ 'name' => 'Infrastructure Test' ] );

            // 2. test added data in database against expected values
            $this->assertInstanceOf( LocationEntity::class, $location );
            $this->assertEquals( 'Infrastructure Test',     $location->getName() );
            $this->assertEquals( 'test',                    $location->getShortname() );
            $this->assertEquals( 'test tag',                $location->getTag() );
            $this->assertEquals( '4',                       $location->getPdbFacilityId() );
            $this->assertEquals( 'test address',            $location->getAddress() );
            $this->assertEquals( '0831862961',              $location->getNocphone() );
            $this->assertEquals( '0831862963',              $location->getNocfax() );
            $this->assertEquals( 'nocemail@test.com',       $location->getNocemail() );
            $this->assertEquals( '0831862970',              $location->getOfficephone() );
            $this->assertEquals( '0831862971',              $location->getOfficefax() );
            $this->assertEquals( 'officeemail@test.com',    $location->getOfficeemail() );
            $this->assertEquals( 'test notes',              $location->getNotes() );

            // 3. browse to edit infrastructure object:
            $browser->click( '#d2f-list-edit-' .  $location->getId() )
                    ->pause( 5000);

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue('name',                  'Infrastructure Test')
                    ->assertInputValue( 'shortname',            'test' )
                    ->assertInputValue( 'tag',                  'test tag' )
                    ->assertSelected( 'pdb_facility_id',        4 )
                    ->assertInputValue( 'address',              'test address' )
                    ->assertInputValue( 'nocphone',             '0831862961' )
                    ->assertInputValue( 'nocfax',               '0831862963' )
                    ->assertInputValue( 'nocemail',             'nocemail@test.com' )
                    ->assertInputValue( 'officephone',          '0831862970' )
                    ->assertInputValue( 'officefax',            '0831862971' )
                    ->assertInputValue( 'officeemail',          'officeemail@test.com' )
                    ->assertInputValue( 'notes',                'test notes' );

            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'pdb_facility_id',     '10' )
                    ->press('Save Changes')
                    ->assertPathIs('/facility/list')
                    ->assertSee( "Facility edited" );


            // 6. repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $location );

            $this->assertInstanceOf( LocationEntity::class, $location );
            $this->assertEquals( 'Infrastructure Test',     $location->getName() );
            $this->assertEquals( 'test',                    $location->getShortname() );
            $this->assertEquals( 'test tag',                $location->getTag() );
            $this->assertEquals( '10',                      $location->getPdbFacilityId() );
            $this->assertEquals( 'test address',            $location->getAddress() );
            $this->assertEquals( '0831862961',              $location->getNocphone() );
            $this->assertEquals( '0831862963',              $location->getNocfax() );
            $this->assertEquals( 'nocemail@test.com',       $location->getNocemail() );
            $this->assertEquals( '0831862970',              $location->getOfficephone() );
            $this->assertEquals( '0831862971',              $location->getOfficefax() );
            $this->assertEquals( 'officeemail@test.com',    $location->getOfficeemail() );
            $this->assertEquals( 'test notes',              $location->getNotes() );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/facility/edit/' .  $location->getId() )
                ->assertSee( 'Edit Facility' )
                ->waitForText( "Choose the matching PeeringDB Facility..." );;

            $browser->assertInputValue('name',              'Infrastructure Test')
                    ->assertInputValue( 'shortname',        'test' )
                    ->assertInputValue( 'tag',              'test tag' )
                    ->assertSelected( 'pdb_facility_id',    10 )
                    ->assertInputValue( 'address',          'test address' )
                    ->assertInputValue( 'nocphone',         '0831862961' )
                    ->assertInputValue( 'nocfax',           '0831862963' )
                    ->assertInputValue( 'nocemail',         'nocemail@test.com' )
                    ->assertInputValue( 'officephone',      '0831862970' )
                    ->assertInputValue( 'officefax',        '0831862971' )
                    ->assertInputValue( 'officeemail',      'officeemail@test.com' )
                    ->assertInputValue( 'notes',            'test notes' );


            // 8. submit with no changes and verify no changes in database
            $browser->press('Save Changes')
                ->assertPathIs('/facility/list');


            // 6. repeat database load and database object check for new values (repeat 2)
            D2EM::refresh( $location );

            $this->assertInstanceOf( LocationEntity::class, $location );
            $this->assertEquals( 'Infrastructure Test',     $location->getName() );
            $this->assertEquals( 'test',                    $location->getShortname() );
            $this->assertEquals( 'test tag',                $location->getTag() );
            $this->assertEquals( '10',                      $location->getPdbFacilityId() );
            $this->assertEquals( 'test address',            $location->getAddress() );
            $this->assertEquals( '0831862961',              $location->getNocphone() );
            $this->assertEquals( '0831862963',              $location->getNocfax() );
            $this->assertEquals( 'nocemail@test.com',       $location->getNocemail() );
            $this->assertEquals( '0831862970',              $location->getOfficephone() );
            $this->assertEquals( '0831862971',              $location->getOfficefax() );
            $this->assertEquals( 'officeemail@test.com',    $location->getOfficeemail() );
            $this->assertEquals( 'test notes',              $location->getNotes() );

            // 9. edit object
            $browser->visit( '/facility/edit/' .  $location->getId() )
                ->assertSee( 'Edit Facility' )
                ->pause( 5000 );

            $browser->type( 'name',         'Infrastructure Test2')
                    ->type( 'shortname',        'test2' )
                    ->type( 'tag',              'test tag2' )
                    ->select( 'pdb_facility_id', 11 )
                    ->type( 'address',          'test address2' )
                    ->type( 'nocphone',         '0831862964' )
                    ->type( 'nocfax',           '0831862965' )
                    ->type( 'nocemail',         'nocemail2@test.com' )
                    ->type( 'officephone',      '0831862972' )
                    ->type( 'officefax',        '0831862973' )
                    ->type( 'officeemail',      'officeemail2@test.com' )
                    ->type( 'notes',            'test notes2' )
                    ->press('Save Changes')
                    ->assertPathIs('/facility/list');


            // 10. verify object values
            D2EM::refresh( $location );

            $this->assertInstanceOf( LocationEntity::class, $location );
            $this->assertEquals( 'Infrastructure Test2',        $location->getName() );
            $this->assertEquals( 'test2',                       $location->getShortname() );
            $this->assertEquals( 'test tag2',                   $location->getTag() );
            $this->assertEquals( '11',                          $location->getPdbFacilityId() );
            $this->assertEquals( 'test address2',               $location->getAddress() );
            $this->assertEquals( '0831862964',                  $location->getNocphone() );
            $this->assertEquals( '0831862965',                  $location->getNocfax() );
            $this->assertEquals( 'nocemail2@test.com',          $location->getNocemail() );
            $this->assertEquals( '0831862972',                  $location->getOfficephone() );
            $this->assertEquals( '0831862973',                  $location->getOfficefax() );
            $this->assertEquals( 'officeemail2@test.com',       $location->getOfficeemail() );
            $this->assertEquals( 'test notes2',                 $location->getNotes() );

            // 11. delete the router in the UI and verify via success message text and location
            $browser->visit( '/facility/list/' )
                ->click('#d2f-list-delete-' . $location->getId() )
                ->waitForText( 'Do you really want to delete this a facility' )
                ->press('Delete' );

            $browser->assertSee( 'Facility deleted.' );

            // 12. do a D2EM findOneBy and verify false/null
            $this->assertEquals( null, D2EM::getRepository( LocationEntity::class )->findOneBy( [ 'name' => 'Infrastructure Test2' ] ) );
        });

    }
}
