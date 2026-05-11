<?php

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $asn
 * @property string $name
 * @property string $class
 * @property string $country_code
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereName($value)
 * @mixin \Eloquent
 */
class Asn extends Model
{
    /**
     * ASN's are globally unique and we don't need any other identifier for them
     * @var string
     */
    protected $primaryKey = 'asn';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asn',
        'name',
        'class',
        'country_code',
    ];

    public $timestamps = false;
}