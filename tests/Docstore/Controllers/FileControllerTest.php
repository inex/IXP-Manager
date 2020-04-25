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

use D2EM, Storage;

use Entities\User as UserEntity;

use Illuminate\Foundation\Testing\WithoutMiddleware;

use Illuminate\Http\UploadedFile;

use IXP\Models\DocstoreFile;

use Tests\TestCase;

class FileControllerTest extends TestCase
{

    const testInfo = [
        'custuser'              => 'hecustuser',
        'custadmin'             => 'hecustadmin',
        'superuser'             => 'travis',
        'disk'                  => 'docstore',
        'fileName'              => 'File.pdf',
        'fileDescription'       => 'This is file.pdf',
        'filePrivs'             => UserEntity::AUTH_SUPERUSER,
        'parentDirId'           => null,
        'fileName2'             => 'File2.pdf',
        'fileDescription2'      => 'This is file2.pdf',
        'filePrivs2'            => UserEntity::AUTH_CUSTADMIN,
        'parentDirId2'          => 1,
        'fileName3'             => 'File3.txt',
        'fileDescription3'      => 'This is file3.txt',
        'textFile'              => 'I am the file3.txt',
        'filePrivs3'            => UserEntity::AUTH_CUSTADMIN,
        'parentDirId3'          => 1,
    ];

    /**
     * Test the access to the upload form for a public user
     *
     * @return void
     */
    public function testUploadFormAccessPublicUser()
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
    public function testUploadFormAccessCustUser()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-file@upload' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the upload form for a cust admin
     *
     * @return void
     */
    public function testUploadFormAccessCustAdmin()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-file@upload' ) );
        $response->assertStatus(403 );
    }

    /**
     * Test the access to the upload form for a super user
     *
     * @return void
     */
    public function testUploadFormAccessSuperUser()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );
        $response = $this->actingAs( $user )->get( route( 'docstore-file@upload' ) );
        $response->assertStatus(200 );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testStorePublicUser()
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
    public function testStoreCustUser()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $response = $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
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
    public function testStoreCustAdmin()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $response = $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
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
     * Test to store an object for a super user
     *
     * @return void
     */
    public function testStoreSuperUser()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
            'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId' ],
            'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ], 'created_by' => $user->getId()
        ] );

        Storage::disk(self::testInfo[ 'disk' ] )->assertExists( $uploadedFile->hashName() );
    }

    /**
     * Test store an object with no name
     *
     * @return void
     */
    public function testStoreWithoutName()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test store an object with no file
     *
     * @return void
     */
    public function testStoreWithoutFile()
    {
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore',
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test store an object with a bad sha256
     *
     * @return void
     */
    public function testStoreWithWrongSha256()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ], 'uploadedFile'  => $uploadedFile, 'sha256' => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117'
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117',
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test store an object with a wrong min priv
     *
     * @return void
     */
    public function testStoreWithWrongMinPivs()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
            'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => 4, 'uploadedFile'  => $uploadedFile,
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => 4, 'created_by' => $user->getId()
        ] );
    }

    /**
     * Test to store an object for a public user
     *
     * @return void
     */
    public function testUpdatePublicUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust admin
     *
     * @return void
     */
    public function testUpdateCustAdmin()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $this->actingAs( $user )->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $uploadedFile->hashName() );
    }

    /**
     * Test to store an object with a post method
     *
     * @return void
     */
    public function testUpdateWithPostMethod()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $response = $this->actingAs( $user )->post( route( 'docstore-file@update', [ 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $response->assertStatus(405 );
    }

    /**
     * Test to store an object for a super user
     *
     * @return void
     */
    public function testUpdateSuperUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName' ] ] )->first();

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->put( route( 'docstore-file@update', [ 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId2' ],
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $uploadedFile->hashName() );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $file->path );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewNoneViewableFilePublicUser()
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
    public function testViewNoneViewableFileCustUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(403 );
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewNoneViewableFileCustAdmin()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertRedirect( route( 'docstore-file@download' , [ 'file' => $file ] ) );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewNoneViewableFileSuperUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertRedirect( route( 'docstore-file@download' , [ 'file' => $file ] ) );
    }

    /**
     * Test to download an object for a public user
     *
     * @return void
     */
    public function testDownloadPublicUser()
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
    public function testDownloadCustUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-file@download', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to download an object for a cust admin
     *
     * @return void
     */
    public function testDownloadCustAdmin()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-file@download', [ 'file' => $file ] ) );
        $response->assertStatus( 200 );
    }

    /**
     * Test to download an object for a superuser
     *
     * @return void
     */
    public function testDownloadSuperUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-file@download', [ 'file' => $file ] ) );
        $response->assertStatus( 200 );
    }

    /**
     * Test to get info for an object for a public user
     *
     * @return void
     */
    public function testInfoPublicUser()
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
    public function testInfoCustUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-file@info', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to get info for an object for a custadmin
     *
     * @return void
     */
    public function testInfoCustAdmin()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-file@info', [ 'file' => $file ] ) );
        $response->assertStatus( 403 );
    }

    /**
     * Test to get info for an object for a superuser
     *
     * @return void
     */
    public function testInfoSuperUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $response = $this->actingAs( $user )->get( route( 'docstore-file@info', [ 'file' => $file ] ) );
        $response->assertStatus( 200 )
            ->assertViewIs('docstore.file.info' );
    }

    /**
     * Test delete an object for a public user
     *
     * @return void
     */
    public function testDeletePublicUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $this->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertStatus(403 );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => 1
        ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $this->actingAs( $user )->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) )
            ->assertStatus(403 );
        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => 1
        ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName2' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) );
        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId2' ], 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => 'docstore', 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->getId()
        ] );
    }

    /**
     * Store a viewable object
     *
     * @return void
     */
    public function testStoreViewableObject()
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], self::testInfo[ 'textFile' ] );

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->post( route( 'docstore-file@store' ), [
            'name' =>  self::testInfo[ 'fileName3' ], 'description' => self::testInfo[ 'fileDescription3' ], 'docstore_directory_id' => self::testInfo[ 'parentDirId3' ],
            'min_privs' => self::testInfo[ 'filePrivs3' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId3' ], 'name' =>  self::testInfo[ 'fileName3' ], 'disk' => 'docstore', 'sha256' => hash_file( 'sha256', $uploadedFile ),
            'description' => self::testInfo[ 'fileDescription3' ], 'min_privs' => self::testInfo[ 'filePrivs3' ], 'created_by' => $user->getId()
        ] );

        Storage::disk(self::testInfo[ 'disk' ] )->assertExists( $uploadedFile->hashName() );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewPublicUser()
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
    public function testViewCustUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
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

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'custadmin' ] ] );
        $this->actingAs( $user )->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(200 )
            ->assertViewIs( 'docstore.file.view' );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewSuperUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )->get( route( 'docstore-file@view', [ 'file' => $file ] ) )
            ->assertStatus(200 )
            ->assertViewIs( 'docstore.file.view' );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDelete2SuperUser()
    {
        $file = DocstoreFile::where( [ 'name' => self::testInfo[ 'fileName3' ] ] )->first();

        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [  'username' => self::testInfo[ 'superuser' ] ] );

        $this->actingAs( $user )
            ->delete( route( 'docstore-file@delete', [ 'file' => $file ] ) );
        $this->assertDatabaseMissing( 'docstore_files', [
            'docstore_directory_id' => self::testInfo[ 'parentDirId3' ], 'name' =>  self::testInfo[ 'fileName3' ], 'disk' => 'docstore', 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription3' ], 'min_privs' => self::testInfo[ 'filePrivs3' ], 'created_by' => $user->getId()
        ] );
    }
}