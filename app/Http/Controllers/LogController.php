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

use Illuminate\Database\Eloquent\Builder;

use IXP\Models\Log;

use IXP\Models\User;
use IXP\Utils\Http\Controllers\Frontend\EloquentController;

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

class LogController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var Log
     */
    protected $object = null;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string|null
     */
    protected static $route_prefix = "log";

    /**
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams = (object)[
            'model'             => Log::class,
            'pagetitle'         => 'Logs',
            'titleSingular'     => 'Log',
            'nameSingular'      => 'log',
            'listOrderBy'       => 'created_at',
            'listOrderByDir'    => 'DESC',
            'viewFolderName'    => 'log',
            'readonly'          => self::$read_only,
            'listColumns'    => [
                'model'         => 'Model',
                'model_id'        => [
                    'title' => 'UID'
                ],
                'action'        => 'Action',
                'username'       => [
                    'title'      => 'User',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'user',
                    'action'     => 'view',
                    'idField'    => 'user_id'
                ],
                'created_at'       => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns, [
                'message'     => [
                    'title'         => 'Message',
                    'type'          => self::$FE_COL_TYPES[ 'ARRAY' ]
                ],
                'models'      => [
                    'title'         => 'Models',
                    'type'          => self::$FE_COL_TYPES[ 'JSON' ]
                ],
            ]
        );
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int|null $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams   = $this->feParams;
        $model      = request()->model ?? null;
        $model_id   = request()->model_id ?? null;
        $user       = request()->user ?? null;

        return Log::selectRaw( 'log.*, u.username' )
        ->leftJoin( 'user AS u', 'u.id', 'log.user_id' )
        ->when( $id , function( Builder $q, $id ) {
            return $q->where('log.id', $id );
        } )->when( $model, function( Builder $q, $model ) {
            return $q->where('log.model', 'like', $model );
        } )->when( $model_id, function( Builder $q, $model_id ) {
            return $q->where('log.model_id', $model_id );
        } )->when( $user, function( Builder $q, $user ) {
                return $q->where('u.username', $user );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    protected function preList(): void
    {
        $this->data[ 'params' ]     = [
            'model' => request()->model ?? false,
            'user' => request()->user ?? false,
            'models' =>  Log::select( 'model' )->orderBy( 'model' )
                ->distinct()->get()->pluck( 'model' )->toArray(),
            'users'  =>  Log::select( [ 'user_id', 'username' ] )
                ->leftJoin( 'user AS u', 'u.id', 'log.user_id')
                ->orderBy( 'username' )
                ->distinct()->get()->pluck( 'username', 'user_id' )->toArray(),
        ];
    }
}