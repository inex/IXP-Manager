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
 * IXP\Models\PatchPanelPortHistory
 *
 * @property int $id
 * @property int|null $patch_panel_port_id
 * @property int $state
 * @property string|null $notes
 * @property string|null $assigned_at
 * @property string|null $connected_at
 * @property string|null $cease_requested_at
 * @property string|null $ceased_at
 * @property int $internal_use
 * @property int $chargeable
 * @property string|null $customer
 * @property string|null $switchport
 * @property int|null $duplex_master_id
 * @property int $number
 * @property string|null $colo_circuit_ref
 * @property string|null $ticket_ref
 * @property string|null $private_notes
 * @property int $owned_by
 * @property string|null $description
 * @property string|null $colo_billing_ref
 * @property int|null $cust_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPortHistoryFile[] $patchPanelPortHistoryFiles
 * @property-read int|null $patch_panel_port_history_files_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereCeaseRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereCeasedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereChargeable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereColoBillingRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereColoCircuitRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereConnectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereCustId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereDuplexMasterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereInternalUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereOwnedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory wherePatchPanelPortId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereSwitchport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortHistory whereTicketRef($value)
 * @mixin \Eloquent
 */

class PatchPanelPortHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port_history';

    /**
     * Get the Patch Panel that owns this patch panel port
     */
    public function patchPanelPort(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne( PatchPanelPort::class , 'id' );
    }

    /**
     * Get the patch panel port history files for this patch panel port history
     */
    public function patchPanelPortHistoryFiles(): HasMany
    {
        return $this->hasMany(PatchPanelPortHistoryFile::class, 'patch_panel_port_history_id' );
    }
}
