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

use Entities\Contact as ContactEntity;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ContactControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        foreach( [ 'Test Contact 1', 'Test Contact 2' ] as $name ) {
            $c = D2EM::getRepository( ContactEntity::class )->findOneBy( [ 'name' => $name ] );

            if( $c ) {
                D2EM::remove( $c );
                D2EM::flush();
            }
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
                    ->visit('/logout')
                    ->visit('/login')
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( '#login-btn' )
                    ->assertPathIs( '/admin' );

            $browser->visit( '/contact/list' )
                ->assertSee( 'HEAnet CustAdmin' )
                ->assertSee( 'heanet-custadmin@example.com' );

            $browser->visit( '/contact/add' )
                ->assertSee( 'Add Contact' )
                ->assertSee( 'Name' )
                ->assertSee( 'Position' );


            // 1. test add :
            $browser->type( 'name', 'Test Contact 1' )
                ->select( 'custid', 5 )
                ->type( 'position', 'Test Position' )
                ->type( 'email',    'test-contact1@example.com' )
                ->type( 'phone',    '0209110000' )
                ->type( 'mobile',   '0209120000' )
                // ->check( 'facilityaccess' )
                // ->check( 'mayauthorize' )
                ->type( 'notes', 'Test note' )
                ->press('Add' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact added' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( 'Test Position' )
                ->assertSee( 'test-contact1@example.com' );

            // get the contact:
            /** @var ContactEntity $c */
            $c = D2EM::getRepository( ContactEntity::class )->findOneBy( [ 'name' => 'Test Contact 1' ] );

            // test the values:
            $this->assertEquals( 'Test Contact 1',            $c->getName() );
            $this->assertEquals( 'Test Position',             $c->getPosition() );
            $this->assertEquals( 'test-contact1@example.com', $c->getEmail() );
            $this->assertEquals( '0209110000',                $c->getPhone() );
            $this->assertEquals( '0209120000',                $c->getMobile() );
            $this->assertEquals( 5,                           $c->getCustomer()->getId() );
            // $this->assertEquals( true,                        $c->getFacilityaccess() );
            // $this->assertEquals( true,                        $c->getMayauthorize() );
            $this->assertEquals( 'Test note',                 $c->getNotes() );


            // test that editing while not making any changes and saving changes nothing

            $browser->visit( '/contact/edit/' . $c->getId() )
                ->assertPathIs('/contact/edit/' . $c->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact edited' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( 'Test Position' )
                ->assertSee( 'test-contact1@example.com' );

            // test the values:
            D2EM::refresh($c);
            $this->assertEquals( 'Test Contact 1',            $c->getName() );
            $this->assertEquals( 'Test Position',             $c->getPosition() );
            $this->assertEquals( 'test-contact1@example.com', $c->getEmail() );
            $this->assertEquals( '0209110000',                $c->getPhone() );
            $this->assertEquals( '0209120000',                $c->getMobile() );
            $this->assertEquals( 5,                           $c->getCustomer()->getId() );
            // $this->assertEquals( true,                        $c->getFacilityaccess() );
            // $this->assertEquals( true,                        $c->getMayauthorize() );
            $this->assertEquals( 'Test note',                 $c->getNotes() );




            // now test that editing while making changes works
            $browser->visit( '/contact/edit/' . $c->getId() )
                ->assertPathIs('/contact/edit/' . $c->getId() )
                ->type( 'name', 'Test Contact 2' )
                ->select( 'custid', 2 )
                ->type( 'position', 'Test Position2' )
                ->type( 'email',    'test-contact2@example.com' )
                ->type( 'phone',    '0209110002' )
                ->type( 'mobile',   '0209120002' )
                // ->uncheck( 'facilityaccess' )
                // ->uncheck( 'mayauthorize' )
                ->type( 'notes', 'Test note 2' )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact edited' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            D2EM::refresh($c);
            $this->assertEquals( 'Test Contact 2',            $c->getName() );
            $this->assertEquals( 'Test Position2',             $c->getPosition() );
            $this->assertEquals( 'test-contact2@example.com', $c->getEmail() );
            $this->assertEquals( '0209110002',                $c->getPhone() );
            $this->assertEquals( '0209120002',                $c->getMobile() );
            $this->assertEquals( 2,                           $c->getCustomer()->getId() );
            // $this->assertEquals( false,                       $c->getFacilityaccess() );
            // $this->assertEquals( false,                       $c->getMayauthorize() );
            $this->assertEquals( 'Test note 2',                 $c->getNotes() );


            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)
            $browser->visit( '/contact/edit/' . $c->getId() )
                ->assertPathIs('/contact/edit/' . $c->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact edited' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            D2EM::refresh($c);
            $this->assertEquals( 'Test Contact 2',            $c->getName() );
            $this->assertEquals( 'Test Position2',             $c->getPosition() );
            $this->assertEquals( 'test-contact2@example.com', $c->getEmail() );
            $this->assertEquals( '0209110002',                $c->getPhone() );
            $this->assertEquals( '0209120002',                $c->getMobile() );
            $this->assertEquals( 2,                           $c->getCustomer()->getId() );
            // $this->assertEquals( false,                       $c->getFacilityaccess() );
            // $this->assertEquals( false,                       $c->getMayauthorize() );
            $this->assertEquals( 'Test note 2',                 $c->getNotes() );

            // delete this contact
            $browser->press( '#d2f-list-delete-' . $c->getId() )
                ->waitForText( 'Do you really want to delete this contact?' )
                ->press( 'Delete' )
                ->assertPathIs('/contact/list' )
                ->assertDontSee( 'Test Contact 2' )
                ->assertDontSee( 'Test Position2' )
                ->assertDontSee( 'test-contact2@example.com' );

            $this->assertNull( D2EM::getRepository( ContactEntity::class )->findOneBy( [ 'name' => 'Test Contact 2' ] ) );

        });


        $this->browse(function (Browser $browser) {

            $browser->visit( '/customer/overview/5/contacts' )
                ->assertSee( 'Imagine CustAdmin' )
                ->assertSee( 'imagine-custadmin@example.com' )
                ->press( '#contacts-add-btn' )
                ->assertSee( 'Add Contact' )
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
                // ->check( 'facilityaccess' )
                // ->check( 'mayauthorize' )
                ->type( 'notes', 'Test note' )
                ->press('Add' )
                ->assertPathIs('/customer/overview/5/contacts' )
                ->assertSee( 'Contact added' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( '0209110000 / 0209120000' )
                ->assertSee( 'test-contact1@example.com' );

            // get the contact:
            /** @var ContactEntity $c */
            $c = D2EM::getRepository( ContactEntity::class )->findOneBy( [ 'name' => 'Test Contact 1' ] );

            // test the values:
            $this->assertEquals( 'Test Contact 1',            $c->getName() );
            $this->assertEquals( 'Test Position',             $c->getPosition() );
            $this->assertEquals( 'test-contact1@example.com', $c->getEmail() );
            $this->assertEquals( '0209110000',                $c->getPhone() );
            $this->assertEquals( '0209120000',                $c->getMobile() );
            $this->assertEquals( 5,                           $c->getCustomer()->getId() );
            // $this->assertEquals( true,                        $c->getFacilityaccess() );
            // $this->assertEquals( true,                        $c->getMayauthorize() );
            $this->assertEquals( 'Test note',                 $c->getNotes() );


            // test that editing while not making any changes and saving changes nothing

            $browser->press( '#cont-list-edit-' . $c->getId() )
                ->assertPathIs('/contact/edit/' . $c->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/customer/overview/5/contacts' );


            // delete this contact
            $browser->press( '#cont-list-delete-' . $c->getId() )
                ->waitForText( 'Do you really want to delete this contact?' )
                ->press( 'Delete' )
                ->assertPathIs('/customer/overview/5/contacts' )
                ->assertSee( 'Contact deleted' )
                ->assertDontSee( 'Test Contact 1' )
                ->assertDontSee( 'Test Position' )
                ->assertDontSee( 'test-contact1@example.com' );

            $this->assertNull( D2EM::getRepository( ContactEntity::class )->findOneBy( [ 'name' => 'Test Contact 1' ] ) );

            $browser->visit('/logout')
                ->assertPathIs( '/login' );
        });

    }







    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testAddCustAdmin()
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

            $browser->visit( '/contact/add' )
                ->assertSee( 'Add Contact' )
                ->assertSee( 'Name' )
                ->assertSee( 'Position' );


            // 1. test add :
            $browser->type( 'name', 'Test Contact 1' )
                ->type( 'position', 'Test Position' )
                ->type( 'email',    'test-contact1@example.com' )
                ->type( 'phone',    '0209110000' )
                ->type( 'mobile',   '0209120000' )
                ->press('Add' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact added' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( 'Test Position' )
                ->assertSee( 'test-contact1@example.com' );

            // get the contact:
            /** @var ContactEntity $c */
            $c = D2EM::getRepository( ContactEntity::class )->findOneBy( [ 'name' => 'Test Contact 1' ] );

            // test the values:
            $this->assertEquals( 'Test Contact 1',            $c->getName() );
            $this->assertEquals( 'Test Position',             $c->getPosition() );
            $this->assertEquals( 'test-contact1@example.com', $c->getEmail() );
            $this->assertEquals( '0209110000',                $c->getPhone() );
            $this->assertEquals( '0209120000',                $c->getMobile() );
            $this->assertEquals( 5,                           $c->getCustomer()->getId() );

            // test that editing while not making any changes and saving changes nothing

            $browser->visit( '/contact/edit/' . $c->getId() )
                ->assertPathIs('/contact/edit/' . $c->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact edited' )
                ->assertSee( 'Test Contact 1' )
                ->assertSee( 'Test Position' )
                ->assertSee( 'test-contact1@example.com' );

            // test the values:
            D2EM::refresh($c);
            $this->assertEquals( 'Test Contact 1',            $c->getName() );
            $this->assertEquals( 'Test Position',             $c->getPosition() );
            $this->assertEquals( 'test-contact1@example.com', $c->getEmail() );
            $this->assertEquals( '0209110000',                $c->getPhone() );
            $this->assertEquals( '0209120000',                $c->getMobile() );
            $this->assertEquals( 5,                           $c->getCustomer()->getId() );


            // now test that editing while making changes works
            $browser->visit( '/contact/edit/' . $c->getId() )
                ->assertPathIs('/contact/edit/' . $c->getId() )
                ->type( 'name', 'Test Contact 2' )
                ->type( 'position', 'Test Position2' )
                ->type( 'email',    'test-contact2@example.com' )
                ->type( 'phone',    '0209110002' )
                ->type( 'mobile',   '0209120002' )
                ->click( '.btn-primary' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact edited' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            D2EM::refresh($c);
            $this->assertEquals( 'Test Contact 2',            $c->getName() );
            $this->assertEquals( 'Test Position2',             $c->getPosition() );
            $this->assertEquals( 'test-contact2@example.com', $c->getEmail() );
            $this->assertEquals( '0209110002',                $c->getPhone() );
            $this->assertEquals( '0209120002',                $c->getMobile() );
            $this->assertEquals( 5,                           $c->getCustomer()->getId() );


            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)
            $browser->visit( '/contact/edit/' . $c->getId() )
                ->assertPathIs('/contact/edit/' . $c->getId() )
                ->click( '.btn-primary' )
                ->assertPathIs('/contact/list' )
                ->assertSee( 'Contact edited' )
                ->assertSee( 'Test Contact 2' )
                ->assertSee( 'Test Position2' )
                ->assertSee( 'test-contact2@example.com' );

            // test the values:
            D2EM::refresh($c);
            $this->assertEquals( 'Test Contact 2',            $c->getName() );
            $this->assertEquals( 'Test Position2',            $c->getPosition() );
            $this->assertEquals( 'test-contact2@example.com', $c->getEmail() );
            $this->assertEquals( '0209110002',                $c->getPhone() );
            $this->assertEquals( '0209120002',                $c->getMobile() );
            $this->assertEquals( 5,                           $c->getCustomer()->getId() );

            // delete this contact
            $browser->press( '#d2f-list-delete-' . $c->getId() )
                ->waitForText( 'Do you really want to delete this contact?' )
                ->press( 'Delete' )
                ->assertPathIs('/contact/list' )
                ->assertDontSee( 'Test Contact 2' )
                ->assertDontSee( 'Test Position2' )
                ->assertDontSee( 'test-contact2@example.com' );

            $this->assertNull( D2EM::getRepository( ContactEntity::class )->findOneBy( [ 'name' => 'Test Contact 2' ] ) );

            $browser->visit('/logout')
                ->assertPathIs( '/login' );

        });


    }
}
