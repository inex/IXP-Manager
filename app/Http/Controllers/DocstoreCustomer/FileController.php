<?php

namespace IXP\Http\Controllers\DocstoreCustomer;

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

use Former\Facades\Former;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\Support\Facades\{
    Log,
    Storage
};

use Illuminate\Validation\Rule;

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Models\{Customer, DocstoreCustomerDirectory, DocstoreCustomerFile, User};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use \League\Flysystem\Exception as FlySystemException;

/**
 * FileController Controller
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   DocstoreCustomer
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class FileController extends Controller
{
    /**
     * View a docstore customer file apply to allowed mimetype ( DocstoreFile::$
     *
     * @param Request               $request
     * @param Customer              $cust
     * @param DocstoreCustomerFile  $file
     *
     * @return mixed
     *
     * @throws
     */
    public function view( Request $request, Customer $cust, DocstoreCustomerFile $file )
    {
        $this->authorize( 'view', $file );

        if( !$file->isViewable() ) {
            return redirect( route( 'docstore-c-file@download', [ 'file' => $file->id ] ) );
        }

        return view( 'docstore-customer/file/view', [
            'file'      => $file,
            'cust'      => $cust,
            'content'   => Storage::disk( $file->disk )->get( $file->path )
        ] );
    }

    /**
     * Download a docstore customer file
     *
     * @param Request               $request
     * @param DocstoreCustomerFile  $file
     *
     * @return mixed
     *
     * @throws
     */
    public function download( Request $request, DocstoreCustomerFile $file )
    {
        $this->authorize( 'download', $file );

        try {
            return Storage::disk( $file->disk )->download( $file->path, $file->name );
        } catch( FlySystemException $e ) {
            AlertContainer::push( "This customer file could not be found / downloaded. Please report this error to the support team.", Alert::DANGER );
            return redirect( route( 'docstore-c-dir@list', [ 'cust' => $file->customer->id , 'dir' => $file->docstore_customer_directory_id ] ) );
        }
    }

    /**
     * Get information on a docstore customer file
     *
     * @param Request               $request
     * @param DocstoreCustomerFile  $file
     *
     * @return mixed
     *
     * @throws
     */
    public function info( Request $request, DocstoreCustomerFile $file )
    {
        $this->authorize( 'info', $file );

        return view( 'docstore/file/info', [
            'file'          => $file,
            'size'          => Storage::disk( $file->disk )->size( $file->path ),
            'last_modified' => Storage::disk( $file->disk )->lastModified( $file->path ),
            'dspath'        => config( 'filesystems.disks.' . $file->disk . '.root', '*** UNKNOWN LOCATION ***' ) . '/' . $file->path,
            'created_by'    => D2EM::getRepository(UserEntity::class)->find( $file->created_by ),
        ]);
    }

    /**
     * Upload a new docstore customer file
     *
     * @param Request   $request
     * @param Customer  $cust
     * @return View
     *
     * @throws
     */
    public function upload( Request $request, Customer $cust )
    {
        $this->authorize( 'create', DocstoreCustomerFile::class );

        Former::populate([
            'min_privs' => $request->old( 'min_privs', User::AUTH_SUPERUSER )
        ]);

        return view( 'docstore-customer/file/upload', [
            'file'          => false,
            'cust'          => $cust,
            'dirs'          => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $cust, null, $request->user() )  ),
        ] );
    }

    /**
     * Store a docstore customer file uploaded
     *
     * @param Request   $request
     * @param Customer  $cust
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function store( Request $request, Customer $cust ): RedirectResponse
    {
        $this->authorize( 'create', DocstoreCustomerFile::class );

        $this->checkForm( $request );
        $file = $request->file('uploadedFile');
        $path = $file->store( $cust->id, 'docstore_customers' );

        $file = DocstoreCustomerFile::create( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'cust_id'               => $cust->id,
            'docstore_directory_id' => $request->docstore_customer_directory_id,
            'min_privs'             => $request->min_privs,
            'path'                  => $path,
            'sha256'                => hash_file( 'sha256', $file ),
            'created_by'            => $request->user()->getId(),
            'file_last_updated'     => now(),
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] uploaded by %s for the customer [%d|%s]", $file->id, $file->name, $request->user()->getUsername(), $cust->id, $cust->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " File <em>{$request->name}</em> uploaded.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust , 'dir' => $file->docstore_directory_id ] ) );
    }

    /**
     * Edit a docstore customer file uploaded
     *
     * @param Request               $request
     * @param DocstoreCustomerFile  $file
     *
     * @return View
     *
     * @throws
     */
    public function edit( Request $request , DocstoreCustomerFile $file ): View
    {
        $this->authorize( 'update', $file );

        Former::populate([
            'name'                  => $request->old( 'name',           $file->name         ),
            'description'           => $request->old( 'descripton',     $file->description  ),
            'sha256'                => $request->old( 'sha256',         $file->sha256       ),
            'min_privs'             => $request->old( 'min_privs',      $file->min_privs    ),
            'docstore_directory_id' => $request->old( 'docstore_customer_directory_id',$file->docstore_directory_id ?? '' ),
        ]);

        return view( 'docstore/file/upload', [
            'file'                      => $file,
            'dirs'                      => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $file->directory->customer, null, $request->user() ) )
        ] );
    }

    /**
     * Update a docstore customer file uploaded
     *
     * @param Request               $request
     * @param DocstoreCustomerFile  $file
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function update( Request $request , DocstoreCustomerFile $file ): RedirectResponse
    {
        $this->authorize( 'update', $file );

        $this->checkForm( $request, $file );

        // if a new file is updated
        if( $request->uploadedFile ) {
            // get path of the old file in order to delete it later
            $oldPath = $file->path;

            $uploadedFile = $request->file('uploadedFile');
            $path = $uploadedFile->store( '', 'docstore_customers' );

            $file->update([
                'path'                  => $path,
                'sha256'                => hash_file( 'sha256', $uploadedFile ),
                'file_last_updated'     => now(),
            ]);

            // Delete the old file
            Storage::disk( $file->disk )->delete( $oldPath );
        }

        $file->update( [
            'name'                              => $request->name,
            'description'                       => $request->description,
            'docstore_customer_directory_id'    => $request->docstore_customer_directory_id,
            'min_privs'                         => $request->min_privs
        ] );

        $cust = $file->directory->customer;

        Log::info( sprintf( "DocStore: customer file [%d|%s] edited by %s for the customer [%d|%s]", $file->id, $file->name, $request->user()->getUsername(), $cust->id, $cust->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " file <em>{$request->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust , 'dir' => $file->docstore_customer_directory_id ] ) );
    }

    /**
     * Delete a file
     *
     * @param Request               $request
     * @param DocstoreCustomerFile  $file
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request , DocstoreCustomerFile $file ): RedirectResponse
    {
        $this->authorize( 'delete', $file );

        $dir    = $file->directory;
        $cust   = $file->directory->customer;

        Storage::disk( $file->disk )->delete( $file->path );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " file <em>{$file->name}</em> deleted.", Alert::SUCCESS );
        Log::info( sprintf( "DocStore: customer file [%d|%s] deleted by %s for the customer [%d|%s]", $file->id, $file->name, $request->user()->getUsername(), $cust->id, $cust->name ) );

        $file->delete();

        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust, 'dir' => $dir ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request               $request
     * @param DocstoreCustomerFile  $file
     *
     */
    private function checkForm( Request $request, ?DocstoreCustomerFile $file = null )
    {
        $request->validate( [
            'name'          => 'required|max:100',
            'uploadedFile'  => Rule::requiredIf( function() use ( $request, $file ) {
                return !$file;
            }),
            'sha256'        => [ 'nullable', 'max:64',
                function ($attribute, $value, $fail ) use( $request ) {
                    if( $value && $request->file('uploadedFile' ) && $value !== hash_file( 'sha256', $request->file( 'uploadedFile' ) ) ) {
                        return $fail( 'The sha256 checksum calculated on the server does not match the one you provided.' );
                    }
                },
            ],
            'min_privs'     => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES ) ),
            'docstore_customer_directory_id' => [ 'nullable', 'integer',
                function ( $value, $fail ) {
                    if( !DocstoreCustomerDirectory::whereId( $value )->exists() ) {
                        return $fail( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' directory does not exist.' );
                    }
                },
            ]
        ] );
    }
}
