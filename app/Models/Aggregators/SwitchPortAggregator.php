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
use Illuminate\Database\Eloquent\{
    Builder,
    Model,
};


/**
 * IXP\Models\Aggregators\SwitchPortAggregator
 *
 * @method static Builder|SwitchPortAggregator newModelQuery()
 * @method static Builder|SwitchPortAggregator newQuery()
 * @method static Builder|SwitchPortAggregator query()
 * @mixin \Eloquent
 */
class SwitchPortAggregator extends Model
{
    /**
     * Returns all available switch ports for a switch.
     *
     * Restrict to only some types of switch port
     * Exclude switch port ids from the list
     *
     * Suitable for other generic use.
     *
     * @param int      $switchid        Switch ID - switch to query
     * @param array    $types           Switch port type restrict to some types only
     * @param array    $excludedSpid    Switch port IDs, if set, those ports are excluded from the results

     * @return array
     */
    public static function getAllPortsForSwitch( int $switchid, $types = [], $excludedSpid = [], bool $notAssignToPI = true ): array
    {
        return self::select( [
            'sp.id AS id', 'sp.name AS name', 'sp.type AS porttype'
        ] )
            ->from( 'switchport AS sp' )
            ->when( $notAssignToPI , function( Builder $q ) {
                return $q->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
                    ->where( 'pi.id', NULL);
            })
            ->when( count( $types ) > 0 , function( Builder $q ) use( $types ) {
                return $q->whereIn( 'sp.type', $types );
            })
            ->when( count( $excludedSpid ) > 0 , function( Builder $q ) use( $excludedSpid ) {
                return $q->whereNotIn( 'sp.id', $excludedSpid );
            })
            ->where( 'sp.switchid', $switchid )
            ->orderBy( 'id', 'ASC' )
            ->get()->keyBy( 'id' )->toArray();
    }


}