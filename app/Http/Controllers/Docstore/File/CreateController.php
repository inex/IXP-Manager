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

use Illuminate\Http\{File, RedirectResponse, Request};

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

use Storage;
use Str;

class CreateController extends Controller
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

        Former::populate([
            'min_privs' => $request->old( 'min_privs',      User::AUTH_SUPERUSER   )
        ]);

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
            'fileContent'           => $request->old( 'fileContent',    Storage::disk( $file->disk )->get( $file->path ) ),
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

        $this->checkForm( $request );

        // FIXME The problem here is that when we create a new file with Storage::put() this file doesnt have a hashed name
        // FIXME solution 1 : create a file and hash the name ourself
        // FIXME I think this solution is the best
        $hashName = Str::random(40) . '.' . pathinfo( $request->name, PATHINFO_EXTENSION ) ;

        Storage::disk( 'docstore' )->put( $hashName , $request->fileContent );

        $newFile = new File( storage_path(). '/docstore/' . $hashName );

        $file = DocstoreFile::create( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs,
            'path'                  => $hashName,
            'sha256'                => hash_file( 'sha256', $newFile )
        ] );
        // FIXME end solution 1

        // FIXME solution 2 : create a file and rename it with the hash name
        /*// create file
        Storage::disk( 'docstore' )->put( $request->name , $request->fileContent  );

        // get the file
        $newFile = new File( storage_path(). '/docstore/' . $request->name );

        // rename the original file with the hash name
        Storage::disk( 'docstore' )->move( $newFile->getFilename() , $hashname = explode( '.' , $newFile->hashName() )[0] . '.' . $newFile->getExtension() );

        // get the file with updated name
        $newFile = new File( storage_path(). '/docstore/' . $hashname );

        $file = DocstoreFile::create( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs,
            'path'                  => $hashname,
            'sha256'                => hash_file( 'sha256', $newFile )
        ] );*/
        // FIXME end solution 2


        // FIXME solution 3 : create temporary file then create the final file based on the temporary on in order to get the hash name
/*        // create a temporary file
        Storage::disk( 'docstore' )->put( $request->name , $request->fileContent );

        // create the final file with an hashed name
        $path = Storage::disk( 'docstore' )->putFile( '' , $temporaryFile = new File( storage_path(). '/docstore/' . $request->name ) );

        $file = DocstoreFile::create( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs,
            'path'                  => $path,
            'sha256'                => hash_file( 'sha256', $temporaryFile )
        ] );

        // Delete temporary file
        Storage::disk( 'docstore' )->delete( $temporaryFile->getFilename() );*/

        // FIXME end solution 3

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

        $this->checkForm( $request );

        // Edit stored file
        $fileEdited = new File( storage_path() . '/' .$file->disk . '/' . $file->path );
        $fileEdited->openFile( 'w' )->fwrite( $request->fileContent );

        $path       = $file->path;
        $extension  = pathinfo( strtolower( $request->name ), PATHINFO_EXTENSION );

        // Check if the extension of the file has be modified
        if( $extension != strtolower( $fileEdited->getExtension() ) ) {
            // Update the filename with the new extension
            Storage::disk( $file->disk )->move( $fileEdited->getFilename(),
                $path = pathinfo( $fileEdited, PATHINFO_FILENAME ) . '.'  . $extension
            );
        }

        // Get the new instance of the file
        $fileEdited = new File( storage_path() . '/' .$file->disk . '/' . $path );

        $file->update( [
            'name'                  => $request->name,
            'description'           => $request->description,
            'docstore_directory_id' => $request->docstore_directory_id,
            'min_privs'             => $request->min_privs,
            'path'                  => $path,
            'sha256'                => hash_file( 'sha256', $fileEdited )
        ] );

        AlertContainer::push( "File <em>{$request->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $file->docstore_directory_id ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request $request
     */
    private function checkForm( Request $request )
    {
        $request->validate( [
            'name'          => [ 'required',
                'max:100',
                function ( $attribute, $value, $fail ) {
                    if( !Str::endsWith( strtolower( $value ), DocstoreFile::$extensionViewable ) ) {
                        return $fail( $attribute.' must have one of the following extensions: ' . implode( ', ', DocstoreFile::$extensionViewable ) );
                    }
                },
            ],
            'min_privs'     => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES_TEXT_ALL ) ),
            'docstore_directory_id' => [ 'nullable', 'integer',
                function ( $attribute, $value, $fail ) {
                    if( !DocstoreDirectory::where( 'id', $value )->exists() ) {
                        return $fail( $attribute.' is invalid.' );
                    }
                },
            ],
            'fileContent'   => 'required',
        ] );
    }
}
