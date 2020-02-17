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

use Former;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;
use IXP\Http\Controllers\Controller;

use IXP\Models\{DocstoreDirectory, DocstoreFile, DocstoreLog, User};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Storage;

class FileController extends Controller
{
    /**
     * Create a new docstore file
     *
     * @param Request $request
     *
     * @return View
     *
     * @throws
     */
    public function create( Request $request )
    {
        $this->authorize( 'create', DocstoreFile::class );

        return view( 'docstore/file/create', [
            'file'          => false,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $request->user() )  ),
        ] );
    }

    /**
     * Edit a docstore file
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
            'min_privs'             => $request->old( 'min_privs',      $file->min_privs    ),
            'docstore_directory_id' => $request->old( 'docstore_directory_id',$file->docstore_directory_id ?? '' ),
        ]);

        return view( 'docstore/file/create', [
            'file'                      => $file,
            'dirs'                      => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $request->user() ) )
        ] );
    }

    /**
     * Store a docstore file
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

        $this->checkForm( $request, false );

        $path = $request->file('uploadedFile')[ 0 ]->store( '', 'docstore' );

        $file = DocstoreFile::create( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs,
            'path'                  => $path
        ] );

        AlertContainer::push( "File <em>{$request->name}</em> created.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
    }

    /**
     * Update a docstore file
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

        $file->update( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs,
        ] );

        AlertContainer::push( "File <em>{$request->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
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
        $this->authorize( 'view', $file );

        if( $request->user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => $request->user()->getId() ] ) );
        }

        return Storage::disk( $file->disk )->download( $file->path, $file->name );
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

        $dir = $file->docstore_directory_id;

        Storage::disk($file->disk )->delete($file->path );

        $file->logs()->delete();

        $file->delete();

        AlertContainer::push( "File <em>{$request->name}</em> deleted.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request $request
     * @param DocstoreFile $file
     */
    private function checkForm( Request $request, DocstoreFile $file )
    {
        // Display a message as the input file is hidden
        if( !$file && !$request->uploadedFile ){
            AlertContainer::push( "You need to upload a file.", Alert::DANGER );
        }

        $request->validate( [
            'name'          => 'required|max:100',
            'description'   => 'nullable',
            'uploadedFile'  => ( $file ? 'nullable' : 'required'),
            'min_privs'     => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES_TEXT_ALL ) ),
            'docstore_directory_id' => [ 'nullable', 'integer',
                function ($attribute, $value, $fail) {
                    if( !DocstoreDirectory::where( 'id', $value )->exists() ) {
                        return $fail( $attribute.' is invalid.' );
                    }
                },
            ]
        ] );
    }
}
