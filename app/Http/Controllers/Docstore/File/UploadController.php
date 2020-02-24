<?php

namespace IXP\Http\Controllers\Docstore\File;

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

use Illuminate\Validation\Rule;

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    DocstoreDirectory,
    DocstoreFile,
    User
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

class UploadController extends Controller
{
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
            'min_privs'             => $request->old( 'min_privs',      User::AUTH_SUPERUSER   )
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
            'sha256'                => hash_file( 'sha256', $file )
        ] );

        AlertContainer::push( "File <em>{$request->name}</em> created.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
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

        $file->update( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs
        ] );

        AlertContainer::push( "File <em>{$request->name}</em> updated.", Alert::SUCCESS );
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
            'uploadedFile'  => Rule::requiredIf( function () use ( $request, $file ) {
                return !$file ;
            }),
            'sha256'        => [ 'nullable', 'max:64',
                function ($attribute, $value, $fail ) use( $request ) {
                    if( $value && $request->file('uploadedFile' ) && $value != hash_file( 'sha256', $request->file( 'uploadedFile' ) ) ) {
                        return $fail( $attribute.' is invalid.' );
                    }
                },
            ],
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
