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

namespace Tests\Docstore\Controllers;


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

    private function insertDocstoreDirectoryFixture1(): DocstoreDirectory
    {
        $dir = new DocstoreDirectory();
        $dir->name = self::testInfo[ 'folderName' ]; // Folder 3
        $dir->description = self::testInfo[ 'folderDescription' ]; // This is the folder 3
        $dir->parent_dir_id = self::testInfo[ 'parentDirId' ];  // null
        $dir->save();
        return $dir;
    }

    private function insertDocstoreDirectoryFixture2(): DocstoreDirectory
    {
        $dir = new DocstoreDirectory();
        $dir->name = self::testInfo[ 'folderName2' ]; // Folder 3
        $dir->description = self::testInfo[ 'folderDescription2' ]; // This is the folder 3
        $dir->parent_dir_id = self::testInfo[ 'parentDirId2' ];  // null
        $dir->save();
        return $dir;
    }

    /**
     * Test the access to the list
     *
     * @return void
     */
    public function testList(): void
    {
        $this->get( route('docstore-dir@list' ) )
            ->assertOk()
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
        $this->get( route( 'docstore-dir@create' ) )
            ->assertRedirectToRoute( 'login@showForm' );
    }

    /**
     * Test the access to the create form for a custuser
     *
     * @return void
     */
    public function testCreateFormAccessCustUser(): void
    {
        // test custuser
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-dir@create' ) )
            ->assertForbidden();
    }

    /**
     * Test the access to the create form for a custadmin
     *
     * @return void
     */
    public function testCreateFormAccessCustAdmin(): void
    {
        // test custadmin
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-dir@create' ) )
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
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-dir@create' ) )
            ->assertOk()
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
        $this->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) )
            ->assertRedirectToRoute( 'login@showForm' );
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
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) )
            ->assertForbidden();
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
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) )
            ->assertForbidden();
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
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-dir@edit', [ 'dir' => $dir ] ) )
            ->assertOk()
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
        $this->post( route( 'docstore-dir@store' ), [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] )
            ->assertRedirectToRoute( 'login@showForm' );

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
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->post( route( 'docstore-dir@store' ),
                [
                    'name'              => self::testInfo[ 'folderName' ],
                    'description'       => self::testInfo[ 'folderDescription' ],
                    'parent_dir_id'     => self::testInfo[ 'parentDirId' ]
                ]
            )
            ->assertForbidden();
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
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->post( route( 'docstore-dir@store' ), [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] )
            ->assertForbidden();

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
        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-dir@store' ),
                [
                    'name'              => self::testInfo[ 'folderName' ],
                    'description'       => self::testInfo[ 'folderDescription' ],
                    'parent_dir_id'     => self::testInfo[ 'parentDirId' ]
                ]
            );
        $newDir = DocstoreDirectory::latest()->first();
        $response->assertRedirectToRoute('docstore-dir@list', [ 'dir' => $newDir->id ]);

        $this->assertDatabaseHas( 'docstore_directories', [ 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test update an object with a post method when the route is defined as PUT
     *
     * @return void
     */
    public function testUpdateWithPostMethodNotPut(): void
    {
        $dir = $this->insertDocstoreDirectoryFixture1();

        // public user
        $this->post( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [ 'name' =>  self::testInfo[ 'folderName2' ], 'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] )
            ->assertMethodNotAllowed();
    }

    /**
     * Test update an object for a public user
     *
     * @return void
     */
    public function testUpdatePublicUser(): void
    {
        $dir = $this->insertDocstoreDirectoryFixture1();

        // public user
        $this->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [
                'name'          => self::testInfo[ 'folderName2' ],
                'description'   => self::testInfo[ 'folderDescription2' ],
                'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
            ] )
            ->assertRedirectToRoute( 'login@showForm' );

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
        $dir = $this->insertDocstoreDirectoryFixture1();

        // cust user
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ),
                [
                    'name'          => self::testInfo[ 'folderName2' ],
                    'description'   => self::testInfo[ 'folderDescription2' ],
                    'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
                ]
            )
            ->assertForbidden();

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
        $dir = $this->insertDocstoreDirectoryFixture1();

        // cust admin
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [
                'name'          => self::testInfo[ 'folderName2' ],
                'description'   => self::testInfo[ 'folderDescription2' ],
                'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
            ] )
            ->assertForbidden();

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
        $dir = $this->insertDocstoreDirectoryFixture1();

        // superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->put( route( 'docstore-dir@update', [ 'dir' => $dir ] ), [
                'name'          => self::testInfo[ 'folderName2' ],
                'description'   => self::testInfo[ 'folderDescription2' ],
                'parent_dir_id' => self::testInfo[ 'parentDirId2' ]
            ] )
            ->assertRedirectToRoute('docstore-dir@list', [ 'dir' => self::testInfo[ 'parentDirId2' ] ]);

        $this->assertDatabaseMissing(   'docstore_directories', [ 'name' => self::testInfo[ 'folderName' ],     'description' => self::testInfo[ 'folderDescription' ],     'parent_dir_id' => self::testInfo[ 'parentDirId' ]  ] );
        $this->assertDatabaseHas(       'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ],    'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object with a post method (should be DELETE)
     *
     * @return void
     */
    public function testDeleteWithPostMethod(): void
    {
        $dir = $this->insertDocstoreDirectoryFixture2();

        // public user
        $this->post( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) )
            ->assertMethodNotAllowed();
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeleteForPublicUser(): void
    {
        $dir = $this->insertDocstoreDirectoryFixture2();

        // public user
        $this->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) )
            ->assertRedirectToRoute( 'login@showForm' );
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser(): void
    {
        $dir = $this->insertDocstoreDirectoryFixture2();

        // cust user
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) )
            ->assertForbidden();
        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin(): void
    {
        $dir = $this->insertDocstoreDirectoryFixture2();

        // cust admin
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) )
            ->assertForbidden();

        $this->assertDatabaseHas( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser(): void
    {
        $dir = $this->insertDocstoreDirectoryFixture2();

        // superuser
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-dir@delete', [ 'dir' => $dir ] ) );
        $this->assertDatabaseMissing( 'docstore_directories', [ 'name' => self::testInfo[ 'folderName2' ],    'description' => self::testInfo[ 'folderDescription2' ], 'parent_dir_id' => self::testInfo[ 'parentDirId2' ] ] );
    }
}