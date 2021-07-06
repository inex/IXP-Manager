<?php

namespace Tests\DocstoreCustomer\Controllers;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Foundation\Testing\WithoutMiddleware;

use IXP\Models\{
    Customer,
    DocstoreCustomerDirectory
};

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

    /**
     * Test the access to the list for public user
     *
     * @return void
     */
    public function testListCustomerForPublicUser(): void
    {
        $response = $this->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
    }

    /**
     * Test the access to the list for cust user
     *
     * @return void
     */
    public function testListCustomerForCustUser(): void
    {
        $user = $this->getCustUser( 'hecustuser' );
        $response = $this->actingAs( $user )->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the list for cust admin
     *
     * @return void
     */
    public function testListCustomerForCustAdmin(): void
    {
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $response = $this->actingAs( $user )->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the list for super user
     *
     * @return void
     */
    public function testListCustomerFoSuperUser(): void
    {
        $user = $this->getSuperUser( 'travis' );
        $response = $this->actingAs( $user )->get( route('docstore-c-dir@customers', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(200 )
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
        $response = $this->get( route('docstore-c-dir@list', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(302 )
                ->assertRedirect( route('login@showForm' ) );
    }

    /**
     * Test the access to the create form for a public user
     *
     * @return void
     */
    public function testCreateFormAccessPublicUser(): void
    {
        // public user
        $response = $this->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
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
        $response = $this->actingAs( $user )->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(403 );
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
        $response = $this->actingAs( $user )->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the create form for a superuser
     *
     * @return void
     */
    public function testCreateFormAccessSuperUser(): void
    {
        // test Superuser
        $user = $this->getSuperUser( 'travis' );
        $response = $this->actingAs( $user )->get( route( 'docstore-c-dir@create', [ 'cust' => self::testInfo[ 'customerId' ] ] ) );
        $response->assertOk()
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
        $response = $this->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
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
        $user = $this->getCustUser( 'hecustuser' );
        $response = $this->actingAs( $user )->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] );
        $response->assertStatus(403 );
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
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $response = $this->actingAs( $user )->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] );
        $response->assertStatus(403 );
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
        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test the access to the edit form for a public user
     *
     * @return void
     */
    public function testEditFormAccessPublicUser(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'name', self::testInfo[ 'folderName' ] )->first();

        // public user
        $response = $this->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  , 'dir' => $dir ] ) );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
    }

    /**
     * Test the access to the edit form for a custuser
     *
     * @return void
     */
    public function testEditFormAccessCustUser(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'name', self::testInfo[ 'folderName' ] )->first();

        // test custuser
        $user = $this->getCustUser( 'hecustuser' );
        $response = $this->actingAs( $user )->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  ,'dir' => $dir ] ) );
        $response->assertStatus(404 );
    }

    /**
     * Test the access to the edit form for a custadmin
     *
     * @return void
     */
    public function testEditFormAccessCustAdmin(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'name', self::testInfo[ 'folderName' ] )->first();

        // test custadmin
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $response = $this->actingAs( $user )->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  , 'dir' => $dir ] ) );
        $response->assertStatus(404 );
    }

    /**
     * Test the access to the edit form for a superuser
     *
     * @return void
     */
    public function testEditFormAccessSuperUser(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'name', self::testInfo[ 'folderName' ] )->first();

        // test Superuser
        $user = $this->getSuperUser( 'travis' );
        $response = $this->actingAs( $user )->get( route( 'docstore-c-dir@edit', [ 'cust' => $dir->cust_id  , 'dir' => $dir ] ) );
        $response->assertOk()
            ->assertViewIs('docstore-customer.dir.create' );
    }

    /**
     * Test update an object with a post method
     *
     * @return void
     */
    public function testUpdateWithPostMethod(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // public user
        $response = $this->post( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(405 );
    }

    /**
     * Test update an object for a public user
     *
     * @return void
     */
    public function testUpdatePublicUser(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // public user
        $response = $this->put( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
        $this->assertDatabaseHas(       'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseMissing(   'docstore_customer_directories', [ 'cust_id' => $dir->cust_id, 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser()
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // cust user
        $user = $this->getCustUser( 'hecustuser' );
        $response = $this->actingAs( $user )->put( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(404 );
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
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // cust admin
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $response = $this->actingAs( $user )->put( route( 'docstore-c-dir@update', [  'cust' => $dir->cust_id, 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(404 );
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
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // superuser
        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )->put( route( 'docstore-c-dir@update', [ 'cust' => $dir->cust_id , 'dir' => $dir ] ), [ 'cust_id' => $dir->cust_id , 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
        $this->assertDatabaseMissing(   'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseHas(       'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object with a post method
     *
     * @return void
     */
    public function testDeleteWithPostMethod(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // public user
        $response = $this->post( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(405 );
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeleteForPublicUser(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // public user
        $response = $this->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser()
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // cust user
        $user = $this->getCustUser( 'hecustuser' );
        $response = $this->actingAs( $user )->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(404 );
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin(): void
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // cust user
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $response = $this->actingAs( $user )->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(404 );
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser()
    {
        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // superuser
        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )->delete( route( 'docstore-c-dir@delete', [ 'dir' => $dir ] ) );
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

        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'cust_id', $cust->id )->get()->last();
        // public user
        $response = $this->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) );
        $response->assertStatus(302 )
            ->assertRedirect( route('login@showForm' ) );
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

        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'cust_id', $cust->id )->get()->last();

        // cust user
        $user = $this->getCustUser( 'hecustuser' );
        $response = $this->actingAs( $user )->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) );
        $response->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'name' => $dir->name,    'description' => $dir->description ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteAllForCustAdmin()
    {
        $cust = Customer::whereId( self::testInfo[ 'customerId' ] )->first();

        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'cust_id', $cust->id )->get()->last();
        // cust admin
        $user = $this->getCustAdminUser( 'hecustadmin' );
        $response = $this->actingAs( $user )->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) );
        $response->assertStatus(403 );
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

        $dir = DocstoreCustomerDirectory::withoutGlobalScope( 'privs' )->where( 'cust_id', $cust->id )->get()->last();
        // superuser
        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )->delete( route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $cust ] ) );
        $this->assertDatabaseMissing( 'docstore_customer_directories', [ 'name' => $dir->name,    'description' => $dir->description, 'parent_dir_id' => $dir->parent_dir ] );
    }
}