<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\DocstoreCustomer\Controllers;

use IXP\Models\{Customer, DocstoreCustomerDirectory};

use Tests\TestCase;

/**
 * Test docstore customer directory Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\DocstoreCustomer\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DirectoryControllerTest extends TestCase
{
    public const testInfo = [
        'customerId'            => 5,
        'folderName'            => 'Folder 3',
        'folderDescription'     => 'This is the folder 3',
        'parentDirId'           => null,
        'folderName2'           => 'Folder 3-1',
        'folderDescription2'    => 'This is the folder 3-1',
        'parentDirId2'          => 1,
    ];

    private function insertDocstoreCustomerDirectoryFixture1(): DocstoreCustomerDirectory
    {
        $dir = new DocstoreCustomerDirectory();
        $dir->name = self::testInfo[ 'folderName' ]; // Folder 3
        $dir->description = self::testInfo[ 'folderDescription' ]; // This is the folder 3
        $dir->parent_dir_id = self::testInfo[ 'parentDirId' ];  // null
        $dir->cust_id = self::testInfo[ 'customerId' ];
        $dir->save();
        return $dir;
    }

    private function insertDocstoreCustomerDirectoryFixture2(): DocstoreCustomerDirectory
    {
        $dir = new DocstoreCustomerDirectory();
        $dir->name = self::testInfo[ 'folderName2' ]; // Folder 3-1
        $dir->description = self::testInfo[ 'folderDescription2' ]; // This is the folder 3-1
        $dir->parent_dir_id = self::testInfo[ 'parentDirId2' ];  // null
        $dir->cust_id = self::testInfo[ 'customerId' ];
        $dir->save();
        return $dir;
    }

    /**
     * Test the access to the list for public user
     *
     * @return void
     */
    public function testListCustomerForPublicUser(): void
    {
        $this->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertRedirectToRoute('login@showForm' );
    }

    /**
     * Test the access to the list for cust user
     *
     * @return void
     */
    public function testListCustomerForCustUser(): void
    {
        $user = $this->getCustUser( 'hecustuser' );
        $this->actingAs( $user )->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertForbidden();
    }

    /**
     * Test the access to the list for cust admin
     *
     * @return void
     */
    public function testListCustomerForCustAdmin(): void
    {
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $this->actingAs( $user )->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertForbidden();
    }

    /**
     * Test the access to the list for super user
     *
     * @return void
     */
    public function testListCustomerFoSuperUser(): void
    {
        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertOk()
            ->assertViewIs( 'docstore-customer.dir.customers' )
            ->assertSee( 'HEAnet' )
            ->assertSee( 'Imagine' )
            ->assertSee( 'AS112' );
    }

    /**
     * Test the access to the list for public user
     *
     * @return void
     */
    public function testListForPublicUser(): void
    {
        $this->get( route('docstore-c-dir@list', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertRedirectToRoute('login@showForm'  );
    }

    /**
     * Test the access to the create form for a public user
     *
     * @return void
     */
    public function testCreateFormAccessPublicUser(): void
    {
        // public user
        $this->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertRedirectToRoute('login@showForm' );

    }

    /**
     * Test the access to the create form for a cust user
     *
     * @return void
     */
    public function testCreateFormAccessCustUser(): void
    {
        // test custuser
        $user = $this->getCustUser( 'hecustuser' );
        $this->actingAs( $user )->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
           ->assertForbidden();
    }

    /**
     * Test the access to the create form for a cust admin
     *
     * @return void
     */
    public function testCreateFormAccessCustAdmin(): void
    {
        // test custadmin
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $this->actingAs( $user )->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertForbidden();
    }

    /**
     * Test the access to the create form for a superuser
     *
     * @return void
     */
    public function testCreateFormAccessSuperUser(): void
    {
        // test Superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertOk()
            ->assertViewIs('docstore-customer.dir.create' );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStorePublicUser(): void
    {
        // public user
        $this->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] )
            ->assertRedirectToRoute('login@showForm' );

        $this->assertDatabaseMissing( 'docstore_customer_directories', [ 'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test store an object for a cust user
     *
     * @return void
     */
    public function testStoreCustUser(): void
    {
        // test custuser
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] )
            ->assertForbidden();

        $this->assertDatabaseMissing( 'docstore_customer_directories', [ 'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test store an object for a cust admin
     *
     * @return void
     */
    public function testStoreCustAdmin(): void
    {
        // test custadmin
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] )
            ->assertForbidden();
        $this->assertDatabaseMissing( 'docstore_customer_directories', [ 'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test store an object for a superuser
     *
     * @return void
     */
    public function testStoreSuperUser(): void
    {
        // test Superuser
        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] );
        $newDir = DocstoreCustomerDirectory::latest()->first();
        $response->assertRedirectToRoute('docstore-c-dir@list', [ 'cust' => self::testInfo[ 'customerId' ], 'dir' => $newDir->id ]);

        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test the access to the edit form for a public user
     *
     * @return void
     */
    public function testEditFormAccessPublicUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // public user
        $this->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  , 'dir' => $dir ] ) )
            ->assertRedirectToRoute('login@showForm' );

    }

    /**
     * Test the access to the edit form for a custuser
     *
     * @return void
     */
    public function testEditFormAccessCustUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // test custuser
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  ,'dir' => $dir ] ) )
            ->assertNotFound();
    }

    /**
     * Test the access to the edit form for a custadmin
     *
     * @return void
     */
    public function testEditFormAccessCustAdmin(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // test custadmin
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  , 'dir' => $dir ] ) )
            ->assertNotFound();
    }

    /**
     * Test the access to the edit form for a superuser
     *
     * @return void
     */
    public function testEditFormAccessSuperUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // test Superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  , 'dir' => $dir ] ) )
            ->assertOk()
            ->assertViewIs('docstore-customer.dir.create' );
    }

    /**
     * Test update an object with a post method when the route is defined as PUT
     *
     * @return void
     */
    public function testUpdateWithPostMethodNotPut(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // public user
        $this->post( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] )
            ->assertMethodNotAllowed();
    }

    /**
     * Test update an object for a public user
     *
     * @return void
     */
    public function testUpdatePublicUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // public user
        $this->put( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] )
            ->assertRedirectToRoute('login@showForm' );
        $this->assertDatabaseHas(       'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseMissing(   'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // cust user
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->put( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] )
            ->assertNotFound();
        $this->assertDatabaseHas(       'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseMissing(   'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a cust admin
     *
     * @return void
     */
    public function testUpdateCustAdmin(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        // cust admin
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->put( route( 'docstore-c-dir@update', [  'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] )
            ->assertNotFound();
        $this->assertDatabaseHas(       'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseMissing(   'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a superuser
     *
     * @return void
     */
    public function testUpdateSuperUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture1();

        $this->assertDatabaseHas(       'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );

        // superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->put( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id , 'dir' => $dir ] ), [ 'cust_id' => $dir->cust_id , 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] )
            ->assertRedirectToRoute('docstore-c-dir@list', [ 'cust' => $dir->cust_id, 'dir' => self::testInfo[ 'parentDirId2' ] ]);

        $this->assertDatabaseMissing(   'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseHas(       'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object with a post method
     *
     * @return void
     */
    public function testDeleteWithPostMethod(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        // public user
        $this->post( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) )
            ->assertMethodNotAllowed();
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeleteForPublicUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        // public user
        $this->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) )
            ->assertRedirectToRoute('login@showForm');
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        // cust user
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) )
            ->assertNotFound();
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        // cust user
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) )
            ->assertNotFound();
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser(): void
    {
        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        // superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) )
            ->assertRedirectToRoute('docstore-c-dir@list', [ 'cust' => $dir->customer , 'dir' => $dir->parent_dir_id ]);

        $this->assertDatabaseMissing( 'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeleteAllForPublicUser(): void
    {
        $cust = Customer::whereId( self::testInfo[ 'customerId' ] )->first();

        $dir = $this->insertDocstoreCustomerDirectoryFixture2();
        // public user
        $this->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) )
            ->assertRedirectToRoute('login@showForm');
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => $dir->name,    'description' => $dir->description ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteAllForCustUser(): void
    {
        $cust = Customer::whereId( self::testInfo[ 'customerId' ] )->first();

        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        // cust user
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) )
            ->assertForbidden();
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => $dir->name,    'description' => $dir->description ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteAllForCustAdmin(): void
    {
        $cust = Customer::whereId( self::testInfo[ 'customerId' ] )->first();
        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) )
            ->assertForbidden();
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => $dir->name,    'description' => $dir->description ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteAllForSuperUser(): void
    {
        $cust = Customer::whereId( self::testInfo[ 'customerId' ] )->first();
        $dir = $this->insertDocstoreCustomerDirectoryFixture2();

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) )
            ->assertRedirectToRoute( 'docstore-c-dir@customers' );
        $this->assertDatabaseMissing( 'docstore_customer_directories', [ 'name' => $dir->name,     'description' => $dir->description,     'parent_dir_id' => $dir->parent_dir_id, ] );
    }
}