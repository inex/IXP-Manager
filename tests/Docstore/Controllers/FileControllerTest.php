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


use IXP\Models\User;
use Storage;

use Illuminate\Http\UploadedFile;

use IXP\Models\DocstoreFile;

use Tests\TestCase;

/**
 * Test docstore File Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Docstore\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FileControllerTest extends TestCase
{
    public const testInfo = [
        'disk'                  => 'docstore',
        'fileName'              => 'File.pdf',
        'fileDescription'       => 'This is file.pdf',
        'filePrivs'             => User::AUTH_SUPERUSER,
        'parentDirId'           => null,
        'fileName2'             => 'File2.pdf',
        'fileDescription2'      => 'This is file2.pdf',
        'filePrivs2'            => User::AUTH_CUSTADMIN,
        'parentDirId2'          => 1,
        'fileName3'             => 'File3.txt',
        'fileDescription3'      => 'This is file3.txt',
        'textFile'              => 'I am the file3.txt',
        'filePrivs3'            => User::AUTH_CUSTADMIN,
        'parentDirId3'          => 1,
    ];

    private function insertDocstoreFileFixture1(UploadedFile $uploadedFile): DocstoreFile
    {
        $file = new DocstoreFile();
        $file->name = self::testInfo[ 'fileName' ];
        $file->disk = self::testInfo[ 'disk' ];
        $file->docstore_directory_id = self::testInfo[ 'parentDirId' ];
        $file->description = self::testInfo[ 'fileDescription' ];
        $file->path = $uploadedFile->store( '', 'docstore' );
        $file->sha256 = hash_file('sha256', $uploadedFile->getPathname());
        $file->min_privs = self::testInfo[ 'filePrivs' ];
        $file->created_by = $this->getSuperUser( 'travis' )->id;
        $file->file_last_updated = now();
        $file->save();
        return $file;
    }

    private function insertDocstoreFileFixture2(UploadedFile $uploadedFile): DocstoreFile
    {
        $file = new DocstoreFile();
        $file->name = self::testInfo[ 'fileName2' ];
        $file->disk = self::testInfo[ 'disk' ];
        $file->docstore_directory_id = self::testInfo[ 'parentDirId2' ];
        $file->description = self::testInfo[ 'fileDescription2' ];
        $file->path = $uploadedFile->store( '', 'docstore' );
        $file->sha256 = hash_file('sha256', $uploadedFile->getPathname());
        $file->min_privs = self::testInfo[ 'filePrivs2' ];
        $file->created_by = $this->getSuperUser( 'travis' )->id;
        $file->file_last_updated = now();
        $file->save();
        return $file;
    }

    private function insertDocstoreFileFixture3(UploadedFile $uploadedFile): DocstoreFile
    {
        $file = new DocstoreFile();
        $file->name = self::testInfo[ 'fileName3' ];
        $file->disk = self::testInfo[ 'disk' ];
        $file->docstore_directory_id = self::testInfo[ 'parentDirId3' ];
        $file->description = self::testInfo[ 'fileDescription3' ];
        $file->path = $uploadedFile->store( '', 'docstore' );
        $file->sha256 = hash_file('sha256', $uploadedFile->getPathname());
        $file->min_privs = self::testInfo[ 'filePrivs3' ];
        $file->created_by = $this->getSuperUser( 'travis' )->id;
        $file->file_last_updated = now();
        $file->save();
        return $file;
    }

    /**
     * Test the access to the upload form for a public user
     *
     * @return void
     */
    public function testUploadFormAccessPublicUser(): void
    {
        // public user
        $this->get( route( 'docstore-file@upload' ) )
            ->assertRedirectToRoute( 'login@showForm' );
    }

    /**
     * Test the access to the upload form for a cust user
     *
     * @return void
     */
    public function testUploadFormAccessCustUser(): void
    {
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@upload' ) )
            ->assertForbidden();
    }

    /**
     * Test the access to the upload form for a cust admin
     *
     * @return void
     */
    public function testUploadFormAccessCustAdmin(): void
    {
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@upload' ) )
            ->assertForbidden();
    }

    /**
     * Test the access to the upload form for a super user
     *
     * @return void
     */
    public function testUploadFormAccessSuperUser(): void
    {
        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@upload' ) )
            ->assertOk();
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStorePublicUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->post( route( 'docstore-file@store' ), [
            'name' => self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
        ] )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ], 'name' => self::testInfo[ 'fileName' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStoreCustUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  => self::testInfo[ 'fileName' ],
                'description'           => self::testInfo[ 'fileDescription' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs'             => self::testInfo[ 'filePrivs' ],
                'uploadedFile'          => $uploadedFile
            ] )
            ->assertForbidden();

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id'     => self::testInfo[ 'parentDirId' ],
            'name'                      => self::testInfo[ 'fileName' ],
            'disk'                      => 'docstore',
            'sha256'                    => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'               => self::testInfo[ 'fileDescription' ],
            'min_privs'                 => self::testInfo[ 'filePrivs' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStoreCustAdmin(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  => self::testInfo[ 'fileName' ],
                'description'           => self::testInfo[ 'fileDescription' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs'             => self::testInfo[ 'filePrivs' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertForbidden();

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  => self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a super user
     *
     * @return void
     */
    public function testStoreSuperUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  => self::testInfo[ 'fileName' ],
                'description'           => self::testInfo[ 'fileDescription' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs'             => self::testInfo[ 'filePrivs' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertRedirectToRoute(  'docstore-dir@list', [ 'dir' => self::testInfo[ 'parentDirId' ] ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  => self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ],
            'created_by'            => $user->id
        ] );

        Storage::disk(self::testInfo[ 'disk' ] )->assertExists( $uploadedFile->hashName() );
    }

    /**
     * Test store an object with no name
     *
     * @return void
     */
    public function testStoreWithoutName(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@store' ), [
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertRedirectBackWithErrors(['name' => 'The name field is required.']);

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'created_by'            => $user->id
        ] );
    }

    /**
     * Test store an object with no file
     *
     * @return void
     */
    public function testStoreWithoutFile(): void
    {
        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  => self::testInfo[ 'fileName' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ]
        ] )
            ->assertRedirectBackWithErrors(['uploadedFile' => 'The uploaded file field is required.']);

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'created_by'            => $user->id
        ] );
    }

    /**
     * Test store an object with a bad sha256
     *
     * @return void
     */
    public function testStoreWithWrongSha256(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  => self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile,
                'sha256'                => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117'
        ] )
            ->assertRedirectBackWithErrors(['sha256' => 'The selected sha256 is invalid.']);

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117',
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'created_by'            => $user->id
        ] );
    }

    /**
     * Test store an object with a wrong min priv
     *
     * @return void
     */
    public function testStoreWithWrongMinPrivs(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  => self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => 4,
                'uploadedFile'          => $uploadedFile,
        ] )
            ->assertRedirectBackWithErrors(['min_privs' => 'The selected min privs is invalid.']);

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => 4,
            'created_by'            => $user->id
        ] );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testUpdatePublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
            'name'                  => self::testInfo[ 'fileName2' ],
            'description'           => self::testInfo[ 'fileDescription2' ],
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'uploadedFile'          => $uploadedFile
        ] )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  => self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  => self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertForbidden();

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  => self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust admin
     *
     * @return void
     */
    public function testUpdateCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  => self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertForbidden();

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  => self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object with a post method
     *
     * @return void
     */
    public function testUpdateWithPostMethod(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  => self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertMethodNotAllowed();
    }

    /**
     * Test to store an object for a super user
     *
     * @return void
     */
    public function testUpdateSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  => self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertRedirectToRoute( 'docstore-dir@list', [ 'dir' => self::testInfo[ 'parentDirId2' ] ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'created_by'            => $user->id
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $uploadedFile->hashName() );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $file->path );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewNoneViewableFilePublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewNoneViewableFileCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewNoneViewableFileCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertRedirect( route( 'docstore-file@download' , [ 'file' => $file ] ) );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewNoneViewableFileSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertRedirect( route( 'docstore-file@download' , [ 'file' => $file ] ) );
    }

    /**
     * Test to download an object for a public user
     *
     * @return void
     */
    public function testDownloadPublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->get( route( 'docstore-file@download', [ 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test to download an object for a cust user
     *
     * @return void
     */
    public function testDownloadCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@download', [ 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test to download an object for a cust admin
     *
     * @return void
     */
    public function testDownloadCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@download', [ 'file' => $file ] ) )
            ->assertOk();
    }

    /**
     * Test to download an object for a superuser
     *
     * @return void
     */
    public function testDownloadSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@download', [ 'file' => $file ] ) )
            ->assertOk();
    }

    /**
     * Test to get info for an object for a public user
     *
     * @return void
     */
    public function testInfoPublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        // hits ApiAuthenticate middleware
        $this->get( route( 'docstore-file-api@info', [ 'file' => $file ] ) )
            ->assertStatus( 401 );
    }

    /**
     * Test to get info for an object for a cust user
     *
     * @return void
     */
    public function testInfoCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        // policy prevents non-superadmins from accessing file
        $this->actingAs(  $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file-api@info', [ 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test to get info for an object for a custadmin
     *
     * @return void
     */
    public function testInfoCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file-api@info', [ 'file' => $file ] ) )
            ->assertForbidden(); // policy prevents non-superadmins from accessing file
    }

    /**
     * Test to get info for an object for a superuser
     *
     * @return void
     */
    public function testInfoSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->get( route( 'docstore-file-api@info', [ 'file' => $file ] ) )
            ->assertOk()
            ->assertJson( [
                'file_name' => self::testInfo[ 'fileName2' ],
                'created_by' => $user->username . " (" . $user->name . ")",
            ] );
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeletePublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => $file->sha256,
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ]
        ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertForbidden();

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => $file->sha256,
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'created_by'            => 1
        ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertForbidden();

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => $file->sha256,
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'created_by'            => 1
        ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );
        $file = $this->insertDocstoreFileFixture2($origUploadedFile);

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertRedirectToRoute( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  => self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => $file->sha256,
            'description'           => self::testInfo[ 'fileDescription2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'created_by'            => $user->id
        ] );
    }

    /**
     * Store a viewable object
     *
     * @return void
     */
    public function testStoreViewableObject(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  => self::testInfo[ 'fileName3' ],
                'description'           => self::testInfo[ 'fileDescription3' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId3' ],
                'min_privs'             => self::testInfo[ 'filePrivs3' ],
                'uploadedFile'          => $uploadedFile
        ] )
            ->assertRedirectToRoute('docstore-dir@list', [ 'dir' => self::testInfo[ 'parentDirId3' ] ]);

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId3' ],
            'name'                  => self::testInfo[ 'fileName3' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description'           => self::testInfo[ 'fileDescription3' ],
            'min_privs'             => self::testInfo[ 'filePrivs3' ],
            'created_by'            => $user->id
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $uploadedFile->hashName() );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewPublicUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );
        $file = $this->insertDocstoreFileFixture3($uploadedFile);

        $this->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewCustUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );
        $file = $this->insertDocstoreFileFixture3($uploadedFile);

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewCustAdmin()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );
        $file = $this->insertDocstoreFileFixture3($uploadedFile);

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertOk()
            ->assertViewIs( 'docstore.file.view' );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewSuperUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );
        $file = $this->insertDocstoreFileFixture3($uploadedFile);

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertOk()
            ->assertViewIs( 'docstore.file.view' );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDelete2SuperUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );
        $file = $this->insertDocstoreFileFixture3($uploadedFile);

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertRedirectToRoute('docstore-dir@list', [ 'dir' => self::testInfo[ 'parentDirId3' ] ]);

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId3' ],
            'name'                  => self::testInfo[ 'fileName3' ],
            'disk'                  => 'docstore',
            'sha256'                => $file->sha256,
            'description'           => self::testInfo[ 'fileDescription3' ],
            'min_privs'             => self::testInfo[ 'filePrivs3' ],
            'created_by'            => $user->id
        ] );
    }
}