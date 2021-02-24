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


use Entities\User as UserEntity;

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
};

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


/**
 * IXP\Models\PatchPanelPortHistoryFile
 *
 * @property int $id
 * @property int|null $patch_panel_port_history_id
 * @property string $name
 * @property string $type
 * @property string $uploaded_at
 * @property string $uploaded_by
 * @property int $size
 * @property int $is_private
 * @property string $storage_location
 * @property-read \IXP\Models\PatchPanelPortHistory|null $patchPanelPortHistory
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile wherePatchPanelPortHistoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereStorageLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereUploadedBy($value)
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
     * Get the Patch Panel Port history that owns this patch panel port history file
     */
    public function patchPanelPortHistory(): BelongsTo
    {
        return $this->belongsTo( PatchPanelPortHistory::class , 'patch_panel_port_history_id' );
    }

    /**
     * Gets a listing of patch panel port files history for a customer
     */
    public static function getForCustomer( Customer $cust, bool $includePrivate = true ): Collection
    {
        $q = self::join(     'patch_panel_port_history as ppph' ,     'patch_panel_port_history_file.patch_panel_port_history_id' , 'ppph.id'  )
            ->where('ppph.cust_id', $cust->id );

        if( !$includePrivate ) {
            $q->where( 'ppphf.is_private', '0' );
        }

        return $q->get();
    }
}
