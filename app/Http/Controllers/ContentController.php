<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM;

use Entities\{
    Customer as CustomerEntity,
    User     as UserEntity
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View as FacadeView;

use Illuminate\View\View;

/**
 * Content Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContentController extends Controller {

    /**
     * Display the appropriate static content page (if permissions match)
     *
     * @param  int    $priv Required privilege for access to the content
     * @param  string $page Page to display
     * @return  View
     */
    public function index( int $priv, string $page ): View {

        // check privilege:
        if( $priv != UserEntity::AUTH_PUBLIC ) {
            if( Auth::guest() || Auth::user()->getPrivs() < $priv ) {
                abort( 403, 'Unauthorized' );
            }
        }

        // sanitise page name:
        $page = "content/{$priv}/" . preg_replace( '/[^a-z0-9\-_]/', '', strtolower( $page ) );

        if( !FacadeView::exists( $page ) ) {
            abort( 404, 'Requested page not found' );
        }


        return view( $page );
    }

    /**
     * Alias for public only content
     *
     * @param  string $page Page to display
     * @return  View
     */
    public function public( string $page ): View {
        return $this->index( 0, $page );
    }

    /**
     * Display the appropriate member details page (if permissions match)
     *
     * @param  int    $priv Required privilege for access to the content
     * @param  string $page Page to display
     * @return View|Response|JsonResponse
     */
    public function members( int $priv, string $page )
    {
        // check privilege:
        if( $priv != UserEntity::AUTH_PUBLIC ) {
            if( Auth::guest() || Auth::user()->getPrivs() < $priv ) {
                abort( 403, 'Unauthorized' );
            }
        }

        // check format
        $page = strtolower( $page );
        if( strpos( $page, '.' ) === false ) {
            $page .= '.html';
        }

        list( $page, $format ) = explode( '.', $page );

        // sanitise page name:
        $page = "content/members/{$priv}/" . preg_replace( '/[^a-z0-9\-_]/', '', $page );

        if( !FacadeView::exists( $page ) ) {
            abort( 404, 'Requested page not found' );
        }

        $r = response()->view( $page, [ 'customers' =>D2EM::getRepository( CustomerEntity::class )->getCurrentActive() ], 200 );

        if( $format == 'json' ) {
            $r->header( 'Content-Type', 'application/json' );
        }

        return $r;
    }

}