<?php

namespace IXP\Http\Controllers\Docstore;

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

use Entities\User as UserEntity;
use Former\Facades\Former;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use D2EM;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use \League\Flysystem\Exception as FlySystemException;

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


use Illuminate\Validation\Rule;

use Illuminate\View\View;



class FileController extends Controller
{
    /**
     * View a docstore file apply to allowed mimetype ( DocstoreFile::$
     *
     * @param Request $request
     * @param DocstoreFile $file
     *
     * @return mixed
     *
     * @throws
     */
    public function view( Request $request, DocstoreFile $file )
    {
        $this->authorize( 'view', $file );

        if( !$file->isViewable() ) {
            return redirect( route( 'docstore-file@download', [ 'file' => $file->id ] ) );
        }

        if( $request->user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => $request->user()->getId() ] ) );
        }

        return view( 'docstore/file/view', [
            'file'      => $file,
            'content'   => $file->extension() != 'mp4' ? Storage::disk( $file->disk )->get( $file->path ) : ''
        ] );
    }

    /**
     * Download a docstore file
     *
     * @param Request $request
     * @param DocstoreFile $file
     *
     * @return mixed
     *
     * @throws
     */
    public function download( Request $request, DocstoreFile $file )
    {
        $this->authorize( 'download', $file );

        if( $request->user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => $request->user()->getId() ] ) );
        }

        try {
            return Storage::disk( $file->disk )->download( $file->path, $file->name );
        } catch( FlySystemException $e ) {
            AlertContainer::push( "This file could not be found / downloaded. Please report this error to the support team.", Alert::DANGER );
            return redirect( route( 'docstore-dir@list', [ 'dir' => $file->directory->id ] ) );
        }
    }

    /**
     * Get information on a docstore file
     *
     * @param Request $request
     * @param DocstoreFile $file
     *
     * @return mixed
     *
     * @throws
     */
    public function info( Request $request, DocstoreFile $file )
    {
        $this->authorize( 'info', $file );

        return view( 'docstore/file/info', [
            'file' => $file,
            'size' => Storage::disk( $file->disk )->size( $file->path ),
            'last_modified' => Storage::disk( $file->disk )->lastModified( $file->path ),
            'dspath' => config( 'filesystems.disks.' . $file->disk . '.root', '*** UNKNOWN LOCATION ***' ) . '/' . $file->path,
            'created_by' => D2EM::getRepository(UserEntity::class)->find($file->created_by),
        ]);
    }


    /**
     * Upload a new docstore file
     *
     * @param Request $request
     *
     * @return View
     *
     * @throws
     */
    public function upload( Request $request )
    {
        $this->authorize( 'create', DocstoreFile::class );

        Former::populate([
            'min_privs' => $request->old( 'min_privs', User::AUTH_SUPERUSER )
        ]);

        return view( 'docstore/file/upload', [
            'file'          => false,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $request->user() )  ),
        ] );
    }

    /**
     * Store a docstore file uploaded
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function store( Request $request ): RedirectResponse
    {
        $this->authorize( 'create', DocstoreFile::class );

        $this->checkForm( $request );
        $file = $request->file('uploadedFile');
        $path = $file->store( '', 'docstore' );

        $file = DocstoreFile::create( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs,
            'path'                  => $path,
            'sha256'                => hash_file( 'sha256', $file ),
            'created_by'            => $request->user()->getId(),
            'file_last_updated'     => now(),
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] uploaded by %s", $file->id, $file->name, $request->user()->getUsername() ) );

        AlertContainer::push( "File <em>{$request->name}</em> uploaded.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
    }

    /**
     * Edit a docstore file uploaded
     *
     * @param Request           $request
     * @param DocstoreFile      $file
     *
     * @return View
     *
     * @throws
     */
    public function edit( Request $request , DocstoreFile $file ): View
    {
        $this->authorize( 'update', $file );

        Former::populate([
            'name'                  => $request->old( 'name',           $file->name         ),
            'description'           => $request->old( 'descripton',     $file->description  ),
            'sha256'                => $request->old( 'sha256',         $file->sha256       ),
            'min_privs'             => $request->old( 'min_privs',      $file->min_privs    ),
            'docstore_directory_id' => $request->old( 'docstore_directory_id',$file->docstore_directory_id ?? '' ),
        ]);

        return view( 'docstore/file/upload', [
            'file'                      => $file,
            'dirs'                      => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $request->user() ) )
        ] );
    }

    /**
     * Update a docstore file uploaded
     *
     * @param Request $request
     * @param DocstoreFile $file
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function update( Request $request , DocstoreFile $file ): RedirectResponse
    {
        $this->authorize( 'update', $file );

        $this->checkForm( $request, $file );

        // if a new file is updated
        if( $request->uploadedFile ) {
            // get path of the old file in order to delete it later
            $oldPath = $file->path;

            $uploadedFile = $request->file('uploadedFile');
            $path = $uploadedFile->store( '', 'docstore' );

            $file->update([
                'path'                  => $path,
                'sha256'                => hash_file( 'sha256', $uploadedFile ),
                'file_last_updated'     => now(),
            ]);

            // Delete the old file
            Storage::disk( $file->disk )->delete( $oldPath );
        }

        // Purge the logs of the file
        if( $request->purgeLogs ) {
            Log::info( sprintf( "DocStore: all download logs for file [%d|%s] purged by %s", $file->id, $file->name, $request->user()->getUsername() ) );
            $file->logs()->delete();
        }

        $file->update( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs
        ] );

        Log::info( sprintf( "DocStore: file [%d|%s] edited by %s", $file->id, $file->name, $request->user()->getUsername() ) );

        AlertContainer::push( "File <em>{$request->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
    }


    /**
     * Check if the form is valid
     *
     * @param Request $request
     * @param DocstoreFile $file
     *
     */
    private function checkForm( Request $request, ?DocstoreFile $file = null )
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
            'min_privs'     => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES_TEXT_ALL ) ),
            'docstore_directory_id' => [ 'nullable', 'integer',
                function ($attribute, $value, $fail) {
                    if( !DocstoreDirectory::where( 'id', $value )->exists() ) {
                        return $fail( 'Directory does not exist.' );
                    }
                },
            ]
        ] );
    }


    /**
     * Delete a file
     *
     * @param Request $request
     *
     * @param DocstoreFile $file
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request , DocstoreFile $file ): RedirectResponse
    {
        $this->authorize( 'delete', $file );

        $dir = $file->directory;

        Storage::disk( $file->disk )->delete( $file->path );
        $file->logs()->delete();

        AlertContainer::push( "File <em>{$file->name}</em> deleted.", Alert::SUCCESS );
        Log::info( sprintf( "DocStore: file [%d|%s] deleted by %s", $file->id, $file->name, $request->user()->getUsername() ) );

        $file->delete();

        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir ] ) );
    }
}
