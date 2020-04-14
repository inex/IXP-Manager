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

use DB, Eloquent;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};

use Entities\User as UserEntity;

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

use Illuminate\Support\Carbon;
use Storage;


/**
 * IXP\Models\PatchPanelPortHistoryFile
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
 * @method static Builder|PatchPanelPortHistoryFile newModelQuery()
 * @method static Builder|PatchPanelPortHistoryFile newQuery()
 * @method static Builder|PatchPanelPortHistoryFile query()
 * @method static Builder|PatchPanelPortHistoryFile whereId($value)
 * @method static Builder|PatchPanelPortHistoryFile wherePatchPanelPortId($value)
 * @method static Builder|PatchPanelPortHistoryFile whereName($value)
 * @method static Builder|PatchPanelPortHistoryFile whereType($value)
 * @method static Builder|PatchPanelPortHistoryFile whereUploadedAt($value)
 * @method static Builder|PatchPanelPortHistoryFile whereUploadedBy($value)
 * @method static Builder|PatchPanelPortHistoryFile whereSize($value)
 * @method static Builder|PatchPanelPortHistoryFile whereIsPrivate($value)
 * @method static Builder|PatchPanelPortHistoryFile whereStorageLocation($value)
 * @mixin Eloquent
 */

class PatchPanelPortHistoryFile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port_history_file';

    /**
     * Gets a listing of patch panel port files history for a customer
     *
     * @param Customer      $cust
     * @param UserEntity    $user
     *
     * @return Collection
     */
    public static function getForCustomer( Customer $cust, UserEntity $user ): Collection
    {
        $q = self::join(     'patch_panel_port_history as ppph' ,     'patch_panel_port_history_file.patch_panel_port_history_id' , 'ppph.id'  )
            ->where('ppph.cust_id', $cust->id );

        if( !$user->isSuperUser() ) {
            $q->where( 'ppphf.is_private', '0' );
        }

        return $q->get();
    }
}
