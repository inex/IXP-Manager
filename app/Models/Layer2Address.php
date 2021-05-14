<?php

namespace IXP\Models;

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

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

use IXP\Traits\Observable;

/**
 * IXP\Models\Layer2Address
 *
 * @property int $id
 * @property int $vlan_interface_id
 * @property string|null $mac
 * @property string|null $firstseen
 * @property string|null $lastseen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\VlanInterface $vlanInterface
 * @method static Builder|Layer2Address newModelQuery()
 * @method static Builder|Layer2Address newQuery()
 * @method static Builder|Layer2Address query()
 * @method static Builder|Layer2Address whereCreatedAt($value)
 * @method static Builder|Layer2Address whereFirstseen($value)
 * @method static Builder|Layer2Address whereId($value)
 * @method static Builder|Layer2Address whereLastseen($value)
 * @method static Builder|Layer2Address whereMac($value)
 * @method static Builder|Layer2Address whereUpdatedAt($value)
 * @method static Builder|Layer2Address whereVlanInterfaceId($value)
 * @mixin \Eloquent
 */
class Layer2Address extends Model
{
    use Observable;

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
     * Get mac formatted depending on the format selected
     *  - with comma (xx:xx:xx:xx:xx:xx)
     *  - with dots (xxxx.xxxx.xxxx)
     *  - with dash (xx-xx-xx-xx-xx-xx)
     *
     * @param string $format
     *
     * @return null|string
     */
    public function macFormatted( string $format ): ?string
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
            "Layer2Address [id:%d] '%s' belonging to VlanInterface [id:%d]",
            $model->id,
            $model->macFormatted( ':' ),
            $model->vlan_interface_id,
        );
    }
}