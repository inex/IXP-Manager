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
    Relations\BelongsTo
};

/**
 * IXP\Models\Layer2Address
 *
 * @property int $id
 * @property int $vlan_interface_id
 * @property string|null $mac
 * @property string|null $firstseen
 * @property string|null $lastseen
 * @property string|null $created
 * @property-read \IXP\Models\VlanInterface $vlanInterface
 * @method static Builder|Layer2Address newModelQuery()
 * @method static Builder|Layer2Address newQuery()
 * @method static Builder|Layer2Address query()
 * @method static Builder|Layer2Address whereCreated($value)
 * @method static Builder|Layer2Address whereFirstseen($value)
 * @method static Builder|Layer2Address whereId($value)
 * @method static Builder|Layer2Address whereLastseen($value)
 * @method static Builder|Layer2Address whereMac($value)
 * @method static Builder|Layer2Address whereVlanInterfaceId($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Layer2Address whereUpdatedAt($value)
 */
class Layer2Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'l2address';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vlan_interface_id',
        'mac',
        'firstseen',
        'lastseen',
    ];

    /**
     * Get the vlan interface that holds the layer2address
     */
    public function vlanInterface(): BelongsTo
    {
        return $this->belongsTo(VlanInterface::class, 'vlan_interface_id');
    }

    /**
     * Get mac formated depending on the format selected
     *  - with comma (xx:xx:xx:xx:xx:xx)
     *  - with dots (xxxx.xxxx.xxxx)
     *  - with dash (xx-xx-xx-xx-xx-xx)
     *
     * @param string $format
     *
     * @return string
     */
    public function macFormatted( string $format ): string
    {
        switch( $format ) {
            case ':':
                return wordwrap( $this->mac, 2, ':',true );
                break;
            case '.':
                return wordwrap( $this->mac, 4, '.',true );
                break;
            case '-':
                return wordwrap($this->mac, 2, '-',true);
                break;
            default:
                return $this->mac;
        }
    }
}