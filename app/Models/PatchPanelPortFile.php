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

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

use Entities\User as UserEntity;

use Illuminate\Support\Carbon;


/**
 * IXP\Models\PatchPanelPortFile
 *
 * @property int $id
 * @property int|null $patch_panel_port_id
 * @property string $name
 * @property string $type
 * @property Carbon $uploaded_at
 * @property string $uploaded_by
 * @property integer $size
 * @property boolean $is_private
 * @property string $storage_location
 * @method static Builder|PatchPanelPortFile newModelQuery()
 * @method static Builder|PatchPanelPortFile newQuery()
 * @method static Builder|PatchPanelPortFile query()
 * @method static Builder|PatchPanelPortFile whereId($value)
 * @method static Builder|PatchPanelPortFile wherePatchPanelPortId($value)
 * @method static Builder|PatchPanelPortFile whereName($value)
 * @method static Builder|PatchPanelPortFile whereType($value)
 * @method static Builder|PatchPanelPortFile whereUploadedAt($value)
 * @method static Builder|PatchPanelPortFile whereUploadedBy($value)
 * @method static Builder|PatchPanelPortFile whereSize($value)
 * @method static Builder|PatchPanelPortFile whereIsPrivate($value)
 * @method static Builder|PatchPanelPortFile whereStorageLocation($value)
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
     * Gets a listing of patch panel port files for a customer
     *
     * @param Customer      $cust
     * @param UserEntity    $user
     *
     * @return Collection
     */
    public static function getForCustomer( Customer $cust, UserEntity $user ): Collection
    {
        $q = self::join(     'patch_panel_port as ppp' ,          'patch_panel_port_file.patch_panel_port_id',        'ppp.id' )
            ->where('ppp.customer_id', $cust->id );

        if( !$user->isSuperUser() ) {
            $q->where( 'patch_panel_port_file.is_private', '0' );
        }

        return $q->get();
    }
}
