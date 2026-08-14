<?php

namespace IXP\Models;

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
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\Location
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property string|null $tag
 * @property string|null $address
 * @property string|null $nocphone
 * @property string|null $nocfax
 * @property string|null $nocemail
 * @property string|null $officephone
 * @property string|null $officefax
 * @property string|null $officeemail
 * @property string|null $notes
 * @property int|null $pdb_facility_id
 * @property string|null $city
 * @property string|null $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \IXP\Models\Cabinet> $cabinets
 * @property-read int|null $cabinets_count
 * @method static Builder<static>|Location newModelQuery()
 * @method static Builder<static>|Location newQuery()
 * @method static Builder<static>|Location query()
 * @method static Builder<static>|Location whereAddress($value)
 * @method static Builder<static>|Location whereCity($value)
 * @method static Builder<static>|Location whereCountry($value)
 * @method static Builder<static>|Location whereCreatedAt($value)
 * @method static Builder<static>|Location whereId($value)
 * @method static Builder<static>|Location whereName($value)
 * @method static Builder<static>|Location whereNocemail($value)
 * @method static Builder<static>|Location whereNocfax($value)
 * @method static Builder<static>|Location whereNocphone($value)
 * @method static Builder<static>|Location whereNotes($value)
 * @method static Builder<static>|Location whereOfficeemail($value)
 * @method static Builder<static>|Location whereOfficefax($value)
 * @method static Builder<static>|Location whereOfficephone($value)
 * @method static Builder<static>|Location wherePdbFacilityId($value)
 * @method static Builder<static>|Location whereShortname($value)
 * @method static Builder<static>|Location whereTag($value)
 * @method static Builder<static>|Location whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Location extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'location';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'shortname',
        'tag',
        'address',
        'nocphone',
        'nocfax',
        'nocemail',
        'officephone',
        'officefax',
        'officeemail',
        'notes',
        'pdb_facility_id',
        'city',
        'country',
    ];

    /**
     * Get the switchers for the cabinet
     *
     * @return HasMany<Cabinet, Location>
     */
    public function cabinets(): HasMany
    {
        return $this->hasMany(Cabinet::class, 'locationid' );
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Facility (Location) [id:%d] '%s'",
            $model->id,
            $model->name,
        );
    }
}