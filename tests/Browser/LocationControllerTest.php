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

use IXP\Models\Location;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test location Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LocationControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'Infrastructure Test', 'Infrastructure Test2' ] as $name ) {
            if( $c = Location::whereName( $name )->first() ) {
                $c->delete();
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
                    ->type( 'username', 'travis'    )
                    ->type( 'password', 'travisci'  )
                    ->press( '#login-btn'   )
                    ->assertPathIs( '/admin'    );

            $browser->visit( '/facility/list'   )
                    ->assertSee( 'Facilities'   )
                    ->assertSee( 'Location 1'   );

            $browser->visit( '/facility/create'     )
                    ->assertSee( 'Create Facility' )
                    ->pause( 500 )
                    ->waitForText( "Choose the matching PeeringDB facility..." );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            // 1. test add empty inputs
            $browser->press('Create' )
                ->assertPathIs('/facility/create' )
                ->waitForText( "Choose the matching PeeringDB facility..." )
                ->assertSee( "The name field is required." )
                ->assertSee( "The shortname field is required." )
                ->assertSee( "The tag field is required." );

            // 1. test add
            $browser->type( 'name',         'Infrastructure Test'   )
                    ->type( 'shortname',        'l1'                )
                    ->type( 'tag',              'test tag'          )
                    ->select( 'pdb_facility_id', 4                  )
                    ->type( 'address',          'test address'      )
                    ->type( 'city',             'Dublin'            )
                    ->select('country',        'IE'             )
                    ->type( 'nocphone',         '0209101231'    )
                    ->type( 'nocfax',           '0209101232'    )
                    ->type( 'nocemail',         'bad-noc-email' )
                    ->type( 'officephone',      '0209101233'    )
                    ->type( 'officefax',        '0209101234'    )
                    ->type( 'officeemail',      'bad-office-email' )
                    ->type( 'notes',            'test notes'    );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->press('Create')
                    ->assertPathIs('/facility/create')
                    ->waitForText('Equinix DA1 - Dallas' )
                    ->assertSee( "The shortname has already been taken" )
                    ->assertSee( "The nocemail must be a valid email address" )
                    ->assertSee( "The officeemail must be a valid email address" )
                    ->type( 'shortname', 'test')
                    ->type( 'nocemail', 'nocemail@example.com' )
                    ->type( 'officeemail', 'officeemail@example.com' );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->press('Create')
                    ->assertPathIs('/facility/list')
                    ->assertSee( "Facility created" );

            $location = Location::whereName( 'Infrastructure Test' )->first();

            // 2. test added data in database against expected values
            $this->assertInstanceOf( Location::class, $location );
            
            $this->assertEquals( 'Infrastructure Test',        $location->name              );
            $this->assertEquals( 'test',                       $location->shortname         );
            $this->assertEquals( 'test tag',                   $location->tag               );
            $this->assertEquals( '4',                          $location->pdb_facility_id   );
            $this->assertEquals( 'test address',               $location->address           );
            $this->assertEquals( 'Dublin',                     $location->city              );
            $this->assertEquals( 'IE',                         $location->country           );
            $this->assertEquals( '0209101231',                 $location->nocphone          );
            $this->assertEquals( '0209101232',                 $location->nocfax            );
            $this->assertEquals( 'nocemail@example.com',       $location->nocemail          );
            $this->assertEquals( '0209101233',                 $location->officephone       );
            $this->assertEquals( '0209101234',                 $location->officefax         );
            $this->assertEquals( 'officeemail@example.com',    $location->officeemail       );
            $this->assertEquals( 'test notes',                 $location->notes             );

            // 3. browse to edit infrastructure object:
            $browser->click( '#e2f-list-edit-' .  $location->id );

            // 4. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->waitForText('Equinix DA1 - Dallas' )
                    ->assertInputValue('name',                  'Infrastructure Test' )
                    ->assertInputValue( 'shortname',            'test'      )
                    ->assertInputValue( 'tag',                  'test tag'  )
                    ->assertSelected( 'pdb_facility_id',        4           )
                    ->assertInputValue( 'city',                   'Dublin'  )
                    ->assertSelected( 'country',                'IE'        )
                    ->assertInputValue( 'address',              'test address'  )
                    ->assertInputValue( 'nocphone',             '0209101231'    )
                    ->assertInputValue( 'nocfax',               '0209101232'    )
                    ->assertInputValue( 'nocemail',             'nocemail@example.com' )
                    ->assertInputValue( 'officephone',          '0209101233'    )
                    ->assertInputValue( 'officefax',            '0209101234'    )
                    ->assertInputValue( 'officeemail',          'officeemail@example.com' )
                    ->assertInputValue( 'notes',                'test notes'    );

            // 5. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'pdb_facility_id',     '10' );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->press('Save Changes'  )
                    ->assertPathIs('/facility/list' )
                    ->assertSee( "Facility updated" );


            // 6. repeat database load and database object check for new values (repeat 2)
            $location->refresh();

            $this->assertEquals( 'Infrastructure Test',     $location->name             );
            $this->assertEquals( 'test',                    $location->shortname        );
            $this->assertEquals( 'test tag',                $location->tag              );
            $this->assertEquals( '10',                      $location->pdb_facility_id  );
            $this->assertEquals( 'test address',            $location->address          );
            $this->assertEquals( 'Dublin',                  $location->city             );
            $this->assertEquals( 'IE',                      $location->country          );
            $this->assertEquals( '0209101231',              $location->nocphone         );
            $this->assertEquals( '0209101232',              $location->nocfax           );
            $this->assertEquals( 'nocemail@example.com',    $location->nocemail         );
            $this->assertEquals( '0209101233',              $location->officephone      );
            $this->assertEquals( '0209101234',              $location->officefax        );
            $this->assertEquals( 'officeemail@example.com', $location->officeemail      );
            $this->assertEquals( 'test notes',              $location->notes            );


            // 7. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( '/facility/edit/' .  $location->id )
                ->assertSee( 'Edit Facility' )
                ->waitForText('Digital Realty NYC (60 Hudson)' );

            $browser->assertInputValue('name',              'Infrastructure Test')
                    ->assertInputValue( 'shortname',        'test'      )
                    ->assertInputValue( 'tag',              'test tag'  )
                    ->assertSelected( 'pdb_facility_id',    10 )
                    ->assertInputValue( 'address',          'test address' )
                    ->assertInputValue( 'nocphone',         '0209101231'    )
                    ->assertInputValue( 'nocfax',           '0209101232'    )
                    ->assertInputValue( 'nocemail',         'nocemail@example.com' )
                    ->assertInputValue( 'officephone',      '0209101233' )
                    ->assertInputValue( 'officefax',        '0209101234' )
                    ->assertInputValue( 'officeemail',      'officeemail@example.com' )
                    ->assertInputValue( 'notes',            'test notes' );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            // 8. submit with no changes and verify no changes in database
            $browser->press('Save Changes')
                ->assertPathIs('/facility/list');


            // 6. repeat database load and database object check for new values (repeat 2)
            $location->refresh();

            $this->assertEquals( 'Infrastructure Test',     $location->name             );
            $this->assertEquals( 'test',                    $location->shortname        );
            $this->assertEquals( 'test tag',                $location->tag              );
            $this->assertEquals( '10',                      $location->pdb_facility_id  );
            $this->assertEquals( 'test address',            $location->address          );
            $this->assertEquals( 'Dublin',                  $location->city             );
            $this->assertEquals( 'IE',                      $location->country          );
            $this->assertEquals( '0209101231',              $location->nocphone         );
            $this->assertEquals( '0209101232',              $location->nocfax           );
            $this->assertEquals( 'nocemail@example.com',    $location->nocemail         );
            $this->assertEquals( '0209101233',              $location->officephone      );
            $this->assertEquals( '0209101234',              $location->officefax        );
            $this->assertEquals( 'officeemail@example.com', $location->officeemail      );
            $this->assertEquals( 'test notes',              $location->notes            );

            // 9. edit object
            $browser->visit( '/facility/edit/' .  $location->id )
                ->assertSee( 'Edit Facility' )
                ->waitForText('Digital Realty NYC (60 Hudson)' );

            $browser->type( 'name',         'Infrastructure Test2')
                    ->type( 'shortname',        'test2'     )
                    ->type( 'tag',              'test tag2' )
                    ->select( 'pdb_facility_id', 11     )
                    ->select( 'country',        'FR'    )
                    ->type( 'city',             'Paris' )
                    ->type( 'address',          'test address2' )
                    ->type( 'nocphone',         '0209101235' )
                    ->type( 'nocfax',           '0209101236' )
                    ->type( 'nocemail',         'nocemail2@example.com' )
                    ->type( 'officephone',      '0209101237' )
                    ->type( 'officefax',        '0209101238' )
                    ->type( 'officeemail',      'officeemail2@example.com' )
                    ->type( 'notes',            'test notes2' );

            $browser->driver->executeScript('window.scrollTo(0, 3000);');

            $browser->press('Save Changes')
                    ->assertPathIs('/facility/list');


            // 10. verify object values
            $location->refresh();

            $this->assertEquals( 'Infrastructure Test2',        $location->name             );
            $this->assertEquals( 'test2',                       $location->shortname        );
            $this->assertEquals( 'test tag2',                   $location->tag              );
            $this->assertEquals( '11',                          $location->pdb_facility_id  );
            $this->assertEquals( 'test address2',               $location->address          );
            $this->assertEquals( 'Paris',                       $location->city             );
            $this->assertEquals( 'FR',                          $location->country          );
            $this->assertEquals( '0209101235',                  $location->nocphone         );
            $this->assertEquals( '0209101236',                  $location->nocfax           );
            $this->assertEquals( 'nocemail2@example.com',       $location->nocemail         );
            $this->assertEquals( '0209101237',                  $location->officephone      );
            $this->assertEquals( '0209101238',                  $location->officefax        );
            $this->assertEquals( 'officeemail2@example.com',    $location->officeemail      );
            $this->assertEquals( 'test notes2',                 $location->notes            );

            // 11. delete the router in the UI and verify via success message text and location
            $browser->visit( '/facility/list/' )
                ->click('#e2f-list-delete-' . $location->id )
                ->waitForText( 'Do you really want to delete this facility' )
                ->press('Delete' );

            $browser->assertSee( 'Facility deleted.' );

            // 12. do a D2EM findOneBy and verify false/null
            $this->assertTrue( Location::whereName( 'Infrastructure Test2' )->doesntExist() );
        });
    }
}