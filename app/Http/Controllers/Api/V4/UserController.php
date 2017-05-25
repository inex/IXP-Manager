<?php

namespace IXP\Http\Controllers\Api\V4;

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

use D2EM;
use Entities\User as UserEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View as FacadeView;
use Repositories\User;


/**
 * User API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends Controller {

    /**
     * API call to get users as JSON
     *
     * @param int $priv Optionally limit to users of given privilege
     * @return Response
     */
    public function json( Request $request, int $priv = null ) {
        return response()->json( D2EM::getRepository( UserEntity::class )->asArray( $priv ) );
    }

    //    /**
    //     * API call to generate user records in a given format
    //     *
    //     * @param int $priv Optionally limit to users of given privilege
    //     * @return Response
    //     */
    //    public function templated( Request $request, string $template, int $priv = null )
    //    {
    //        $tmpl = sprintf('api/v4/user/%s', preg_replace('/[^a-z0-9\-]/', '', strtolower( $template ) ) );
    //
    //        if( !FacadeView::exists( $tmpl ) ) {
    //            abort(404, 'Unknown template');
    //        }
    //
    //        $users = $priv ? D2EM::getRepository( UserEntity::class )->findBy( [ 'privs' => $priv ] )
    //                        : D2EM::getRepository( UserEntity::class )->findAll();
    //
    //        return response()
    //                ->view( $tmpl, [ 'users' => $users ], 200 )
    //                ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    //    }

}
