<?php

namespace Tests\Docstore\Controllers;

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

use IXP\Models\DocstoreDirectory;

use Tests\TestCase;

/**
 * Test docstore directory Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Docstore\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DirectoryControllerTest extends TestCase
{
    public const testInfo = [
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
    public function testList(): void
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
    public function testCreateFormAccessPublicUser(): void
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
    public function testCreateFormAccessCustUser(): void
    {
        // test custuser
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-dir@create' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the create form for a custadmin
     *
     * @return void
     */
    public function testCreateFormAccessCustAdmin(): void
    {
        // test custadmin
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-dir@create' ) );
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
        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-dir@create' ) );
        $response->assertOk()
            ->assertViewIs('docstore.dir.create' );
    }

    /**
     * Test the access to the edit form for a public user
     *
     * @return void
     */
    public function testEditFormAccessPublicUser(): void
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
    public function testEditFormAccessCustUser(): void
    {
        $dir = DocstoreDirectory::inRandomOrder()->first();

        // test custuser
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the edit form for a custadmin
     *
     * @return void
     */
    public function testEditFormAccessCustAdmin(): void
    {
        $dir = DocstoreDirectory::inRandomOrder()->first();

        // test custadmin
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the edit form for a superuser
     *
     * @return void
     */
    public function testEditFormAccessSuperUser(): void
    {
        $dir = DocstoreDirectory::inRandomOrder()->first();

        // test Superuser
        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) );
        $response->assertOk()
            ->assertViewIs('docstore.dir.create' );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStorePublicUser(): void
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
    public function testStoreCustUser(): void
    {
        // test custuser
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->post( route( 'docstore-dir@store' ),
                [
                    'name'              =>  self::testInfo[ 'folderName' ],
                    'description'       => self::testInfo[ 'folderDescription' ],
                    'parent_dir_id'     => self::testInfo[ 'parentDirId' ]
                ]
            );
        $response->assertStatus(403 );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test store an object for a cust admin
     *
     * @return void
     */
    public function testStoreCustAdmin(): void
    {
        // test custadmin
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->post( route( 'docstore-dir@store' ), [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $response->assertStatus(403 );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );

    }

    /**
     * Test store an object for a superuser
     *
     * @return void
     */
    public function testStoreSuperUser(): void
    {
        // test Superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-dir@store' ),
                [
                    'name'              => self::testInfo[ 'folderName' ],
                    'description'       => self::testInfo[ 'folderDescription' ],
                    'parent_dir_id'     => self::testInfo[ 'parentDirId' ]
                ]
            );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test update an object with a post method
     *
     * @return void
     */
    public function testUpdateWithPostMethod(): void
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
    public function testUpdatePublicUser(): void
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // public user
        $response = $this->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ),
            [
                'name'          =>  self::testInfo[ 'folderName2' ],
                'description'   => self::testInfo[ 'folderDescription2' ],
                'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
            ]
        );
        $response->assertStatus(403 );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser(): void
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // cust user
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ),
                [
                    'name'          =>  self::testInfo[ 'folderName2' ],
                    'description'   => self::testInfo[ 'folderDescription2' ],
                    'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
                ]
            );
        $response->assertStatus(403 );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a cust admin
     *
     * @return void
     */
    public function testUpdateCustAdmin(): void
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // cust admin
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ),
                [
                    'name'          =>  self::testInfo[ 'folderName2' ],
                    'description'   => self::testInfo[ 'folderDescription2' ],
                    'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
                ]
            );
        $response->assertStatus(403 );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test update an object for a superuser
     *
     * @return void
     */
    public function testUpdateSuperUser(): void
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName' ] ] )->first();

        // superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ),
            [
                'name'          =>  self::testInfo[ 'folderName2' ],
                'description'   => self::testInfo[ 'folderDescription2' ],
                'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
            ]
        );
        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object with a post method
     *
     * @return void
     */
    public function testDeleteWithPostMethod(): void
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
    public function testDeleteForPublicUser(): void
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
    public function testDeleteCustUser(): void
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // cust user
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin(): void
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // cust admin
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $response->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser(): void
    {
        $dir = DocstoreDirectory::where( [ 'name' => self::testInfo[ 'folderName2' ] ] )->first();

        // superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }
}