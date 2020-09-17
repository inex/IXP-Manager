<?php

namespace IXP\Models\Aggregators;

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
use stdClass;

use Illuminate\Database\Eloquent\Builder;

use IXP\Models\ConsoleServerConnection;

/**
 * IXP\Models\Aggregators\ConsoleServerConnectionAggregatore
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $description
 * @property string|null $port
 * @property int|null $speed
 * @property int|null $parity
 * @property int|null $stopbits
 * @property int|null $flowcontrol
 * @property int|null $autobaud
 * @property string|null $notes
 * @property int|null $console_server_id
 * @property-read \IXP\Models\ConsoleServer|null $consoleServer
 * @property-read \IXP\Models\CustomerTag|null $customer
 * @property-read \IXP\Models\Switcher $switcher
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereAutobaud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereConsoleServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereFlowcontrol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereParity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereStopbits($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Aggregators\ConsoleServerConnectionAggregatore whereUpdatedAt($value)
 */
class ConsoleServerConnectionAggregatore extends ConsoleServerConnection
{
    /**
     * Gets a listing of console server connections list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     * @param int|null $port
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null, int $port = null ): array
    {
        return self::select(
            [
                'csc.*',
                'c.name AS customer', 'c.id AS customerid',
                'cs.id  AS consoleserver_id', 'cs.name AS consoleserver_name'
            ]
        )
            ->from( 'consoleserverconnection AS csc' )
            ->leftJoin( 'console_server AS cs', 'cs.id', 'csc.console_server_id')
            ->leftJoin( 'cust AS c', 'c.id', 'csc.custid')
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('csc.id', $id );
            } )
            ->when( $port , function( Builder $q, $port ) {
                return $q->where('csc.console_server_id', $port );
            } )
            ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                foreach( $orderby as $order ) {
                    return $q->orderBy( $order, $feParams->listOrderByDir ?? 'ASC');
                }
            })->get()->toArray();
    }
}