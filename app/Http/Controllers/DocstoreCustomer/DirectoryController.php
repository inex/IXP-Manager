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

use Former;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use Illuminate\Support\Str;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Customer,
    DocstoreCustomerDirectory,
    DocstoreCustomerFile,
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * DirectoryController Controller
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   DocstoreCustomer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DirectoryController extends Controller
{
    /**
     * Display the list of all Customer with docstore
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function listCustomers() : View
    {
        $this->authorize( 'listCustomers', [ DocstoreCustomerDirectory::class ] );

        return view( 'docstore-customer/dir/customers', [
            'files'      => DocstoreCustomerFile::groupBy( 'cust_id' )->get()
        ] );
    }

    /**
     * Display the list of directories for a customer
     *
     * @param Request                           $r
     * @param Customer                          $cust
     * @param DocstoreCustomerDirectory|null    $dir
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function list( Request $r, Customer $cust, DocstoreCustomerDirectory $dir = null ) : View
    {
        $this->authorize( 'list', [ DocstoreCustomerDirectory::class, $cust ] );

        return view( 'docstore-customer/dir/list', [
            'dir'           => $dir ?? false,
            'cust'          => $cust,
            'dirs'          => DocstoreCustomerDirectory::getHierarchyForCustomerAndUserClass( $cust, $r->user()->privs(), false )[ $dir ? $dir->id : '' ] ?? [],
            'files'         => DocstoreCustomerFile::getListing( $cust, $r->user(), $dir ),
            'ppp_files'     => $cust->patchPanelPorts()->with( 'patchPanelPortFiles' )
                ->has($r->user()->isSuperUser() ? 'patchPanelPortFiles' : 'patchPanelPortFilesPublic' )->get()
                ->pluck( 'patchPanelPortFiles' )->isNotEmpty(),
            'ppph_files'    => $r->user()->isSuperUser() ? $cust->patchPanelPortHistories()
                ->with( 'patchPanelPortHistoryFiles' )->has( 'patchPanelPortHistoryFiles' )
                ->get()->pluck( 'patchPanelPortHistoryFiles' )->isNotEmpty() : false,
        ] );
    }

    /**
     * Display the list of patch panel file for a customer
     *
     * @param Request           $r
     * @param Customer|null     $cust
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function listPatchPanelPortFiles( Request $r,  Customer $cust = null ) : View
    {
        $this->authorize( 'listPatchPanelPortFiles', [ DocstoreCustomerDirectory::class, $cust ] );

        return view( 'docstore-customer/dir/list-ppp-files', [
            'cust'          => $cust,
            'history'       => false,
            'files'         => $cust->patchPanelPorts()->with( $r->user()->isSuperUser() ? 'patchPanelPortFiles' : 'patchPanelPortFilesPublic' )
                ->has($r->user()->isSuperUser() ? 'patchPanelPortFiles' : 'patchPanelPortFilesPublic' )->get()
                ->pluck( 'patchPanelPortFiles' )->flatten(),
        ] );
    }

    /**
     * Display the list of patch panel file history for a customer
     *
     * @param Request                           $r
     * @param Customer                          $cust
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function listPatchPanelPortHistoryFiles( Request $r,  Customer $cust ) : View
    {
        $this->authorize( 'listPatchPanelPortFilesHistory', [ DocstoreCustomerDirectory::class, $cust ]);

        return view( 'docstore-customer/dir/list-ppp-files', [
            'cust'          => $cust,
            'history'       => true,
            'files'         => $r->user()->isSuperUser() ? $cust->patchPanelPortHistories()->with( 'patchPanelPortHistoryFiles' )
                ->has( 'patchPanelPortHistoryFiles' )->get()
                ->pluck( 'patchPanelPortHistoryFiles' )->flatten() : [],
        ] );
    }
    /**
     * Create a new customer directory
     *
     * @param Request   $r
     * @param Customer  $cust
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function create( Request $r, Customer $cust ): view
    {
        $this->authorize( 'create', [ DocstoreCustomerDirectory::class, $cust ] );

        return view( 'docstore-customer/dir/create', [
            'dir'               => false,
            'cust'              => $cust,
            'dirs'              => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $cust, $r->user() )  ),
            'parent_dir_id'     => $r->input( 'parent_dir_id', false )
        ] );
    }

    /**
     * Edit a customer directory
     *
     * @param Request                       $r
     * @param Customer                      $cust
     * @param DocstoreCustomerDirectory     $dir
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function edit( Request $r, Customer $cust, DocstoreCustomerDirectory $dir ): View
    {
        $this->authorize( 'update', [ DocstoreCustomerDirectory::class, $dir ] );

        Former::populate([
            'name'                  => $r->old( 'name',                    $dir->name          ),
            'description'           => $r->old( 'descripton',              $dir->description   ),
            'parent_dir_id'         => $r->old( 'parent_dir_id',    $dir->parent_dir_id ?? '' ),
        ]);

        return view( 'docstore-customer/dir/create', [
            'dir'               => $dir,
            'cust'              => $cust,
            'dirs'              => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $cust, $r->user() ) ),
            'parent_dir_id'     => $dir->parent_dir_id
        ] );
    }

    /**
     * Store a customer directory
     *
     * @param Request   $r
     * @param Customer  $cust
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store( Request $r, Customer $cust ): RedirectResponse
    {
        $this->authorize( 'create', [ DocstoreCustomerDirectory::class, $cust ] );

        $this->checkForm( $r );

        $dir = DocstoreCustomerDirectory::create( $r->all() );

        Log::info( sprintf( "DocStore: new directory [%d|%s] created by %s for the customer [%d|%s]", $dir->id, $dir->name, $r->user()->username, $cust->id, $cust->name ) );

        AlertContainer::push( "New per-" . config( 'ixp_fe.lang.customer.one' ) . " directory <em>{$r->name}</em> created.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust,'dir' => $dir->id ] ) );
    }

    /**
     * Update a customer directory
     *
     * @param Request                   $r
     * @param Customer                  $cust
     * @param DocstoreCustomerDirectory $dir
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update( Request $r , Customer $cust, DocstoreCustomerDirectory $dir ): RedirectResponse
    {
        $this->authorize( 'update', [ DocstoreCustomerDirectory::class, $dir ] );

        $this->checkForm( $r );

        $dir->update( [ 'name' => $r->name, 'description' => $r->description, 'parent_dir_id' => $r->parent_dir_id ] );

        Log::info( sprintf( "DocStore: customer directory [%d|%s] edited by %s", $dir->id, $dir->name, $r->user()->username ) );

        AlertContainer::push( "Per-" . config( 'ixp_fe.lang.customer.one' ) . " directory  <em>{$r->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust, 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Delete a directory
     *
     * @param Request                   $r
     * @param DocstoreCustomerDirectory $dir
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function delete( Request $r , DocstoreCustomerDirectory $dir ): RedirectResponse
    {
        $this->authorize( 'delete', $dir );

        Log::notice( sprintf( "DocStore: start recursive deletion of directory [%d|%s] by %s for the customer [%d|%s]", $dir->id, $dir->name, $r->user()->username, $dir->customer->id, $dir->customer->name ) );
        DocstoreCustomerDirectory::recursiveDelete( $dir );
        Log::notice( sprintf( "DocStore: finish recursive deletion of directory [%d|%s] by %s for the customer [%d|%s]", $dir->id, $dir->name, $r->user()->username, $dir->customer->id, $dir->customer->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) .  "Directory <em>{$dir->name}</em> deleted.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $dir->customer , 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Delete a directory
     *
     * @param Customer  $cust
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function deleteForCustomer( Customer $cust ): RedirectResponse
    {
        $this->authorize( 'deleteForCustomer', [ DocstoreCustomerDirectory::class, $cust ] );

        Log::notice( sprintf( "DocStore: start purge for the customer [%d|%s]", $cust->id, $cust->name ) );
        DocstoreCustomerDirectory::deleteAllForCustomer( $cust );
        Log::notice( sprintf( "DocStore: finish purge for the customer [%d|%s]", $cust->id, $cust->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) .  " <em>{$cust->name}</em> purged.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@customers' ) );
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    private function checkForm( Request $r ): void
    {
        $r->validate( [
            'name' => [ 'required', 'max:100',
                function( $attribute, $value, $fail ) {
                    if( Str::startsWith(strtolower( $value ), 'patch panel port' ) ) {
                        return $fail( '"Patch Panel Port..." is a reserved name.' );
                    }
                }
            ],
            'cust_id'          => [ 'required', 'integer',
                function( $attribute, $value, $fail ) use ($r) {
                    if( !Customer::find( $value ) ) {
                        Log::notice( "Attempt to create/edit a directory where the customer ID [{$value}] is invalid / does not exist by user ID {$r->user()->id}." );
                        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' is invalid / does not exist.', Alert::DANGER );
                        return $fail( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' is invalid / does not exist.' );
                    }
                }
            ],
            'description'   => 'nullable',
            'parent_dir_id' => [ 'nullable', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !DocstoreCustomerDirectory::find( $value ) ) {
                        return $fail( 'Parent directory is invalid / does not exist.' );
                    }
                }
            ]
        ] );
    }
}