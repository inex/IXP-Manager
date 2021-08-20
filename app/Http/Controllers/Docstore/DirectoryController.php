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

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

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

/**
 * DirectoryController Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Docstore
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DirectoryController extends Controller
{
    /**
     * Display the list of directories
     *
     * @param DocstoreDirectory|null    $dir
     *
     * @return View|RedirectResponse
     */
    public function list( DocstoreDirectory $dir = null ): View|RedirectResponse
    {
        $privs  = User::AUTH_PUBLIC;
        if( $user   = Auth::user() ){
            $privs  = $user->privs;
        }

        $dirs   = DocstoreDirectory::getHierarchyForUserClass( optional( $user )->privs() ?? 0 )[ $dir->id ?? '' ] ?? [];
        $files  = DocstoreFile::getListing( $dir, $privs );

        $nbTotalDirs    = count( DocstoreDirectory::getHierarchyForUserClass( User::AUTH_SUPERUSER )[ $dir->id ?? '' ] ?? [] );
        $nbTotalFiles   = count( DocstoreFile::getListing( $dir, User::AUTH_SUPERUSER ) );

        if( !count($dirs) && count($dirs) <= $nbTotalDirs && !count($files) && count($files) <= $nbTotalFiles && ( $nbTotalDirs + $nbTotalFiles ) > 0 ) {
            // Only show a folder if there's a file (or folder) there for the user to see:
            if( !Auth::check() ){
                return redirect( route( 'login@login' ) );
            }

            abort( 401, 'Nothing for you here. You either need to log in or you do not have sufficient privileges.' );
        }

        return view( 'docstore/dir/list', [
            'dir'       => $dir ?: false,
            'dirs'      => $dirs,
            'files'     => $files,
        ] );
    }

    /**
     * Create a new directory
     *
     * @param  Request  $r
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function create( Request $r ): View
    {
        $this->authorize( 'create', DocstoreDirectory::class );

        return view( 'docstore/dir/create', [
            'dir'           => false,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, Auth::getUser() ) ),
        ] );
    }

    /**
     * Store a directory
     *
     * @param  Request  $r
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store( Request $r ): RedirectResponse
    {
        $this->authorize( 'create', DocstoreDirectory::class );
        $this->checkForm( $r );
        $dir = DocstoreDirectory::create( $r->all() );

        Log::info( sprintf( "DocStore: new directory [%d|%s] created by %s", $dir->id, $dir->name, Auth::getUser()->username ) );
        AlertContainer::push( "New directory <em>{$dir->name}</em> created.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir->id ] ) );
    }

    /**
     * Edit a new directory
     *
     * @param Request           $r
     * @param DocstoreDirectory $dir
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function edit( Request $r, DocstoreDirectory $dir ): View
    {
        $this->authorize( 'update', $dir );

        Former::populate([
            'name'                  => $r->old( 'name',                 $dir->name                  ),
            'description'           => $r->old( 'descripton',           $dir->description           ),
            'parent_dir_id'         => $r->old( 'parent_dir',    $dir->parent_dir_id ?: ''    ),
        ]);

        return view( 'docstore/dir/create', [
            'dir'           => $dir,
            'dirs'          => DocstoreDirectory::getListingForDropdown( DocstoreDirectory::getListing( null, Auth::user() ) ),
        ] );
    }

    /**
     * Update a directory
     *
     * @param Request               $r
     * @param DocstoreDirectory     $dir
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update( Request $r , DocstoreDirectory $dir ): RedirectResponse
    {
        $this->authorize( 'update', $dir );
        $this->checkForm( $r );
        $dir->update( $r->all() );

        Log::info( sprintf( "DocStore: directory [%d|%s] edited by %s", $dir->id, $dir->name, Auth::user()->username ) );
        AlertContainer::push( "Directory <em>{$dir->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Delete a directory
     *
     * @param Request           $r
     * @param DocstoreDirectory $dir
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function delete( Request $r , DocstoreDirectory $dir ): RedirectResponse
    {
        $this->authorize( 'delete', $dir );

        Log::notice( sprintf( "DocStore: start recursive deletion of directory [%d|%s] by %s", $dir->id, $dir->name, $r->user()->username ) );
        DocstoreDirectory::recursiveDelete( $dir );
        Log::notice( sprintf( "DocStore: finish recursive deletion of directory [%d|%s] by %s", $dir->id, $dir->name, $r->user()->username ) );

        AlertContainer::push( "Directory <em>{$dir->name}</em> deleted.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     *
     * @return void
     */
    private function checkForm( Request $r ): void
    {
        $r->validate( [
            'name'          => 'required|max:100',
            'description'   => 'nullable',
            'parent_dir_id' => 'nullable|integer|exists:docstore_directories,id',
        ] );
    }
}