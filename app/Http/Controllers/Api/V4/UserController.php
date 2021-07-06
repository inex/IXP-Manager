<?php

namespace IXP\Http\Controllers\Api\V4;

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

use Illuminate\Http\{
    JsonResponse,
    Request,
    Response
};

use Illuminate\Support\Facades\View as FacadeView;

use IXP\Models\User;

/**
 * User API Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends Controller
{
    /**
     * API call to get users as JSON
     *
     * @param int|null $priv Optionally limit to users of given privilege
     *
     * @return JsonResponse
     */
    public function json( int $priv = null ): JsonResponse
    {
        return response()->json(
            User::byPrivs( $priv )->get()->toArray()
        );
    }

    /**
     * API call to get users formatted
     *
     * @param  Request      $r
     * @param  int|null     $priv       Optionally limit to users of given privilege
     * @param  string|null  $template
     *
     * @return Response
     */
    public function formatted( Request $r, int $priv = null, string $template = null ): Response
    {
        if( $template === null && !$r->template ) {
            $tmpl = 'api/v4/user/formatted/default';
        } else {
            if( $template === null ) {
                $template = $r->template;
            }
            $tmpl = sprintf( 'api/v4/user/formatted/%s', preg_replace( '/[^a-z0-9\-]/', '', strtolower( $template ) ) );
        }

        if( !FacadeView::exists( $tmpl ) ) {
            abort(404, 'Unknown template');
        }

        if( $priv === null && $r->priv ) {
            $priv = $r->priv;
        }

        if( $r->users ) {
            $users = User::activeOnly()->whereIn( 'username', explode(',', $r->users ) )->get();
        } else if( $priv !== null ) {
            $users = User::byPrivs( $priv )->activeOnly()->get();
        } else {
            $users = User::activeOnly()->get();
        }

        return response()
            ->view( $tmpl, [
                    'users'     => $users,
                    'reqUsers'  => $r->users ? explode(',', $r->users ) : [],
                    'priv'      => $priv ?? '',
                    'bcrypt'    => $r->input( 'bcrypt', '2y' ),
                    'group'     => $r->input( 'group',  'admin' ),
                ], 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }
}