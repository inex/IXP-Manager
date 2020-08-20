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

use Eloquent;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo
};

use Entities\User as UserEntity;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


/**
 * IXP\Models\PatchPanelPortFile
 *
 * @property int $id
 * @property int|null $patch_panel_port_id
 * @property string $name
 * @property string $type
 * @property string $uploaded_at
 * @property string $uploaded_by
 * @property int $size
 * @property int $is_private
 * @property string $storage_location
 * @property-read \IXP\Models\PatchPanelPort|null $patchPanelPort
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile wherePatchPanelPortId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereStorageLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereUploadedBy($value)
 * @mixin Eloquent
 */

class PatchPanelPortFile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port_file';

    /**
     * Get the Patch Panel Port that owns this patch panel port file
     */
    public function patchPanelPort(): BelongsTo
    {
        return $this->belongsTo( PatchPanelPort::class , 'patch_panel_port_id' );
    }

    /**
     * Gets a listing of patch panel port files for a customer
     */
    public static function getForCustomer( Customer $c, bool $includePrivate = true ): Collection
    {
        $q = self::join(     'patch_panel_port as ppp' ,          'patch_panel_port_file.patch_panel_port_id',        'ppp.id' )
            ->where('ppp.customer_id', $c->id );

        if( !$includePrivate ) {
            $q->where( 'patch_panel_port_file.is_private', '0' );
        }

        return $q->get();
    }
}
