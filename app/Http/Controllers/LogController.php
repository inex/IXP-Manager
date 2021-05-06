<?php

namespace IXP\Http\Controllers;

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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use IXP\Models\Log;
use Illuminate\View\View;

/**
 * Log Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class LogController extends Controller
{
    /**
     * Search in logs list
     *
     * @param Request   $r
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function search( Request $r )
    {
        return $this->list( $r );
    }
    /**
     * Display the Logs list
     *
     * @param Request   $r
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function list( Request $r )
    {
        $model      = $r->model ?? null;
        $model_id   = $r->model_id ?? null;
        $user       = $r->user ?? null;
        $action     = $r->action ?? null;
        $created_at = $r->created_at ?? null;

        return view( 'log/index' )->with([
            'model'     => $r->model ?? false,
            'user'      => $r->user ?? false,
            'models'    =>  Log::select( 'model' )->orderBy( 'model' )
                ->distinct()->get()->toArray(),
            'users'     =>  Log::select( [ 'user_id', 'username' ] )
                ->leftJoin( 'user AS u', 'u.id', 'log.user_id')
                ->orderBy( 'username' )
                ->distinct()->get()->toArray(),
            'logs'      => Log::selectRaw( 'log.*, u.username' )
                ->leftJoin( 'user AS u', 'u.id', 'log.user_id' )
                ->when( $model, function( Builder $q, $model ) {
                    return $q->where('log.model', 'like', $model );
                } )->when( $model_id, function( Builder $q, $model_id ) {
                    return $q->where('log.model_id', $model_id );
                } )->when( $user, function( Builder $q, $user ) {
                    return $q->where('u.username', $user );
                } )->when( $action, function( Builder $q, $action ) {
                    return $q->where('log.action', $action );
                } )->when( $created_at, function( Builder $q, $created_at ) {
                    return $q->where('log.created_at', 'like', $created_at . '%' );
                } )->orderByDesc( 'created_at' )->paginate( 20 )
        ]);
    }

    /**
     * Display the log details
     *
     * @param   Log $log the log
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function view( Log $log )
    {
        return view( 'log/view' )->with([
            'log'   => $log
        ]);
    }
}