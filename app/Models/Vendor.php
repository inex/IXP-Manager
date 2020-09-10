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

/**
 * IXP\Models\Vendor
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property string|null $nagios_name
 * @property string|null $bundle_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServer[] $consoleServers
 * @property-read int|null $console_servers_count
 * @method static Builder|Vendor newModelQuery()
 * @method static Builder|Vendor newQuery()
 * @method static Builder|Vendor query()
 * @method static Builder|Vendor whereBundleName($value)
 * @method static Builder|Vendor whereId($value)
 * @method static Builder|Vendor whereNagiosName($value)
 * @method static Builder|Vendor whereName($value)
 * @method static Builder|Vendor whereShortname($value)
 * @mixin \Eloquent
 */
class Vendor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vendor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'shortname',
        'bundle_name',
    ];

    /**
     * Get the console servers for the vendor
     */
    public function consoleServers(): HasMany
    {
        return $this->hasMany(ConsoleServer::class, 'vendor_id' );
    }
}
