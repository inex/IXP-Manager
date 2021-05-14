<?php

namespace IXP\Http\Controllers\Docstore;

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

use Auth;
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

use Illuminate\Validation\Rule;

use Illuminate\View\View;

use League\Flysystem\Exception as FlySystemException;

use IXP\Http\Controllers\Controller;

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
            'min_privs' => $r->old( 'min_privs', User::AUTH_SUPERUSER )
        ]);

        return view( 'docstore/file/upload', [
            'file'          => false,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $r->user() )  ),
        ] );
    }

    /**
     * Store a docstore file uploaded
     *
     * @param Request $r
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store( Request $r ): RedirectResponse
    {
        $this->authorize( 'create', DocstoreFile::class );

        $this->checkForm( $r );
        $file = $r->file('uploadedFile');
        $path = $file->store( '', 'docstore' );

        $file = DocstoreFile::create( [
            'name'                  => $r->name,
            'description'           => $r->description,
            'docstore_directory_id' => $r->docstore_directory_id,
            'min_privs'             => $r->min_privs,
            'path'                  => $path,
            'sha256'                => hash_file( 'sha256', $file ),
            'created_by'            => Auth::id(),
            'file_last_updated'     => now(),
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] uploaded by %s", $file->id, $file->name, Auth::user()->username ) );
        AlertContainer::push( "File <em>{$r->name}</em> uploaded.", Alert::SUCCESS );
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
            'min_privs'             => $r->old( 'min_privs',                    $file->min_privs                    ),
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
     * @param  Request  $r
     * @param  DocstoreFile  $file
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update( Request $r , DocstoreFile $file ): RedirectResponse
    {
        $this->authorize( 'update', $file );
        $this->checkForm( $r, $file );

        $user = Auth::user();

        // if a new file is updated
        if( $r->uploadedFile ) {
            // get path of the old file in order to delete it later
            $oldPath        = $file->path;
            $uploadedFile   = $r->file('uploadedFile');
            $path           = $uploadedFile->store( '', 'docstore' );

            $file->update([
                'path'                  => $path,
                'sha256'                => hash_file( 'sha256', $uploadedFile ),
                'file_last_updated'     => now(),
            ]);

            // Delete the old file
            Storage::disk( $file->disk )->delete( $oldPath );
        }

        // Purge the logs of the file
        if( $r->purgeLogs ) {
            Log::info( sprintf( "DocStore: all download logs for file [%d|%s] purged by %s", $file->id, $file->name, $user->username ) );
            $file->logs()->delete();
        }

        $file->update( [
            'name'                  => $r->name,
            'description'           => $r->description,
            'docstore_directory_id' => $r->docstore_directory_id,
            'min_privs'             => $r->min_privs
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] edited by %s", $file->id, $file->name, $user->username ) );
        AlertContainer::push( "File <em>{$file->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
    }

    /**
     * View a docstore file apply to allowed mimetype ( DocstoreFile::$
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
     * @return mixed
     *
     * @throws AuthorizationException
     */
    public function download( DocstoreFile $file ): mixed
    {
        $this->authorize( 'download', $file );

        if( Auth::user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => Auth::id() ] ) );
        }

        try {
            return Storage::disk( $file->disk )->download( $file->path, $file->name );
        } catch( FlySystemException $e ) {
            AlertContainer::push( "This file could not be found / downloaded. Please report this error to the support team.", Alert::DANGER );
            return redirect()->back();
        }
    }

    /**
     * Get information on a docstore file
     *
     * @param  DocstoreFile  $file
     *
     * @return mixed
     *
     * @throws AuthorizationException
     */
    public function info( DocstoreFile $file ): mixed
    {
        $this->authorize( 'info', $file );

        return view( 'docstore/file/info', [
            'file'          => $file,
            'size'          => Storage::disk( $file->disk )->size( $file->path ),
            'last_modified' => Storage::disk( $file->disk )->lastModified( $file->path ),
            'dspath'        => config( 'filesystems.disks.' . $file->disk . '.root', '*** UNKNOWN LOCATION ***' ) . '/' . $file->path,
            'created_by'    => User::find( $file->created_by ),
            'created_at'    => $file->created_at,
        ]);
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

        AlertContainer::push( "File <em>{$file->name}</em> deleted.", Alert::SUCCESS );
        Log::info( sprintf( "DocStore: file [%d|%s] deleted by %s", $file->id, $file->name, $r->user()->username ) );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request               $r
     * @param DocstoreFile|null     $file
     *
     * @return void
     */
    private function checkForm( Request $r, ?DocstoreFile $file = null ): void
    {
        $r->validate( [
            'name'                  => 'required|max:100',
            'uploadedFile'          => Rule::requiredIf( function() use ( $r, $file ) {
                return !$file;
            }),
            'sha256'                => 'nullable|max:64' . ( $r->file( 'uploadedFile' ) ? '|in:' . hash_file( 'sha256', $r->file( 'uploadedFile' ) ) : '' ) ,
            'min_privs'             => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES_TEXT_ALL ) ),
            'docstore_directory_id' => 'nullable|integer|exists:docstore_directories,id',
        ] );
    }
}