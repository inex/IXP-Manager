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

use IXP\Models\User;
use Storage;

use Illuminate\Foundation\Testing\WithoutMiddleware;

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

    /**
     * Test the access to the upload form for a public user
     *
     * @return void
     */
    public function testUploadFormAccessPublicUser(): void
    {
        // public user
        $response = $this->get( route( 'docstore-file@upload' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the upload form for a cust user
     *
     * @return void
     */
    public function testUploadFormAccessCustUser(): void
    {
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@upload' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the upload form for a cust admin
     *
     * @return void
     */
    public function testUploadFormAccessCustAdmin(): void
    {
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@upload' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the upload form for a super user
     *
     * @return void
     */
    public function testUploadFormAccessSuperUser(): void
    {
        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@upload' ) );
        $response->assertStatus(200 );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStorePublicUser(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $response = $this->post( route( 'docstore-file@store' ), [
            'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
        ] );

        $response->assertStatus(403 );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
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

        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  =>  self::testInfo[ 'fileName' ],
                'description'           => self::testInfo[ 'fileDescription' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs'             => self::testInfo[ 'filePrivs' ],
                'uploadedFile'          => $uploadedFile
            ] );

        $response->assertStatus(403 );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id'     => self::testInfo[ 'parentDirId' ],
            'name'                      =>  self::testInfo[ 'fileName' ],
            'disk'                      => 'docstore',
            'sha256'                    => hash_file( 'sha256', $uploadedFile ),
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

        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->post( route( 'docstore-file@store' ), [
                'name'                  =>  self::testInfo[ 'fileName' ],
                'description'           => self::testInfo[ 'fileDescription' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs'             => self::testInfo[ 'filePrivs' ],
                'uploadedFile'          => $uploadedFile
        ] );

        $response->assertStatus(403 );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  =>  self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
                'name'                  =>  self::testInfo[ 'fileName' ],
                'description'           => self::testInfo[ 'fileDescription' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs'             => self::testInfo[ 'filePrivs' ],
                'uploadedFile'          => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  =>  self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
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
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile,
                'sha256'                => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117'
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
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
    public function testStoreWithWrongMinPivs(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@store' ), [
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => 4,
                'uploadedFile'          => $uploadedFile,
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
            'name'                  =>  self::testInfo[ 'fileName2' ],
            'description'           => self::testInfo[ 'fileDescription2' ],
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs'             => self::testInfo[ 'filePrivs2' ],
            'uploadedFile'          => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  =>  self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  =>  self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  =>  self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  =>  self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'name'                  =>  self::testInfo[ 'fileName' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
            'description'           => self::testInfo[ 'fileDescription' ],
            'min_privs'             => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  =>  self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] );

        $response->assertStatus(405 );
    }

    /**
     * Test to store an object for a super user
     *
     * @return void
     */
    public function testUpdateSuperUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
                'name'                  =>  self::testInfo[ 'fileName2' ],
                'description'           => self::testInfo[ 'fileDescription2' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
                'min_privs'             => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'          => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(403 );
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewNoneViewableFileCustUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(403 );
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewNoneViewableFileCustAdmin(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->get( route( 'docstore-file@download', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to download an object for a cust user
     *
     * @return void
     */
    public function testDownloadCustUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@download', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to download an object for a cust admin
     *
     * @return void
     */
    public function testDownloadCustAdmin(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@download', [ 'file' => $file ] ) );
        $response->assertStatus( 200 );
    }

    /**
     * Test to download an object for a superuser
     *
     * @return void
     */
    public function testDownloadSuperUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@download', [ 'file' => $file ] ) );
        $response->assertStatus( 200 );
    }

    /**
     * Test to get info for an object for a public user
     *
     * @return void
     */
    public function testInfoPublicUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->get( route( 'docstore-file@info', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to get info for an object for a cust user
     *
     * @return void
     */
    public function testInfoCustUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->actingAs(  $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@info', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to get info for an object for a custadmin
     *
     * @return void
     */
    public function testInfoCustAdmin(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@info', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to get info for an object for a superuser
     *
     * @return void
     */
    public function testInfoSuperUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $response = $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@info', [ 'file' => $file ] ) );
        $response->assertStatus( 200 )
            ->assertViewIs('docstore.file.info' );
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeletePublicUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertStatus(403 );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) );
        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'name'                  =>  self::testInfo[ 'fileName2' ],
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
                'name'                  =>  self::testInfo[ 'fileName3' ],
                'description'           => self::testInfo[ 'fileDescription3' ],
                'docstore_directory_id' => self::testInfo[ 'parentDirId3' ],
                'min_privs'             => self::testInfo[ 'filePrivs3' ],
                'uploadedFile'          => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId3' ],
            'name'                  =>  self::testInfo[ 'fileName3' ],
            'disk'                  => 'docstore',
            'sha256'                => hash_file( 'sha256', $uploadedFile ),
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
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $this->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(403 );
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewCustUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(403 );
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewCustAdmin()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(200 )
            ->assertViewIs( 'docstore.file.view' );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewSuperUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(200 )
            ->assertViewIs( 'docstore.file.view' );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDelete2SuperUser(): void
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $this->actingAs( $user = $this->getSuperUser( 'travis' ) )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) );
        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId3' ],
            'name'                  =>  self::testInfo[ 'fileName3' ],
            'disk'                  => 'docstore',
            'sha256'                => $file->sha256,
            'description'           => self::testInfo[ 'fileDescription3' ],
            'min_privs'             => self::testInfo[ 'filePrivs3' ],
            'created_by'            => $user->id
        ] );
    }
}