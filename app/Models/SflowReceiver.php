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
    Model,
    Relations\BelongsTo
};

use IXP\Traits\Observable;

/**
 * IXP\Models\SlowReceiver
 *
 * @property int $id
 * @property int|null $virtual_interface_id
 * @property string $dst_ip
 * @property int $dst_port
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver query()
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver whereDstIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver whereDstPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SflowReceiver whereVirtualInterfaceId($value)
 * @mixin \Eloquent
 */
class SflowReceiver extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sflow_receiver';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'virtual_interface_id',
        'dst_ip',
        'dst_port',
    ];

    /**
     * Get the virtual Interfae that owns the sflow receiver.
     */
    public function virtualInterface(): BelongsTo
    {
        return $this->belongsTo(VirtualInterface::class, 'virtual_interface_id');
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
            "Sflow Receiver [id:%d] '%s' belonging to Vlan [id:%d]",
            $model->id,
            $model->dst_ip,
            $model->virtual_interface_id,
        );
    }
}
