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
};

use IXP\Models\ContactGroup;

/**
 * IXP\Models\Aggregators\ContactGroupAggregator
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property int $active
 * @property int $limited_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 * @method static Builder|ContactGroupAggregator newModelQuery()
 * @method static Builder|ContactGroupAggregator newQuery()
 * @method static Builder|ContactGroupAggregator query()
 * @method static Builder|ContactGroupAggregator whereActive($value)
 * @method static Builder|ContactGroupAggregator whereCreatedAt($value)
 * @method static Builder|ContactGroupAggregator whereDescription($value)
 * @method static Builder|ContactGroupAggregator whereId($value)
 * @method static Builder|ContactGroupAggregator whereLimitedTo($value)
 * @method static Builder|ContactGroupAggregator whereName($value)
 * @method static Builder|ContactGroupAggregator whereType($value)
 * @method static Builder|ContactGroupAggregator whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContactGroupAggregator extends ContactGroup
{

    /**
     * Get contact group names as an array grouped by group type.
     *
     * Returned array structure:
     *
     *     $arr = [
     *         'ROLE' => [
     *              [ 'id' => 1, 'name' => 'Billing' ],
     *              [ 'id' => 2, 'name' => 'Admin']
     *         ]
     *         'OTHER' => [
     *              [ 'id' => n, 'name' => 'Other group' ]
     *         ]
     *     ];
     *
     *
     * @param string|null   $type   Optionally limit to a specific type
     * @param int|null      $cid    Contact id to filter for a particular contact
     * @param bool          $active Filter active
     *
     * @return array
     */
    public static function getGroupNamesTypeArray( string $type = null, int $cid = null, bool $active = false ): array
    {
        $result = self::when( $cid , function( Builder $q, $cid ) {
            return $q->leftJoin( 'contact_to_group', function( $join ) use( $cid ) {
                $join->on( 'contact_group.id', 'contact_to_group.contact_group_id')
                    ->where('contact_to_group.contact_id', $cid );
            });
        })->when( $type , function( Builder $q, $type ) {
            return $q->where( 'type', $type );
        })->when( $active , function( Builder $q, $active ) {
            return $q->where('active', $active );
        } )->get();

        $groups = [];

        foreach( $result as $r ){
            $groups[ $r->type ][ $r->id ] = [ 'id' => $r->id, 'name' => $r->name ];
        }

        return $groups;
    }
}