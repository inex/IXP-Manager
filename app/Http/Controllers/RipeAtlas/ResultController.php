<?php

namespace IXP\Http\Controllers\RipeAtlas;

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
use IXP\Models\AtlasResult;

use IXP\Utils\Http\Controllers\Frontend\EloquentController as Eloquent2Frontend;

/**
 * Result Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ResultController extends Eloquent2Frontend
{
    /**
     * The object being created / edited
     *
     * @var AtlasResult
     */
    protected $object = null;

    /**
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = true;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string
     */
    protected static $route_prefix = "ripe-atlas/results";

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'model'             => AtlasResult::class,
            'pagetitle'         => 'Ripe Atlas :: Results',
            'titleSingular'     => 'Result',
            'nameSingular'      => 'an atlas result',
            'listOrderBy'       => 'id',
            'listOrderByDir'    => 'DESC',
            'viewFolderName'    => 'atlas-result',
            'readonly'          => self::$read_only,
            'listColumns'       => [
                'routing'   => 'Routing',
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'path'      => 'Path'
            ]
        );
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( $id = null ): array
    {
        $feParams = $this->feParams;
        return AtlasResult::when( $id, function( Builder $q, $id ) {
            return $q->where( 'atlas_results.id', $id );
        } )->when( $feParams->listOrderBy , function( Builder $q ) use( $feParams ) {
            return $q->orderBy( $feParams->listOrderBy, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }
}