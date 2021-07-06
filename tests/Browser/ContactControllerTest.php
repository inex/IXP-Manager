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

use IXP\Models\Contact;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

/**
 * Test Contact Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        foreach( [ 'Test Contact 1', 'Test Contact 2' ] as $name ) {
            if( $c = Contact::whereName( $name )->first() ) {
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
     * @throws Throwable
     */
    public function testAdd(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                    ->visit('/logout')
                    ->visit('/login')
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( '#login-btn' )
                    ->assertPathIs( '/admin' );

            $browser->visit( '/contact/list' )
                ->assertSee( 'HEAnet CustAdmin' )
                ->assertSee( 'heanet-custadmin@example.com' );

            $browser->visit( '/contact/create' )
                ->assertSee( 'Create Contact' )
                ->assertSee( 'Name' )
                ->assertSee( 'Position' );


            // 1. test add :
            $browser->type( 'name', 'Test Contact 1'            )
                ->select( 'custid', 5                           )
                ->type( 'position', 'Test Position'             )
                ->type( 'email',    'test-contact1@example.com' )
                ->type( 'phone',    '0209110000'                )
                ->type( 'mobile',   '0209120000'                )
                ->type( 'notes',    'Test note'                 )
                ->press('Create' )
                ->assertPathIs('/contact/list'             )
                ->assertSee( 'Contact created'              )
                ->assertSee( 'Test Contact 1'               )
                ->assertSee( 'Test Position'                )
                ->assertSee( 'test-contact1@example.com'    );

            // get the contact:
            /** @var Contact $c */
            $c = Contact::whereName( 'Test Contact 1' )->first();

            // test the values:
            $this->assertEquals( 'Test Contact 1',            $c->name      );
            $this->assertEquals( 'Test Position',             $c->position  );
            $this->assertEquals( 'test-contact1@example.com', $c->email     );
            $this->assertEquals( '0209110000',                $c->phone     );
            $this->assertEquals( '0209120000',                $c->mobile    );
            $this->assertEquals( 5,                           $c->custid    );
            $this->assertEquals( 'Test note',                 $c->notes     );


            // test that editing while not making any changes and saving changes nothing

            $browser->visit( '/contact/edit/' . $c->id      )
                ->assertPathIs('/contact/edit/' . $c->id    )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list'  )
                ->assertSee( 'Contact updated'  )
                ->assertSee( 'Test Contact 1'   )
                ->assertSee( 'Test Position'    )
                ->assertSee( 'test-contact1@example.com' );

            // test the values:
            $c->refresh();
            $this->assertEquals( 'Test Contact 1',            $c->name      );
            $this->assertEquals( 'Test Position',             $c->position  );
            $this->assertEquals( 'test-contact1@example.com', $c->email     );
            $this->assertEquals( '0209110000',                $c->phone     );
            $this->assertEquals( '0209120000',                $c->mobile    );
            $this->assertEquals( 5,                           $c->custid    );
            $this->assertEquals( 'Test note',                 $c->notes     );

            // now test that editing while making changes works
            $browser->visit( '/contact/edit/' . $c->id       )
                ->assertPathIs('/contact/edit/' . $c->id    )
                ->type( 'name', 'Test Contact 2' )
                ->select( 'custid', 2 )
                ->type( 'position', 'Test Position2' )
                ->type( 'email',    'test-contact2@example.com' )
                ->type( 'phone',    '0209110002' )
                ->type( 'mobile',   '0209120002' )
                ->type( 'notes', 'Test note 2' )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact updated' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            $c->refresh();
            $this->assertEquals( 'Test Contact 2',            $c->name );
            $this->assertEquals( 'Test Position2',             $c->position );
            $this->assertEquals( 'test-contact2@example.com', $c->email );
            $this->assertEquals( '0209110002',                $c->phone );
            $this->assertEquals( '0209120002',                $c->mobile );
            $this->assertEquals( 2,                           $c->custid );
            $this->assertEquals( 'Test note 2',               $c->notes );


            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)
            $browser->visit( '/contact/edit/' . $c->id )
                ->assertPathIs('/contact/edit/' . $c->id )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact update' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            $c->refresh();
            $this->assertEquals( 'Test Contact 2',            $c->name );
            $this->assertEquals( 'Test Position2',            $c->position );
            $this->assertEquals( 'test-contact2@example.com', $c->email );
            $this->assertEquals( '0209110002',                $c->phone );
            $this->assertEquals( '0209120002',                $c->mobile );
            $this->assertEquals( 2,                           $c->custid );
            $this->assertEquals( 'Test note 2',                $c->notes );

            // delete this contact
            $browser->press( '#e2f-list-delete-' . $c->id )
                ->waitForText( 'Do you really want to delete this contact?' )
                ->press( 'Delete' )
                ->assertPathIs('/contact/list' )
                ->assertDontSee( 'Test Contact 2' )
                ->assertDontSee( 'Test Position2' )
                ->assertDontSee( 'test-contact2@example.com' );

            $this->assertTrue( Contact::whereName( 'Test Contact 2' )->doesntExist() );
        });


        $this->browse(function (Browser $browser) {

            $browser->visit( '/customer/overview/5/contacts' )
                ->waitForText( 'Imagine CustAdmin' )
                ->assertSee( 'imagine-custadmin@example.com' )
                ->press( '#contacts-add-btn' )
                ->assertSee( 'Create Contact' )
                ->assertSee( 'Name' )
                ->assertSee( 'Position' )
                ->assertSelected( 'custid', 5 );


            // 1. test add :
            $browser->type( 'name', 'Test Contact 1' )
                ->select( 'custid', 5 )
                ->type( 'position', 'Test Position' )
                ->type( 'email',    'test-contact1@example.com' )
                ->type( 'phone',    '0209110000' )
                ->type( 'mobile',   '0209120000' )
                ->type( 'notes', 'Test note' )
                ->press('Create' )
                ->assertPathIs('/customer/overview/5/contacts' )
                ->assertSee( 'Contact created' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( '0209110000 / 0209120000' )
                ->assertSee( 'test-contact1@example.com' );

            // get the contact:
            $c = Contact::whereName( 'Test Contact 1' )->first();
            // test the values:
            $this->assertEquals( 'Test Contact 1',            $c->name );
            $this->assertEquals( 'Test Position',             $c->position );
            $this->assertEquals( 'test-contact1@example.com', $c->email );
            $this->assertEquals( '0209110000',                $c->phone );
            $this->assertEquals( '0209120000',                $c->mobile );
            $this->assertEquals( 5,                           $c->custid );
            $this->assertEquals( 'Test note',                 $c->notes );


            // test that editing while not making any changes and saving changes nothing

            $browser->press( '#cont-list-edit-' . $c->id )
                ->assertPathIs('/contact/edit/' . $c->id )
                ->press( 'Save Changes' )
                ->assertPathIs('/customer/overview/5/contacts' );


            // delete this contact
            $browser->press( '#btn-delete-' . $c->id )
                ->pause( 500 )
                ->waitForText( 'Delete Contact' )
                ->press( 'Delete' )
                ->assertPathIs('/customer/overview/5/contacts' )
                ->assertSee( 'Contact deleted' )
                ->assertDontSee( 'Test Contact 1' )
                ->assertDontSee( 'Test Position' )
                ->assertDontSee( 'test-contact1@example.com' );

            $this->assertTrue( Contact::whereName( 'Test Contact 1' )->doesntExist() );

            $browser->visit('/logout')
                ->assertPathIs( '/login' );
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws
     */
    public function testAddCustAdmin(): void
    {

        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                ->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/dashboard' )
                ->visit('/contact/list')
                ->assertSee( 'Your Contacts' )
                ->assertSee( 'Imagine CustAdmin' )
                ->assertSee( 'imagine-custadmin@example.com' );

            $browser->visit( '/contact/create' )
                ->assertSee( 'Create Contact' )
                ->assertSee( 'Name' )
                ->assertSee( 'Position' );


            // 1. test add :
            $browser->type( 'name', 'Test Contact 1' )
                ->type( 'position', 'Test Position' )
                ->type( 'email',    'test-contact1@example.com' )
                ->type( 'phone',    '0209110000' )
                ->type( 'mobile',   '0209120000' )
                ->press('Create' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact created' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( 'Test Position' )
                ->assertSee( 'test-contact1@example.com' );

            // get the contact:
            $c = Contact::whereName( 'Test Contact 1' )->first();

            // test the values:
            $this->assertEquals( 'Test Contact 1',            $c->name );
            $this->assertEquals( 'Test Position',             $c->position );
            $this->assertEquals( 'test-contact1@example.com', $c->email );
            $this->assertEquals( '0209110000',                $c->phone );
            $this->assertEquals( '0209120000',                $c->mobile );
            $this->assertEquals( 5,                           $c->custid );

            // test that editing while not making any changes and saving changes nothing

            $browser->visit( '/contact/edit/' . $c->id )
                ->assertPathIs('/contact/edit/' . $c->id )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact updated' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( 'Test Position' )
                ->assertSee( 'test-contact1@example.com' );

            // test the values:
            $c->refresh();
            $this->assertEquals( 'Test Contact 1',            $c->name );
            $this->assertEquals( 'Test Position',             $c->position );
            $this->assertEquals( 'test-contact1@example.com', $c->email );
            $this->assertEquals( '0209110000',                $c->phone );
            $this->assertEquals( '0209120000',                $c->mobile );
            $this->assertEquals( 5,                           $c->custid );


            // now test that editing while making changes works
            $browser->visit( '/contact/edit/' . $c->id )
                ->assertPathIs('/contact/edit/' . $c->id )
                ->type( 'name', 'Test Contact 2' )
                ->type( 'position', 'Test Position2' )
                ->type( 'email',    'test-contact2@example.com' )
                ->type( 'phone',    '0209110002' )
                ->type( 'mobile',   '0209120002' )
                ->click( '.btn-primary' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact updated' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            $c->refresh();
            $this->assertEquals( 'Test Contact 2',            $c->name );
            $this->assertEquals( 'Test Position2',            $c->position );
            $this->assertEquals( 'test-contact2@example.com', $c->email );
            $this->assertEquals( '0209110002',                $c->phone );
            $this->assertEquals( '0209120002',                $c->mobile );
            $this->assertEquals( 5,                           $c->custid );


            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)
            $browser->visit( '/contact/edit/' . $c->id )
                ->assertPathIs('/contact/edit/' . $c->id )
                ->click( '.btn-primary' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact updated' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            $c->refresh();
            $this->assertEquals( 'Test Contact 2',            $c->name );
            $this->assertEquals( 'Test Position2',            $c->position );
            $this->assertEquals( 'test-contact2@example.com', $c->email );
            $this->assertEquals( '0209110002',                $c->phone );
            $this->assertEquals( '0209120002',                $c->mobile );
            $this->assertEquals( 5,                           $c->custid );

            // delete this contact
            $browser->press( '#e2f-list-delete-' . $c->id )
                ->waitForText( 'Do you really want to delete this contact?' )
                ->press( 'Delete' )
                ->assertPathIs('/contact/list' )
                ->assertDontSee( 'Test Contact 2' )
                ->assertDontSee( 'Test Position2' )
                ->assertDontSee( 'test-contact2@example.com' );

            $this->assertTrue( Contact::whereName( 'Test Contact 2' )->doesntExist() );

            $browser->visit('/logout')
                ->assertPathIs( '/login' );

        });
    }
}