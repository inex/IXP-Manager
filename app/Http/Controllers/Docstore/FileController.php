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

namespace IXP\Http\Controllers\Docstore;

use Former\Facades\Former;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\{
    RedirectResponse,
    Request
};
use Illuminate\Support\Facades\{
    Auth,
    Log,
    Storage
};
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\Docstore\StoreDocstoreFileRequest;
use IXP\Http\Requests\Docstore\UpdateDocstoreFileRequest;
use IXP\Models\{
    DocstoreDirectory,
    DocstoreFile,
    DocstoreLog,
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
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Docstore
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FileController extends Controller
{
    /**
     * Upload a new docstore file
     *
     * @param  Request  $r
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function upload( Request $r ): View
    {
        $this->authorize( 'create', DocstoreFile::class );

        Former::populate([
            'min_privs' => $r->old( 'min_privs', (string) User::AUTH_SUPERUSER )
        ]);

        return view( 'docstore/file/upload', [
            'file'          => false,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $r->user() )  ),
        ] );
    }

    /**
     * Store a docstore file uploaded
     *
     * @param StoreDocstoreFileRequest $r
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store( StoreDocstoreFileRequest $r ): RedirectResponse
    {
        // Policy check performed in StoreDocstoreFileRequest
        $file = $r->file('uploadedFile');
        $path = $file->store( '', 'docstore' );

        /** @psalm-suppress InvalidArgument */
        $file = DocstoreFile::create( [
            'name'                  => $r->validated('name'),
            'description'           => $r->validated('description'),
            'docstore_directory_id' => $r->validated('docstore_directory_id'),
            'min_privs'             => $r->validated('min_privs'),
            'path'                  => $path,
            'sha256'                => hash_file( 'sha256', $file->getRealPath() ),
            'created_by'            => Auth::id(),
            'file_last_updated'     => now(),
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] uploaded by %s", $file->id, $file->name, Auth::user()->username ) );
        AlertContainer::push( __( 'File <em>:name</em> uploaded.', [ 'name' => $file->name ] ), Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
    }

    /**
     * Edit a docstore file uploaded
     *
     * @param Request           $r
     * @param DocstoreFile      $file
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function edit( Request $r , DocstoreFile $file ): View
    {
        $this->authorize( 'update', $file );

        Former::populate([
            'name'                  => $r->old( 'name',                         $file->name                         ),
            'description'           => $r->old( 'descripton',                   $file->description                  ),
            'sha256'                => $r->old( 'sha256',                       $file->sha256                       ),
            'min_privs'             => $r->old( 'min_privs',                    (string) $file->min_privs                    ),
            'docstore_directory_id' => $r->old( 'docstore_directory_id', $file->docstore_directory_id ?: ''  ),
        ]);

        return view( 'docstore/file/upload', [
            'file'                      => $file,
            'dirs'                      => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $r->user() ) )
        ] );
    }

    /**
     * Update a docstore file uploaded
     *
     * @param  UpdateDocstoreFileRequest  $r
     * @param  DocstoreFile  $file
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update( UpdateDocstoreFileRequest $r , DocstoreFile $file ): RedirectResponse
    {
        // Policy check performed in UpdateDocstoreFileRequest
        $user = Auth::user();

        // if a new file is updated
        if( $r->file('uploadedFile') ) {
            // get path of the old file in order to delete it later
            $oldPath        = $file->path;
            $uploadedFile   = $r->file('uploadedFile');
            $path           = $uploadedFile->store( '', 'docstore' );

            /** @psalm-suppress InvalidArgument */
            $file->update([
                'path'                  => $path,
                'sha256'                => hash_file( 'sha256', $uploadedFile->getRealPath() ),
                'file_last_updated'     => now(),
            ]);

            // Delete the old file
            Storage::disk( $file->disk )->delete( $oldPath );
        }

        // Purge the logs of the file
        if( $r->validated(  'purgeLogs' ) ) {
            Log::info( sprintf( "DocStore: all download logs for file [%d|%s] purged by %s", $file->id, $file->name, $user->username ) );
            $file->logs()->delete();
        }

        $file->update( [
            'name'                  => $r->validated('name'),
            'description'           => $r->validated('description'),
            'docstore_directory_id' => $r->validated('docstore_directory_id'),
            'min_privs'             => $r->validated('min_privs')
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] edited by %s", $file->id, $file->name, $user->username ) );
        AlertContainer::push( __( 'File <em>:name</em> updated.', [ 'name' => $file->name ] ), Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
    }

    /**
     * View a docstore file apply to allowed mimetype ( DocstoreFile::$extensionViewable )
     *
     * @param  DocstoreFile  $file
     *
     * @return RedirectResponse|View
     *
     * @throws AuthorizationException|FileNotFoundException
     */
    public function view( DocstoreFile $file ): RedirectResponse|View
    {
        $this->authorize( 'view', $file );

        if( !$file->isViewable() ) {
            return redirect( route( 'docstore-file@download', [ 'file' => $file->id ] ) );
        }

        if( Auth::user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => Auth::id() ] ) );
        }

        return view( 'docstore/file/view', [
            'file'    => $file,
            'content' => Storage::disk( $file->disk )->get( $file->path ),
        ]);
    }

    /**
     * Download a docstore file
     *
     * @param  DocstoreFile  $file
     *
     * @return StreamedResponse|RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function download( DocstoreFile $file ): StreamedResponse|RedirectResponse
    {
        $this->authorize( 'download', $file );

        if( Auth::user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => Auth::id() ] ) );
        }

        try {
            return Storage::disk( $file->disk )->download( $file->path, $file->name, [
                'Content-Type' => 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ] );
        } catch( FilesystemException $e ) {
            AlertContainer::push( __( "This file could not be found / downloaded. Please report this error to the support team." ), Alert::DANGER );
            return redirect()->back();
        }
    }

    /**
     * Delete a file
     *
     * @param  Request  $r
     * @param  DocstoreFile  $file
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function delete( Request $r , DocstoreFile $file ): RedirectResponse
    {
        $this->authorize( 'delete', $file );

        $dir = $file->directory;

        Storage::disk( $file->disk )->delete( $file->path );
        $file->logs()->delete();
        $file->delete();

        AlertContainer::push( __( 'File <em>:name</em> deleted.', [ 'name' => $file->name ] ), Alert::SUCCESS );
        Log::info( sprintf( "DocStore: file [%d|%s] deleted by %s", $file->id, $file->name, $r->user()->username ) );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir ] ) );
    }
}