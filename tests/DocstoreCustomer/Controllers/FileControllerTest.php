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


use Storage;



use Illuminate\Http\UploadedFile;

use IXP\Models\{DocstoreCustomerDirectory, DocstoreCustomerFile, DocstoreFile, User};

use Tests\TestCase;

/**
 * Test docstore customer File Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\DocstoreCustomer\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FileControllerTest extends TestCase
{

    public const testInfo = [
        'folderName'            => 'Folder 3',
        'folderDescription'     => 'This is the folder 3',
        'disk'                  => 'docstore_customers',
        'customerId'            => 5,
        'fileName'              => 'File2.pdf',
        'fileDescription'       => 'This is file2.pdf',
        'filePrivs'             => User::AUTH_SUPERUSER,
        'parentDirId'           => null,
        'fileName2'             => 'File3.pdf',
        'fileDescription2'      => 'This is file3.pdf',
        'filePrivs2'            => User::AUTH_CUSTADMIN,
        'fileName3'             => 'File4.txt',
        'fileDescription3'      => 'This is file4.txt',
        'textFile'              => 'I am the file4.txt',
        'filePrivs3'            => User::AUTH_CUSTADMIN,
    ];

    private function makeDocstoreCustomerDirectory(int $customerId): DocstoreCustomerDirectory
    {
        $dir = new DocstoreCustomerDirectory();
        $dir->name = "Test directory";
        $dir->description = "some directory needed for tests";
        $dir->parent_dir_id = null;
        $dir->cust_id = $customerId;
        $dir->save();
        return $dir;
    }

    private function insertDocstoreCustomerFileFixture1(UploadedFile $uploadedFile): DocstoreCustomerFile
    {
        $file = new DocstoreCustomerFile();
        $file->name = self::testInfo[ 'fileName' ];
        $file->disk = self::testInfo[ 'disk' ];
        $file->cust_id = self::testInfo[ 'customerId' ];
        $file->docstore_customer_directory_id = self::testInfo[ 'parentDirId' ];
        $file->description = self::testInfo[ 'fileDescription' ];
        $file->path = $uploadedFile->store( (string) $file->cust_id, 'docstore_customers' );
        $file->sha256 = hash_file('sha256', $uploadedFile->getPathname());
        $file->min_privs = self::testInfo[ 'filePrivs' ];
        $file->created_by = $this->getSuperUser( 'travis' )->id;
        $file->file_last_updated = now();
        $file->save();
        return $file;
    }

    private function insertDocstoreCustomerFileFixture2(UploadedFile $uploadedFile, DocstoreCustomerDirectory $dir): DocstoreCustomerFile
    {
        $file = new DocstoreCustomerFile();
        $file->name = self::testInfo[ 'fileName2' ];
        $file->disk = self::testInfo[ 'disk' ];
        $file->cust_id = self::testInfo[ 'customerId' ];
        $file->docstore_customer_directory_id = $dir->id;
        $file->description = self::testInfo[ 'fileDescription2' ];
        $file->path = $uploadedFile->store( (string) $file->cust_id, 'docstore_customers' );
        $file->sha256 = hash_file('sha256', $uploadedFile->getPathname());
        $file->min_privs = self::testInfo[ 'filePrivs2' ];
        $file->created_by = $this->getSuperUser( 'travis' )->id;
        $file->file_last_updated = now();
        $file->save();
        return $file;
    }

    private function insertDocstoreCustomerFileFixture3(UploadedFile $uploadedFile, DocstoreCustomerDirectory $dir): DocstoreCustomerFile
    {
        $file = new DocstoreCustomerFile();
        $file->name = self::testInfo[ 'fileName3' ];
        $file->disk = self::testInfo[ 'disk' ];
        $file->cust_id = self::testInfo[ 'customerId' ];
        $file->docstore_customer_directory_id = $dir->id;
        $file->description = self::testInfo[ 'fileDescription3' ];
        $file->path = $uploadedFile->store( (string) $file->cust_id, 'docstore_customers' );
        $file->sha256 = hash_file('sha256', $uploadedFile->getPathname());
        $file->min_privs = self::testInfo[ 'filePrivs3' ];
        $file->created_by = $this->getSuperUser( 'travis' )->id;
        $file->file_last_updated = now();
        $file->save();
        return $file;
    }

    /**
     * Test store an object for a superuser
     *
     * @return void
     */
    public function testStoreSuperUser2(): void
    {
        // test Superuser
        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )->post( route( 'docstore-c-dir@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [  'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir' => self::testInfo[ 'parentDirId' ] ] );
        $this->assertDatabaseHas( 'docstore_customer_directories', [ 'cust_id' => self::testInfo[ 'customerId' ], 'name' =>  self::testInfo[ 'folderName' ], 'description' => self::testInfo[ 'folderDescription' ], 'parent_dir_id' => self::testInfo[ 'parentDirId' ] ] );
    }

    /**
     * Test the access to the upload form for a public user
     *
     * @return void
     */
    public function testUploadFormAccessPublicUser(): void
    {
        // public user
        $this->get( route( 'docstore-c-file@upload' , [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
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
            ->get( route( 'docstore-c-file@upload', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
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
            ->get( route( 'docstore-c-file@upload', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
            ->assertForbidden();
    }

    /**
     * Test the access to the upload form for a super user
     *
     * @return void
     */
    public function testUploadFormAccessSuperUser(): void
    {
        $this->actingAs(  $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-c-file@upload', [ 'cust' => self::testInfo[ 'customerId' ] ] ) )
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

        $this->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ]  ), [
                'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
            ] )
            ->assertRedirectToRoute('login@showForm' );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
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

        // test custuser
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->post( route( 'docstore-c-file@store' , [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
                'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
            ] )
            ->assertForbidden();

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
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

        // test custadmin
        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ]  ), [
                'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
            ] )
            ->assertForbidden();

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ] , 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
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

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->post( route( 'docstore-c-file@store' , [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
                'name' =>  self::testInfo[ 'fileName' ], 'description' => self::testInfo[ 'fileDescription' ], 'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ],
                'min_privs' => self::testInfo[ 'filePrivs' ],'uploadedFile'  => $uploadedFile
            ] )
            ->assertRedirectToRoute('docstore-c-dir@list', [ 'cust' => self::testInfo[ 'customerId' ] , 'dir' => self::testInfo[ 'parentDirId' ]]);

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ], 'created_by' => $user->id
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test store an object with no name
     *
     * @return void
     */
    public function testStoreWithoutName(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName2' ], '2000' );

        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )
            ->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
                'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => null,
                'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
            ] )
            ->assertRedirectBackWithErrors(['name' => 'The name field is required.']);

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => null, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->id
        ] );
    }

    /**
     * Test store an object with no file
     *
     * @return void
     */
    public function testStoreWithoutFile(): void
    {
        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
                'name' => self::testInfo[ 'fileName2' ],
                'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => null,
                'min_privs' => self::testInfo[ 'filePrivs2' ]
            ] )
            ->assertRedirectBackWithErrors(['uploadedFile' => 'The uploaded file field is required.']);

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => null, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ],
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->id
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

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
                'name' => self::testInfo[ 'fileName2' ],
                'description' => self::testInfo[ 'fileDescription2' ],
                'docstore_customer_directory_id' => null,
                'min_privs' => self::testInfo[ 'filePrivs2' ],
                'uploadedFile'  => $uploadedFile,
                'sha256' => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117'
            ] )
            ->assertRedirectBackWithErrors(['sha256' => 'The sha256 checksum calculated on the server does not match the one you provided.']);

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => null, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => '93fc19ea1eb40b8ef8984a7c53dd7b94cb690d5ae5f8b3497c206b43e0bfe117',
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->id
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

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->post( route( 'docstore-c-file@store', [ 'cust' => self::testInfo[ 'customerId' ] ] ), [
                'name' => self::testInfo[ 'fileName2' ],
                'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => null,
                'min_privs' => 4, 'uploadedFile'  => $uploadedFile,
            ] )
            ->assertRedirectBackWithErrors(['min_privs' => 'The selected min privs is invalid.']);

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => null, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => 4, 'created_by' => $user->id
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
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
            'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => null,
            'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
        ] )
            ->assertRedirectToRoute('login@showForm');

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => null, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust user
     *
     * @return void
     */
    public function testUpdateCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
                'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => null,
                'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
            ] )
            ->assertNotFound();

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => null, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' => self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test to store an object for a cust admin
     *
     * @return void
     */
    public function testUpdateCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = $this->getCustAdminUser( 'hecustadmin' );

        $this->actingAs( $user )
            ->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
                'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => null,
                'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
            ] )
            ->assertNotFound();

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => self::testInfo[ 'parentDirId' ], 'name' =>  self::testInfo[ 'fileName' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription' ], 'min_privs' => self::testInfo[ 'filePrivs' ]
        ] );

        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => null, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( $file->path );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
    }

    /**
     * Test to store an object with a post method
     *
     * @return void
     */
    public function testUpdateWithPostMethodInsteadOfPut(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->post( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
                'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => null,
                'min_privs' => self::testInfo[ 'filePrivs2' ],'uploadedFile'  => $uploadedFile
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
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );

        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->put( route( 'docstore-c-file@update', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ), [
                'name' =>  self::testInfo[ 'fileName2' ], 'description' => self::testInfo[ 'fileDescription2' ], 'docstore_customer_directory_id' => $testDir->id,
                'min_privs' => self::testInfo[ 'filePrivs2' ], 'uploadedFile'  => $uploadedFile
            ] )
            ->assertRedirectToRoute('docstore-c-dir@list', [ 'cust' => self::testInfo[ 'customerId' ] , 'dir' => $testDir->id ]);

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => $testDir->id, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->id
        ] );

        Storage::disk( self::testInfo[ 'disk' ] )->assertExists( self::testInfo[ 'customerId' ] . '/' . $uploadedFile->hashName() );
        Storage::disk( self::testInfo[ 'disk' ] )->assertMissing( $file->path );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewNoneViewableFilePublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $this->get( route( 'docstore-c-file@view', [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertRedirectToRoute('login@showForm' );
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewNoneViewableFileCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $user = $this->getCustUser( 'hecustuser' );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertNotFound();
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewNoneViewableFileCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $user = $this->getCustAdminUser( 'hecustadmin' );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewNoneViewableFileSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )->get( route( 'docstore-c-file@view', [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertRedirect( route( 'docstore-c-file@download' , [  'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) );
    }

    /**
     * Test to download an object for a public user
     *
     * @return void
     */
    public function testDownloadPublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $file = $this->insertDocstoreCustomerFileFixture1($origUploadedFile);

        $this->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertRedirectToRoute('login@showForm' );
    }

    /**
     * Test to download an object for a cust user
     *
     * @return void
     */
    public function testDownloadCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertNotFound();
    }

    /**
     * Test to download an object for a cust admin
     *
     * @return void
     */
    public function testDownloadCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $this->actingAs( $this->getCustAdminUser( 'imcustadmin' ) )
            ->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertOk();
    }

    /**
     * Test to download an object for a superuser
     *
     * @return void
     */
    public function testDownloadSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-c-file@download', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertOk();
    }

    /**
     * Test to get info for an object for a public user
     *
     * @return void
     */
    public function testInfoPublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        // model binding happens before our authentication middleware and fails
        // because of the global scope on DocstureCustomerFile that triggers ModelNotFound
        $this->get( route( 'docstore-c-file-api@info', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertNotFound();
    }

    /**
     * Test to get info for an object for a cust user
     *
     * @return void
     */
    public function testInfoCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        // model binding happens before our authentication middleware and fails
        // because of the global scope on DocstureCustomerFile that triggers ModelNotFound
        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-c-file-api@info', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertNotFound();
    }

    /**
     * Test to get info for an object for a custadmin
     *
     * @return void
     */
    public function testInfoCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->get( route( 'docstore-c-file-api@info', [ 'cust' => self::testInfo[ 'customerId' ], 'file' => $file ] ) )
            ->assertForbidden();
    }

    /**
     * Test to get info for an object for a superuser
     *
     * @return void
     */
    public function testInfoSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $user = $this->getSuperUser( 'travis' );
        $this->actingAs( $user )
            ->get( route( 'docstore-c-file-api@info', [ 'cust' => self::testInfo[ 'customerId' ] ,'file' => $file ] ) )
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
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $this->delete( route( 'docstore-c-file@delete', [  'cust' => self::testInfo[ 'customerId' ] ,'file' => $file ] ) )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => $testDir->id, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ]
        ] );
    }

    /**
     * Test delete an object for a cust user
     *
     * @return void
     */
    public function testDeleteCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->delete( route( 'docstore-c-file@delete', [  'cust' => self::testInfo[ 'customerId' ] ,'file' => $file ] ) )
            ->assertNotFound();

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => $testDir->id, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => 1
        ] );
    }

    /**
     * Test delete an object for a cust admin
     *
     * @return void
     */
    public function testDeleteCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )
            ->delete( route( 'docstore-c-file@delete', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertForbidden();
        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => $testDir->id, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => 1
        ] );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDeleteSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture2($origUploadedFile, $testDir);

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->delete( route( 'docstore-c-file@delete', [  'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertRedirectToRoute('docstore-c-dir@list', [ 'cust' => self::testInfo['customerId'], 'dir' => $testDir->id ]);
        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => $testDir->id, 'name' =>  self::testInfo[ 'fileName2' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription2' ], 'min_privs' => self::testInfo[ 'filePrivs2' ], 'created_by' => $user->id
        ] );
    }

    /**
     * Store a viewable object
     *
     * @return void
     */
    public function testStoreViewableObject(): void
    {
        $uploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName3' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )->post( route( 'docstore-c-file@store', [  'cust' => self::testInfo[ 'customerId' ] ] ), [
            'name' =>  self::testInfo[ 'fileName3' ], 'description' => self::testInfo[ 'fileDescription3' ], 'docstore_customer_directory_id' => $testDir->id,
            'min_privs' => self::testInfo[ 'filePrivs3' ],'uploadedFile'  => $uploadedFile
        ] );

        $this->assertDatabaseHas( 'docstore_customer_files', [
            'docstore_customer_directory_id' => $testDir->id, 'name' =>  self::testInfo[ 'fileName3' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => hash_file( 'sha256', $uploadedFile->getPathname() ),
            'description' => self::testInfo[ 'fileDescription3' ], 'min_privs' => self::testInfo[ 'filePrivs3' ], 'created_by' => $user->id
        ] );

        Storage::disk(self::testInfo[ 'disk' ] )->assertExists(  self::testInfo[ 'customerId' ] . '/' .  $uploadedFile->hashName() );
    }

    /**
     * Test view a none viewable object for a public user
     *
     * @return void
     */
    public function testViewPublicUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture3($origUploadedFile, $testDir);

        $this->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertRedirectToRoute('login@showForm');
    }

    /**
     * Test view a none viewable object for a cust user
     *
     * @return void
     */
    public function testViewCustUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture3($origUploadedFile, $testDir);

        $this->actingAs( $this->getCustUser( 'hecustuser' ) )
            ->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertNotFound();
    }

    /**
     * Test view a none viewable object for a cust admin
     *
     * @return void
     */
    public function testViewCustAdmin(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture3($origUploadedFile, $testDir);

        $this->actingAs( $this->getCustAdminUser( 'imcustadmin' ) )
            ->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertOk()
            ->assertViewIs( 'docstore-customer.file.view' );
    }

    /**
     * Test view a none viewable object for a super user
     *
     * @return void
     */
    public function testViewSuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture3($origUploadedFile, $testDir);

        $this->actingAs( $this->getSuperUser( 'travis' ) )
            ->get( route( 'docstore-c-file@view', [ 'cust' => self::testInfo[ 'customerId' ] , 'file' => $file ] ) )
            ->assertOk()
            ->assertViewIs( 'docstore-customer.file.view' );
    }

    /**
     * Test delete an object for a superuser
     *
     * @return void
     */
    public function testDelete2SuperUser(): void
    {
        $origUploadedFile = UploadedFile::fake()->create( self::testInfo[ 'fileName' ], '2000' );
        $testDir = $this->makeDocstoreCustomerDirectory( self::testInfo['customerId'] );
        $file = $this->insertDocstoreCustomerFileFixture3($origUploadedFile, $testDir);

        $user = $this->getSuperUser( 'travis' );

        $this->actingAs( $user )
            ->delete( route( 'docstore-c-file@delete', [ 'file' => $file ] ) )
            ->assertRedirectToRoute('docstore-c-dir@list', [ 'cust' => self::testInfo['customerId'], 'dir' => $testDir->id ]);
        $this->assertDatabaseMissing( 'docstore_customer_files', [
            'docstore_customer_directory_id' => $testDir->id, 'name' =>  self::testInfo[ 'fileName3' ], 'disk' =>  self::testInfo[ 'disk' ], 'sha256' => $file->sha256,
            'description' => self::testInfo[ 'fileDescription3' ], 'min_privs' => self::testInfo[ 'filePrivs3' ], 'created_by' => $user->id
        ] );
    }
}