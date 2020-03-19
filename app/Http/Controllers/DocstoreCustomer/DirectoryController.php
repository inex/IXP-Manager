<?php

namespace IXP\Http\Controllers\DocstoreCustomer;

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

use Illuminate\Support\Facades\Log;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Customer,
    DocstoreCustomerDirectory,
    DocstoreCustomerFile
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * DirectoryController Controller
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   DocstoreCustomer
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DirectoryController extends Controller
{
    /**
     * Display the list of all Customer with docstore
     *
     * @param Request                           $request
     *
     * @return View
     *
     * @throws
     */
    public function listCustomers( Request $request ) : View
    {
        $this->authorize( 'listCustomer', [ DocstoreCustomerDirectory::class ] );

        return view( 'docstore-customer/dir/customers', [
            'files'      => DocstoreCustomerFile::groupBy( 'cust_id' )->get()
        ] );
    }

    /**
     * Display the list of directories for a customer
     *
     * @param Request                           $request
     * @param Customer|null                     $cust
     * @param DocstoreCustomerDirectory|null    $dir
     *
     * @return View
     *
     * @throws
     */
    public function list( Request $request, Customer $cust = null, DocstoreCustomerDirectory $dir = null ) : View
    {
        $this->authorize( 'list', [ DocstoreCustomerDirectory::class, $cust ]);

        return view( 'docstore-customer/dir/list', [
            'dir'       => $dir ?? false,
            'cust'      => $cust ?? false,
            'dirs'      => DocstoreCustomerDirectory::getHierarchyForCustomerAndUserClass( $cust, optional( $request->user() )->getPrivs() ?? 0 , false )[ $dir ? $dir->id : '' ] ?? [],
            'files'     => DocstoreCustomerFile::getListing( $cust, $dir, $request->user() ),
        ] );
    }

    /**
     * Create a new customer directory
     *
     * @param Request   $request
     * @param Customer  $cust
     *
     * @return View
     *
     * @throws
     */
    public function create( Request $request, Customer $cust )
    {
        $this->authorize( 'create', [ DocstoreCustomerDirectory::class, $cust ] );

        return view( 'docstore-customer/dir/create', [
            'dir'           => false,
            'cust'          => $cust,
            'dirs'          => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $cust,null, $request->user() )  ),
            'parent_dir'    => $request->input( 'parent_dir', false )
        ] );
    }

    /**
     * Edit a customer directory
     *
     * @param Request                       $request
     * @param Customer                      $cust
     * @param DocstoreCustomerDirectory     $dir
     *
     * @return View
     *
     * @throws
     */
    public function edit( Request $request, Customer $cust, DocstoreCustomerDirectory $dir ): View
    {
        $this->authorize( 'update', [ DocstoreCustomerDirectory::class, $cust, $dir ] );

        Former::populate([
            'name'                  => $request->old( 'name',               $dir->name          ),
            'description'           => $request->old( 'descripton',         $dir->description   ),
            'parent_dir'            => $request->old( 'parent_dir', $dir->parent_dir_id ?? '' ),
        ]);

        return view( 'docstore-customer/dir/create', [
            'dir'           => $dir,
            'cust'          => $cust,
            'dirs'          => DocstoreCustomerDirectory::getListingForDropdown( DocstoreCustomerDirectory::getListing( $cust, null, $request->user() ) ),
            'parent_dir'    => $dir->parent_dir_id
        ] );
    }

    /**
     * Store a customer directory
     *
     * @param Request   $request
     * @param Customer  $cust
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function store( Request $request, Customer $cust ): RedirectResponse
    {
        $this->authorize( 'create', [ DocstoreCustomerDirectory::class, $cust ] );

        $this->checkForm( $request );

        $dir = DocstoreCustomerDirectory::create( [
            'name'          => $request->name,
            'cust_id'       => $request->cust_id,
            'description'   => $request->description,
            'parent_dir_id' => $request->parent_dir ] );

        Log::info( sprintf( "DocStore: new directory [%d|%s] created by %s for the customer [%d|%s]", $dir->id, $dir->name, $request->user()->getUsername(), $cust->id, $cust->name ) );

        AlertContainer::push( "New " . config( 'ixp_fe.lang.customer.one' ) . " directory <em>{$request->name}</em> created.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust,'dir' => $dir->id ] ) );
    }

    /**
     * Update a customer directory
     *
     * @param Request $request
     *
     * @param Customer $cust
     * @param DocstoreCustomerDirectory $dir
     * @return RedirectResponse
     *
     * @throws
     */
    public function update( Request $request , Customer $cust, DocstoreCustomerDirectory $dir ): RedirectResponse
    {

        $this->authorize( 'update', [ DocstoreCustomerDirectory::class, $cust, $dir ] );

        $this->checkForm( $request );

        $dir->update( [ 'name' => $request->name, 'description' => $request->description, 'parent_dir_id' => $request->parent_dir ] );

        Log::info( sprintf( "DocStore: customer directory [%d|%s] edited by %s", $dir->id, $dir->name, $request->user()->getUsername() ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " Directory  <em>{$request->name}</em> updated.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $cust, 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Delete a directory
     *
     * @param Request                   $request
     * @param DocstoreCustomerDirectory $dir
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request , DocstoreCustomerDirectory $dir ): RedirectResponse
    {
        $this->authorize( 'delete', $dir );

        Log::notice( sprintf( "DocStore: start recursive deletion directory [%d|%s] by %s for the customer [%d|%s]", $dir->id, $dir->name, $request->user()->getUsername(), $dir->customer->id, $dir->customer->name ) );
        DocstoreCustomerDirectory::recursiveDelete( $dir );
        Log::notice( sprintf( "DocStore: finish recursive deletion of directory [%d|%s] by %s for the customer [%d|%s]", $dir->id, $dir->name, $request->user()->getUsername(), $dir->customer->id, $dir->customer->name ) );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) .  "Directory <em>{$dir->name}</em> deleted.", Alert::SUCCESS );
        return redirect( route( 'docstore-c-dir@list', [ 'cust' => $dir->customer , 'dir' => $dir->parent_dir_id ] ) );
    }

    /**
     * Delete a directory
     *
     * @param Request                   $request
     * @param Customer                  $cust
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function deleteForCustomer( Request $request , Customer $cust ): RedirectResponse
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
     * @param $request
     */
    private function checkForm( $request )
    {
        $request->validate( [
            'name'          => 'required|max:100',
            'cust_id'          => [ 'required', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Customer::whereId( $value )->exists() ) {
                        Log::notice( 'fff' );
                        AlertContainer::push('Customer is invalid / does not exist.', Alert::DANGER );
                        return $fail( 'Customer is invalid / does not exist.' );
                    }
                },
            ],
            'description'   => 'nullable',
            'parent_dir_id' => [ 'nullable', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !DocstoreCustomerDirectory::whereId( $value )->exists() ) {
                        return $fail( 'Parent directory is invalid / does not exist.' );
                    }
                },
            ]
        ] );
    }
}
