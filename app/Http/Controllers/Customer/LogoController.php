<?php

namespace IXP\Http\Controllers\Customer;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, D2EM, Redirect;

use Intervention\Image\ImageManagerStatic as Image;

use IXP\Http\Controllers\Controller;

use IXP\Models\Customer;
use IXP\Models\Logo;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};

use Illuminate\View\View;


use Entities\{
    Customer                as CustomerEntity,
    Logo                    as LogoEntity
};

use IXP\Http\Requests\Customer\{
    Logo                    as LogoRequest
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Logo Controller
 *
 * Routes to access this controller are dependent on the controller being enabled.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customers
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LogoController extends Controller
{
    /**
     * Load a customer from the database with the given ID (or ID in request)
     *
     * @param int|null $id The customer ID
     * @return Customer The customer object
     */
    private function loadCustomer( ?int $id ): Customer
    {
        if( Auth::getUser()->isSuperUser() ) {
            return Customer::findOrFail( $id );
        }

        return Customer::find( Auth::getUser()->getCustomer()->getId() );
    }


    /**
     * Display the form to add / edit / delete a member's logo
     *
     * @param int|null $id ID of the customer
     *
     * @return  View
     */
    public function manage( ?int $id ): View
    {
        $c = $this->loadCustomer( $id );

        return view( 'customer/logo/manage' )->with( [
            'c'                  => $c,
            'logo'               => $c->logos()->exists() ? $c->logos()->first()  : false
        ] );
    }

    /**
     * Add or edit a customer's logo
     *
     * @param   LogoRequest $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function store( LogoRequest $request ): RedirectResponse
    {
        $c = $this->loadCustomer( $request->input( 'id' ) );

        if( !$request->hasFile( 'logo' ) ) {
            abort(400);
        }

        $file = $request->file('logo');
        $img = Image::make( $file->getPath().'/'.$file->getFilename() );

        $img->resize(null, 80, function ( $constraint ) {
            $constraint->aspectRatio();
        });
        $img->encode('png');

        // remove old logo
        if( $c->logos()->exists() ) {
            $oldLogo = $c->logos()->first();
            if( file_exists( public_path() . '/logos/' . $oldLogo->getShardedPath() ) ) {
                @unlink( public_path() . '/logos/' . $oldLogo->getShardedPath() );
            }
            $oldLogo->delete();
        }

        $logo = Logo::create( [
            'type'          => Logo::TYPE_WWW80,
            'original_name' => $file->getClientOriginalName(),
            'stored_name'   => sha1( $img->getEncoded() ) . '.png',
            'uploaded_by'   => Auth::getUser()->getUsername(),
            'width'         => $img->width(),
            'height'        => $img->height(),
        ] );

        $logo->customer_id = $c->id;
        $logo->save();

        $saveTo =  $logo->getFullPath();

        if( !is_dir( dirname( $saveTo ) ) ) {
            @mkdir( dirname( $saveTo ), 0755, true );
        }

        $img->save( $saveTo );

        AlertContainer::push( "Logo successfully uploaded!", Alert::SUCCESS );

        return Redirect::to( Auth::getUser()->isSuperUser() ? route( "customer@overview" , [ "id" => $c->id ] ) : Redirect::to( route( "dashboard@index" ) ) );
    }


    /**
     * Delete a customer's logo
     *
     * @param Request $request
     * @param int $id
     *
     * @return  RedirectResponse
     *
     * @throws \Exception
     */
    public function delete( Request $request, int $id ) : RedirectResponse
    {
        $c = $this->loadCustomer( $id );

        // do we have a logo?
        if( $c->logos()->doesntExist() ) {
            AlertContainer::push( "Sorry, we could not find any logo for you.", Alert::DANGER );
            return Redirect::to( '' );
        }

        $oldLogo = $c->logos()->first();

        if( file_exists( $oldLogo->getFullPath() ) ) {
            @unlink( $oldLogo->getFullPath() );
        }

        $oldLogo->delete();

        AlertContainer::push( "Logo successfully removed!", Alert::SUCCESS );

        return redirect( Auth::user()->isSuperUser() ? route( 'customer@overview', [ 'id' => $c->id ] ) : route( 'dashboard@index' ) );
    }


    /**
     * Display all the customers' logos
     *
     * @return  View
     *
     * @throws
     */
    public function logos()
    {
        return view( 'customer/logos' )->with([
            'customers' => Customer::with( 'logos' )->has( 'logos' )->orderBy( 'name' )->get(),
        ]);
    }
}