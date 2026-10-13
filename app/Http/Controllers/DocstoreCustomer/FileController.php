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

namespace IXP\Http\Controllers\DocstoreCustomer;


use Former\Facades\Former;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\{
    RedirectResponse,
    Request
};
use Illuminate\Support\Facades\{
    Log,
    Storage
};
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\DocstoreCustomer\StoreDocstoreCustomerFileRequest;
use IXP\Http\Requests\DocstoreCustomer\UpdateDocstoreCustomerFileRequest;
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

use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * View a docstore customer file apply to allowed mimetype ( DocstoreCustomerFile::$extensionViewable )
     *
     * @param  Customer  $cust
     * @param  DocstoreCustomerFile  $file
     *
     * @return View|RedirectResponse
     *
     * @throws AuthorizationException|FileNotFoundException
     */
    public function view( Customer $cust, DocstoreCustomerFile $file ): View|RedirectResponse
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
     * @return StreamedResponse|RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function download( Customer $cust, DocstoreCustomerFile $file ): StreamedResponse|RedirectResponse
    {
        $this->authorize( 'download', $file );

        try {
            return Storage::disk( $file->disk )->download( $file->path, $file->name, [
                'Content-Type' => 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ] );
        } catch( FilesystemException $e ) {
            AlertContainer::push( "This customer file could not be found / downloaded. Please report this error to the support team.", Alert::DANGER );
            return redirect( route( 'docstore-c-dir@list', [ 'cust' => $file->customer->id , 'dir' => $file->docstore_customer_directory_id ] ) );
        }
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
    public function upload( Request $r, Customer $cust ): View
    {
        $this->authorize( 'create', DocstoreCustomerFile::class );

        Former::populate([
            'min_privs' => $r->old( 'min_privs', (string) User::AUTH_SUPERUSER )
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
     * @param  StoreDocstoreCustomerFileRequest  $r
     * @param  Customer  $cust
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store( StoreDocstoreCustomerFileRequest $r, Customer $cust ): RedirectResponse
    {
        // Policy check performed in StoreDocstoreCustomerFileRequest

        $uploadedFile = $r->file('uploadedFile' );

        $path = $uploadedFile->store( (string) $cust->id, 'docstore_customers' );

        /** @psalm-suppress InvalidArgument */
        $file = DocstoreCustomerFile::create( [
            'name'                              => $r->validated('name'),
            'description'                       => $r->validated('description'),
            'cust_id'                           => $cust->id,
            'min_privs'                         => $r->validated('min_privs'),
            'path'                              => $path,
            'sha256'                            => hash_file( 'sha256', $uploadedFile->getRealPath() ),
            'created_by'                        => $r->user()->id,
            'file_last_updated'                 => now(),
            'docstore_customer_directory_id'    => $r->validated('docstore_customer_directory_id'),
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] uploaded by %s for the customer [%d|%s]", $file->id, $file->name, $r->user()->username, $cust->id, $cust->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " File <em>{$file->name}</em> uploaded.", Alert::SUCCESS );
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
            'min_privs'                         => $r->old( 'min_privs',      (string) $file->min_privs    ),
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
     * @param  UpdateDocstoreCustomerFileRequest $r
     * @param  Customer                 $cust
     * @param  DocstoreCustomerFile     $file
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update( UpdateDocstoreCustomerFileRequest $r, Customer $cust, DocstoreCustomerFile $file ): RedirectResponse
    {
        // Policy check performed in UpdateDocstoreCustomerFileRequest

        // if a new file is updated
        if( $r->file('uploadedFile') ) {
            // get path of the old file in order to delete it later
            $oldPath = $file->path;

            $uploadedFile = $r->file('uploadedFile');
            $path = $uploadedFile->store( (string) $file->customer->id, 'docstore_customers' );

            /** @psalm-suppress InvalidArgument */
            $file->update([
                'path'                  => $path,
                'sha256'                => hash_file( 'sha256', $uploadedFile->getRealPath() ),
                'file_last_updated'     => now(),
            ]);

            // Delete the old file
            Storage::disk( $file->disk )->delete( $oldPath );
        }

        $file->update( [
            'name'                              => $r->validated('name'),
            'description'                       => $r->validated('description'),
            'docstore_customer_directory_id'    => $r->validated('docstore_customer_directory_id'),
            'min_privs'                         => $r->validated('min_privs')
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
}