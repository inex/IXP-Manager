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
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{
    AtlasProbe,
    Customer,
    Router
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController as Eloquent2Frontend;

/**
 * Probes Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ProbesController extends Eloquent2Frontend
{
    /**
     * The object being created / edited
     *
     * @var AtlasProbe
     */
    protected $object = null;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string
     */
    protected static $route_prefix = "ripe-atlas/probes";

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {
        $this->feParams         = (object)[
            'model'             => AtlasProbe::class,
            'pagetitle'         => 'Ripe Atlas :: Probes',
            'titleSingular'     => 'Probe',
            'nameSingular'      => 'a probe',
            'listOrderBy'       => 'updated_at',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'atlas-probes',
            'readonly'       => 'true',
            'listColumns'       => [
                'name'  => [
                    'title'      => 'Customer',
                    'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                    'controller' => 'customer',
                    'action'     => 'overview',
                    'idField'    => 'cust_id'
                ],
                'address_v4' => [
                    'title'    => 'IPv4',
                    'type'     => self::$FE_COL_TYPES[ 'WHO_IS_PREFIX' ]
                ],
                'address_v6' => 'IPv6',
                'v4_enabled'       => [
                    'title'    => 'IPv4 Enabled',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'v6_enabled'       => [
                    'title'    => 'IPv6 Enabled',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'last_connected'    =>  [
                    'title'      => 'Last Connected',
                    'type'       => self::$FE_COL_TYPES[ 'DATETIME' ],
                ],
                'updated_at'        =>  [
                    'title'      => 'Updated at',
                    'type'       => self::$FE_COL_TYPES[ 'DATETIME' ],
                ],
            ]
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'asn'           => 'ASN',
                'atlas_id'      => [
                    'title'         => 'Atlas ID',
                    'type'          => self::$FE_COL_TYPES[ 'JSON' ],
                    'displayAs'     => 'text',
                    'valueFrom'     => 'url',
                    'value'         => url( '/api/v4/ripe-atlas/probe/' ) . '/%%COL%%/info',
                ],
                'is_anchor'       => [
                    'title'    => 'Is Anchor',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'is_public'       => [
                    'title'    => 'Is Public',
                    'type'     => self::$FE_COL_TYPES[ 'YES_NO' ]
                ],
                'api_data'       => [
                    'title'         => 'API Data',
                    'type'          => self::$FE_COL_TYPES[ 'JSON' ],
                    'displayAs'     => 'btn',
                    'valueFrom'     => 'DB',
                ],
            ]
        );
    }

    /**
     * List the contents of a database table.
     *
     * @param Request $r
     *
     * @return View
     */
    public function list( Request $r  ): View
    {
        $cid = $protocol = false;

        if( $cust = Customer::find( $r->cust ) ) {
            $cid = $cust->id;
        }

        if( array_key_exists( $r->protocol, Router::$PROTOCOLS ) ) {
            $protocol = $r->protocol;
        }

        $this->data[ 'params' ][ 'cid' ]            = $cid;
        $this->data[ 'params' ][ 'protocol' ]       = $protocol;

        $this->data[ 'rows' ] = $this->listGetData();

        $this->listIncludeTemplates();

        $this->preList();

        return $this->display( 'list' );
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
        $feParams   = $this->feParams;
        $cid        = $this->data[ 'params' ][ 'cid' ]        ?? null;
        $protocol   = $this->data[ 'params' ][ 'protocol' ]   ?? null;

        return AtlasProbe::select( [
            'atlas_probes.*',
            'cust.name'
        ] )->leftJoin( 'cust', 'atlas_probes.cust_id', 'cust.id' )
        ->when( $id, function( Builder $q, $id ) {
            return $q->where( 'atlas_probes.id', $id );
        } )->when( $cid , function( Builder $q, $cid ) {
            return $q->where('atlas_probes.cust_id', $cid );
        })->when( $protocol , function( Builder $q, $protocol ) {
            return $q->where('atlas_probes.v' . $protocol . "_enabled", $protocol, true );
        })->when( $feParams->listOrderBy , function( Builder $q ) use( $feParams ) {
            return $q->orderBy( $feParams->listOrderBy, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }
}