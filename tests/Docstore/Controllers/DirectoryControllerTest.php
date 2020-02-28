<?php

namespace Tests\Docstore\Controllers;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM;

use Entities\User as UserEntity;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use IXP\Models\DocstoreDirectory;

use Tests\TestCase;

class DirectoryControllerTest extends TestCase
{

    const testInfo = [
        'custuser'              => 'hecustuser',
        'custadmin'             => 'hecustadmin',
        'superuser'             => 'travis',
        'folderName'            => 'Folder 3',
        'folderDescription'     => 'This is the folder 3',
        'parentDirId'           => null,
        'folderName2'           => 'Folder 3-1',
        'folderDescription2'    => 'This is the folder 3-1',
        'parentDirId2'          => 1,
    ];

    /**
     * Test the access to the list
     *
     * @return void
     */
    public function testList()
    {
        $response = $this->get( route('docstore-dir@list' ) );
        $response->assertOk()
            ->assertViewIs( 'docstore.dir.list' )
            ->assertSeeText('Document Store');
    }

    /**
     * Test the access to the create form for a public user
     *
     * @return void
     */
    public function testCreateFormAccessPublicUser()
    {
        // public user
        $response = $this->get( route( 'docstore-dir@create' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the create form for a custuser
     *
     * @return void
     */
    public function testCreateFormAccessCustUser()
    {
        // test custuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-dir@create' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the create form for a custadmin
     *
     * @return void
     */
    public function testCreateFormAccessCustAdmin()
    {
        // test custadmin
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-dir@create' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the create form for a superuser
     *
     * @return void
     */
    public function testCreateFormAccessSuperUser()
    {
        // test Superuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-dir@create' ) );
        $response->assertOk()
            ->assertViewIs('docstore.dir.create' );
    }

    /**
     * Test the access to the edit form for a public user
     *
     * @return void
     */
    public function testEditFormAccessPublicUser()
    {
        $dir = DocstoreDirectory::inRandomOrder()->first();

        // public user
        $response = $this->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the edit form for a custuser
     *
     * @return void
     */
    public function testEditFormAccessCustUser()
    {
        $dir = DocstoreDirectory::inRandomOrder()->first();

        // test custuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ]  ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the edit form for a custadmin
     *
     * @return void
     */
    public function testEditFormAccessCustAdmin()
    {
        $dir = DocstoreDirectory::inRandomOrder()->first();

        // test custadmin
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ]  ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the edit form for a superuser
     *
     * @return void
     */
    public function testEditFormAccessSuperUser()
    {
        $dir = DocstoreDirectory::inRandomOrder()->first();

        // test Superuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) );
        $response->assertOk()
            ->assertViewIs('docstore.dir.create' );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStorePublicUser()
    {
        // public user
        $response = $this->post( route( 'docstore-dir@store' ), [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $response->assertStatus(403 );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test store an object for a cust user
     *
     * @return void
     */
    public function testStoreCustUser()
    {
        // test custuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );
        $response = $this->actingAs( $user )->post( route( 'docstore-dir@store' ), [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $response->assertStatus(403 );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test store an object for a cust admin
     *
     * @return void
     */
    public function testStoreCustAdmin()
    {
        // test custadmin
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $response = $this->actingAs( $user )->post( route( 'docstore-dir@store' ), [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $response->assertStatus(403 );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );

    }

    /**
     * Test store an object for a superuser
     *
     * @return void
     */
    public function testStoreSuperUser()
    {
        // test Superuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $this->actingAs( $user )->post( route( 'docstore-dir@store' ), [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test update an object with a post method
     *
     * @return void
     */
    public function testUpdateWithPostMethod()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // public user
        $response = $this->post( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(405 );
    }

    /**
     * Test update an object for a public user
     *
     * @return void
     */
    public function testUpdatePublicUser()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // public user
        $response = $this->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(403 );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // cust user
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );
        $response = $this->actingAs( $user )->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(403 );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a cust admin
     *
     * @return void
     */
    public function testUpdateCustAdmin()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // cust admin
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $response = $this->actingAs( $user )->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
        $response->assertStatus(403 );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a superuser
     *
     * @return void
     */
    public function testUpdateSuperUser()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // superuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $this->actingAs( $user )->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir' => self::testInfo[ 'parentDirId2' ] ] );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object with a post method
     *
     * @return void
     */
    public function testDeleteWithPostMethod()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // public user
        $response = $this->post( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(405 );
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeleteForPublicUser()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // public user
        $response = $this->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // cust user
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );
        $response = $this->actingAs( $user )->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // cust user
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $response = $this->actingAs( $user )->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser()
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // superuser
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $this->actingAs( $user )->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }
}