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

use Auth, Former;

use Entities\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    DocstoreDirectory,
    DocstoreFile
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * DirectoryController Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Docstore
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DirectoryController extends Controller
{
    /**
     * Display the list of directories
     *
     * @param Request $request
     * @param DocstoreDirectory|null $dir
     *
     * @return View|RedirectResponse
     */
    public function list( Request $request, DocstoreDirectory $dir = null )
    {
        $privs  = User::AUTH_PUBLIC;
        if( $user = $request->user() ){
            $privs = $user->getPrivs();
        }

        $dirs  = DocstoreDirectory::getHierarchyForUserClass( optional( $user )->getPrivs() ?? 0 )[ $dir ? $dir->id : '' ] ?? [];
        $files = DocstoreFile::getListing( $dir, $privs );

        $nbTotalDirs  = count( DocstoreDirectory::getHierarchyForUserClass( User::AUTH_SUPERUSER )[ $dir ? $dir->id : '' ] ?? [] );
        $nbTotalFiles = DocstoreFile::getListing( $dir, User::AUTH_SUPERUSER )->count();

        // Only show a folder if there's a file (or folder) there for the user to see:
        if( !count($dirs) && count($dirs) <= $nbTotalDirs && !$files->count() && $files->count() <= $nbTotalFiles && ( $nbTotalDirs + $nbTotalFiles ) > 0) {
            if( !Auth::check() ){
                return redirect( route( 'login@login' ) );
            }

            abort( 401, 'Nothing for you here. You either need to log in or you do not have sufficient privileges.' );
        }

        return view( 'docstore/dir/list', [
            'dir'       => $dir ?? false,
            'dirs'      => $dirs,
            'files'     => $files,
        ] );
    }

    /**
     * Create a new directory
     *
     * @param Request $request
     *
     * @return View
     *
     * @throws
     */
    public function create( Request $request )
    {
        $this->authorize( 'create', DocstoreDirectory::class );

        return view( 'docstore/dir/create', [
            'dir'           => false,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $request->user() )  ),
            'parent_dir'    => $request->input( 'parent_dir', false )
        ] );
    }

    /**
     * Edit a new directory
     *
     * @param Request           $request
     * @param DocstoreDirectory $dir
     *
     * @return View
     *
     * @throws
     */
    public function edit( Request $request, DocstoreDirectory $dir ): View
    {
        $this->authorize( 'update', $dir );

        Former::populate([
            'name'                  => $request->old( 'name',               $dir->name          ),
            'description'           => $request->old( 'descripton',         $dir->description   ),
            'parent_dir'            => $request->old( 'parent_dir', $dir->parent_dir_id ?? '' ),
        ]);

        return view( 'docstore/dir/create', [
            'dir'           => $dir,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, $request->user() ) ),
            'parent_dir'    => $dir->parent_dir_id
        ] );
    }

    /**
     * Store a directory
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function store( Request $request ): RedirectResponse
    {
        $this->authorize( 'create', DocstoreDirectory::class );

        $this->checkForm( $request );

        $dir = DocstoreDirectory::create( [ 'name' => $request->name, 'description' => $request->description, 'parent_dir_id' => $request->parent_dir ] );

        Log::info( sprintf( "DocStore: new directory [%d|%s] created by %s", $dir->id, $dir->name, $request->user()->getUsername() ) );

        AlertContainer::push( "New directory <em>{$request->name}</em> created.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir->id ] ) );
    }

    /**
     * Update a directory
     *
     * @param Request $request
     *
     * @param DocstoreDirectory $dir
     * @return RedirectResponse
     *
     * @throws
     */
    public function update( Request $request , DocstoreDirectory $dir ): RedirectResponse
    {
        $this->authorize( 'update', $dir );

        $this->checkForm( $request );

        $dir->update( [ 'name' => $request->name, 'description' => $request->description, 'parent_dir_id' => $request->parent_dir ] );

        Log::info( sprintf( "DocStore: directory [%d|%s] edited by %s", $dir->id, $dir->name, $request->user()->getUsername() ) );

        AlertContainer::push( "Directory <em>{$request->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Delete a directory
     *
     * @param Request           $request
     * @param DocstoreDirectory $dir
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request , DocstoreDirectory $dir ): RedirectResponse
    {
        $this->authorize( 'delete', $dir );

        Log::notice( sprintf( "DocStore: start recursive deletion of directory [%d|%s] by %s", $dir->id, $dir->name, $request->user()->getUsername() ) );
        DocstoreDirectory::recursiveDelete( $dir );
        Log::notice( sprintf( "DocStore: finish recursive deletion of directory [%d|%s] by %s", $dir->id, $dir->name, $request->user()->getUsername() ) );

        AlertContainer::push( "Directory <em>{$dir->name}</em> deleted.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     */
    private function checkForm( $request )
    {
        $request->validate( [
            'name'          => 'required|max:100',
            'description'   => 'nullable',
            'parent_dir_id' => [ 'nullable', 'integer',
                function ($attribute, $value, $fail) {
                    if( !DocstoreDirectory::where( $attribute, $value )->exists() ) {
                        return $fail( 'Parent directory is invalid / does not exist.' );
                    }
                },
            ]
        ] );
    }
}
