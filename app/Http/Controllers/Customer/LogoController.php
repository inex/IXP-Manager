<?php

namespace IXP\Http\Controllers\Customer;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Http\{
    RedirectResponse
};

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
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LogoController extends Controller
{

    /**
     * Load a customer from the database with the given ID (or ID in request)
     *
     * @param int $id The customer ID

     * @return CustomerEntity The customer object
     */
    private function loadCustomer( $id = null ): CustomerEntity
    {
        if( Auth::getUser()->isSuperUser() ) {
            if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
                abort( 404);
            }
        } else {
            $c = Auth::getUser()->getCustomer();
        }

        return $c;
    }


    /**
     * Display the form to add / edit / delete a member's logo
     *
     * @param int $id ID of the customer
     *
     * @return  View
     */
    public function manage( $id = null ) {
        $c = $this->loadCustomer( $id );

        return view( 'customer/logo/manage' )->with([
            'c'                  => $c,
            'logo'               => $c->getLogo( LogoEntity::TYPE_WWW80 ) ?? false
        ]);
    }

    /**
     * Add or edit a customer's logo
     *
     * @param   LogoRequest $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function store( LogoRequest $request ): RedirectResponse {
        $c = $this->loadCustomer( $request->input( 'id' ) );

        if( !$request->hasFile( 'logo' ) ) {
            abort(400);
        }

        $file = $request->file('logo');

        $img = Image::make( $file->getPath().'/'.$file->getFilename() );

        $img->resize(null, 80, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->encode('png');

        $logo = new LogoEntity();

        $logo->setCustomer(         $c );
        $logo->setOriginalName(     $file->getClientOriginalName() );
        $logo->setStoredName(       sha1($img->getEncoded()) . '.png' );
        $logo->setWidth(            $img->width() );
        $logo->setHeight(           $img->height() );
        $logo->setUploadedBy(       Auth::getUser()->getUsername() );
        $logo->setUploadedAt(       new \DateTime );
        $logo->setType(             LogoEntity::TYPE_WWW80 );

        D2EM::persist($logo);

        $saveTo =  $logo->getFullPath();

        if( !is_dir( dirname( $saveTo ) ) ) {
            @mkdir( dirname($saveTo), 0755, true );
        }

        $img->save( $saveTo );

        // remove old logo
        if( $oldLogo = $c->getLogo( LogoEntity::TYPE_WWW80 ) ) {
            // only delete if they do not upload the exact same logo
            if( $oldLogo->getShardedPath() != $logo->getShardedPath() ) {
                if( file_exists( public_path().'/logos/' . $oldLogo->getShardedPath() ) ) {
                    @unlink( public_path().'/logos/' . $oldLogo->getShardedPath() );
                }
            }
            $c->removeLogo( $oldLogo );
            D2EM::remove( $oldLogo );
            D2EM::flush();
        }

        D2EM::flush();

        AlertContainer::push( "Logo successfully uploaded!", Alert::SUCCESS );

        if( !Auth::getUser()->isSuperUser() ) {
            return Redirect::to( route( "dashboard@index" ) );
        }

        return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() ] ) );
    }


    /**
     * Delete a customer's logo
     *
     * @param   int      $id         Id of the customer
     *
     * @return  RedirectResponse
     * @throws
     */
    public function delete( int $id = null ) {
        $c = $this->loadCustomer( $id );

        // do we have a logo?
        if( !( $oldLogo = $c->getLogo( LogoEntity::TYPE_WWW80 ) ) ) {
            AlertContainer::push( "Sorry, we could not find any logo for you.", Alert::DANGER );
            return Redirect::to( '' );
        }

        if( file_exists( $oldLogo->getFullPath() ) ) {
            @unlink( $oldLogo->getFullPath() );
        }

        $c->removeLogo( $oldLogo );
        D2EM::remove( $oldLogo );
        D2EM::flush();

        AlertContainer::push( "Logo successfully removed!", Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }


    /**
     * Display all the customers' logos
     *
     * @return  View
     * @throws
     */
    public function logos(){
        $logos = [];
        foreach( D2EM::getRepository( CustomerEntity::class )->findAll() as $c ) {
            /** @var CustomerEntity $c */
            if( $c->getLogo( LogoEntity::TYPE_WWW80) ) {
                $logos[] = $c->getLogo( LogoEntity::TYPE_WWW80);
            }
        }

        return view( 'customer/logos' )->with([
            'logos' => $logos,
        ]);
    }


}

