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
    Model
};

use Illuminate\Database\Eloquent\Relations\{
    HasMany
};

use Illuminate\Support\Carbon;

/**
 * IXP\Models\PatchPanel
 *
 * @property int $id
 * @property int|null $cabinet_id
 * @property string $name
 * @property string $colo_reference
 * @property int $cable_type
 * @property int $connector_type
 * @property string|null $installation_date
 * @property string $port_prefix
 * @property int $active
 * @property int $chargeable
 * @property string $location_notes
 * @property int|null $u_position
 * @property int|null $mounted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPort[] $patchPanelPorts
 * @property-read int|null $patch_panel_ports_count
 * @method static Builder|PatchPanel newModelQuery()
 * @method static Builder|PatchPanel newQuery()
 * @method static Builder|PatchPanel query()
 * @method static Builder|PatchPanel whereActive($value)
 * @method static Builder|PatchPanel whereCabinetId($value)
 * @method static Builder|PatchPanel whereCableType($value)
 * @method static Builder|PatchPanel whereChargeable($value)
 * @method static Builder|PatchPanel whereColoReference($value)
 * @method static Builder|PatchPanel whereConnectorType($value)
 * @method static Builder|PatchPanel whereId($value)
 * @method static Builder|PatchPanel whereInstallationDate($value)
 * @method static Builder|PatchPanel whereLocationNotes($value)
 * @method static Builder|PatchPanel whereMountedAt($value)
 * @method static Builder|PatchPanel whereName($value)
 * @method static Builder|PatchPanel wherePortPrefix($value)
 * @method static Builder|PatchPanel whereUPosition($value)
 * @mixin Eloquent
 */

class PatchPanel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel';

    /**
     * Get the patch panel port files for this patch panel port
     */
    public function patchPanelPorts(): HasMany
    {
        return $this->hasMany(PatchPanelPort::class, 'patch_panel_id' );
    }
}
