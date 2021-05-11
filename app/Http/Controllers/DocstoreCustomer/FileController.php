<?php

namespace IXP\Http\Controllers\DocstoreCustomer;

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

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Log, Storage;

use Former\Facades\Former;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\Validation\Rule;

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Customer,
    DocstoreCustomerDirectory,
    DocstoreCustomerFile,
    User
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use League\Flysystem\Exception as FlySystemException;

/**
 * FileController Controller
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   DocstoreCustomer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class FileController extends Controller
{
    /**
     * View a docstore customer file apply to allowed mimetype ( DocstoreFile::$
     *
     * @param  Customer  $cust
     * @param  DocstoreCustomerFile  $file
     *
     * @return mixed
     *
     * @throws AuthorizationException|FileNotFoundException
     */
    public function view( Customer $cust, DocstoreCustomerFile $file )
    {
        $this->authorize( 'view', $file );

        if( !$file->isViewable() ) {
            return redirect( route( 'docstore-c-file@download', [ 'cust' => $cust, 'file' => $file->id ] ) );
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
     * @param  Customer  $cust
     * @param  DocstoreCustomerFile  $file
     *
     * @return mixed
     *
     * @throws AuthorizationException
     */
    public function download( Customer $cust, DocstoreCustomerFile $file )
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
     * @param  DocstoreCustomerFile  $file
     *
     * @return mixed
     *
     * @throws AuthorizationException
     */
    public function info( DocstoreCustomerFile $file )
    {
        $this->authorize( 'info', $file );

        return view( 'docstore-customer/file/info', [
            'file'          => $file,
            'size'          => Storage::disk( $file->disk )->size( $file->path ),
            'last_modified' => Storage::disk( $file->disk )->lastModified( $file->path ),
            'dspath'        => config( 'filesystems.disks.' . $file->disk . '.root', '*** UNKNOWN LOCATION ***' ) . '/' . $file->path,
            'created_by'    => User::find( $file->created_by ),
            'created_at'    => $file->created_at,
        ]);
    }

    /**
     * Upload a new docstore customer file
     *
     * @param  Request  $r
     * @param  Customer  $cust
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function upload( Request $r, Customer $cust ): view
    {
        $this->authorize( 'create', DocstoreCustomerFile::class );

        Former::populate([
            'min_privs' => $r->old( 'min_privs', User::AUTH_SUPERUSER )
        ]);

        return view( 'docstore-customer/file/upload', [
            'file'          => false,
            'cust'          => $cust,
            'dirs'          => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $cust, $r->user() )  ),
        ] );
    }

    /**
     * Store a docstore customer file uploaded
     *
     * @param  Request  $r
     * @param  Customer  $cust
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store( Request $r, Customer $cust ): RedirectResponse
    {
        $this->authorize( 'create', DocstoreCustomerFile::class );

        $this->checkForm( $r );

        $uploadedFile = $r->file('uploadedFile' );

        $path = $uploadedFile->store( $cust->id, 'docstore_customers' );

        $file = DocstoreCustomerFile::create( [
            'name'                              => $r->name,
            'description'                       => $r->description,
            'cust_id'                           => $cust->id,
            'min_privs'                         => $r->min_privs,
            'path'                              => $path,
            'sha256'                            => hash_file( 'sha256', $uploadedFile ),
            'created_by'                        => $r->user()->id,
            'file_last_updated'                 => now(),
            'docstore_customer_directory_id'    => $r->docstore_customer_directory_id,
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] uploaded by %s for the customer [%d|%s]", $file->id, $file->name, $r->user()->username, $cust->id, $cust->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " File <em>{$r->name}</em> uploaded.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust , 'dir' => $file->docstore_customer_directory_id ] ) );
    }

    /**
     * Edit a docstore customer file uploaded
     *
     * @param  Request  $r
     * @param  Customer  $cust
     * @param  DocstoreCustomerFile  $file
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function edit( Request $r, Customer $cust, DocstoreCustomerFile $file ): View
    {
        $this->authorize( 'update', $file );

        Former::populate([
            'name'                              => $r->old( 'name',           $file->name         ),
            'description'                       => $r->old( 'descripton',     $file->description  ),
            'sha256'                            => $r->old( 'sha256',         $file->sha256       ),
            'min_privs'                         => $r->old( 'min_privs',      $file->min_privs    ),
            'docstore_customer_directory_id'    => $r->old( 'docstore_customer_directory_id',$file->docstore_customer_directory_id ?? '' ),
        ]);

        return view( 'docstore-customer/file/upload', [
            'file'                      => $file,
            'cust'                      => $cust,
            'dirs'                      => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $file->customer, $r->user() ) )
        ] );
    }

    /**
     * Update a docstore customer file uploaded
     *
     * @param  Request                  $r
     * @param  Customer                 $cust
     * @param  DocstoreCustomerFile     $file
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update( Request $r, Customer $cust, DocstoreCustomerFile $file ): RedirectResponse
    {
        $this->authorize( 'update', $file );

        $this->checkForm( $r, $file );

        // if a new file is updated
        if( $r->uploadedFile ) {
            // get path of the old file in order to delete it later
            $oldPath = $file->path;

            $uploadedFile = $r->file('uploadedFile');
            $path = $uploadedFile->store( $file->customer->id, 'docstore_customers' );

            $file->update([
                'path'                  => $path,
                'sha256'                => hash_file( 'sha256', $uploadedFile ),
                'file_last_updated'     => now(),
            ]);

            // Delete the old file
            Storage::disk( $file->disk )->delete( $oldPath );
        }

        $file->update( [
            'name'                              => $r->name,
            'description'                       => $r->description,
            'docstore_customer_directory_id'    => $r->docstore_customer_directory_id,
            'min_privs'                         => $r->min_privs
        ] );


        Log::info( sprintf( "DocStore: customer file [%d|%s] edited by %s for the customer [%d|%s]", $file->id, $file->name, $r->user()->username, $cust->id, $cust->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " file <em>{$r->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust , 'dir' => $file->docstore_customer_directory_id ] ) );
    }

    /**
     * Delete a file
     *
     * @param  Request                  $r
     * @param  DocstoreCustomerFile     $file
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function delete( Request $r , DocstoreCustomerFile $file ): RedirectResponse
    {
        $this->authorize( 'delete', $file );

        $dir    = $file->directory;
        $cust   = $file->customer;

        Storage::disk( $file->disk )->delete( $file->path );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " file <em>{$file->name}</em> deleted.", Alert::SUCCESS );
        Log::info( sprintf( "DocStore: customer file [%d|%s] deleted by %s for the customer [%d|%s]", $file->id, $file->name, $r->user()->username, $cust->id, $cust->name ) );

        $file->delete();

        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust, 'dir' => $dir ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request                       $r
     * @param DocstoreCustomerFile|null     $file
     *
     * @return void
     */
    private function checkForm( Request $r, ?DocstoreCustomerFile $file = null ): void
    {
        $r->validate( [
            'name'                              => 'required|max:100',
            'uploadedFile'                      => Rule::requiredIf( function() use ( $r, $file ) {
                return !$file;
            }),
            'sha256'                            => [ 'nullable', 'max:64',
                function ( $attribute, $value, $fail ) use( $r ) {
                    if( $value && $r->file('uploadedFile' ) && $value !== hash_file( 'sha256', $r->file( 'uploadedFile' ) ) ) {
                        return $fail( 'The sha256 checksum calculated on the server does not match the one you provided.' );
                    }
                },
            ],
            'min_privs'                         => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES ) ),
            'docstore_customer_directory_id'    => 'nullable|integer|exists:docstore_customer_directories,id',
        ] );
    }
}