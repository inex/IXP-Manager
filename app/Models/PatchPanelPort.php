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
 * IXP\Models\PatchPanelPort
 *
 * @property int $id
 * @property int|null $switch_port_id
 * @property int|null $patch_panel_id
 * @property int|null $customer_id
 * @property int $state
 * @property string $notes
 * @property Carbon $assigned_at
 * @property Carbon $connected_at
 * @property Carbon $cease_requested_at
 * @property Carbon $ceased_at
 * @property Carbon $last_state_change
 * @property boolean $internal_use
 * @property int $chargeable
 * @property int $duplex_master_id
 * @property boolean $number
 * @property string $colo_circuit_ref
 * @property string $ticket_ref
 * @property string $private_notes
 * @property int $owned_by
 * @property string $loa_code
 * @property string $description
 * @property string $colo_billing_ref
 * @method static Builder|PatchPanelPort newModelQuery()
 * @method static Builder|PatchPanelPort newQuery()
 * @method static Builder|PatchPanelPort query()
 * @method static Builder|PatchPanelPort whereId($value)
 * @method static Builder|PatchPanelPort whereSwitchPortId($value)
 * @method static Builder|PatchPanelPort wherePatchPanelId($value)
 * @method static Builder|PatchPanelPort whereCustomerId($value)
 * @method static Builder|PatchPanelPort whereState($value)
 * @method static Builder|PatchPanelPort whereNotes($value)
 * @method static Builder|PatchPanelPort whereAssignedAt($value)
 * @method static Builder|PatchPanelPort whereConnectAt($value)
 * @method static Builder|PatchPanelPort whereCeaseRequestedAt($value)
 * @method static Builder|PatchPanelPort whereCeaseAt($value)
 * @method static Builder|PatchPanelPort whereLastStateChange($value)
 * @method static Builder|PatchPanelPort whereInternalUse($value)
 * @method static Builder|PatchPanelPort whereChargeable($value)
 * @method static Builder|PatchPanelPort whereDuplexMasterId($value)
 * @method static Builder|PatchPanelPort whereNumber($value)
 * @method static Builder|PatchPanelPort whereColoCircuitRef($value)
 * @method static Builder|PatchPanelPort whereTicketRef($value)
 * @method static Builder|PatchPanelPort wherePrivateNotes($value)
 * @method static Builder|PatchPanelPort whereOwnedBy($value)
 * @method static Builder|PatchPanelPort whereLoaCode($value)
 * @method static Builder|PatchPanelPort whereDescription($value)
 * @method static Builder|PatchPanelPort whereColoBillingRef($value)
 * @mixin Eloquent
 */

class PatchPanelPort extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port';

    /**
     * Get the patch panel port files for this patch panel port
     */
    public function patchPanelPortFiles(): HasMany
    {
        return $this->hasMany(PatchPanelPortFile::class, 'patch_panel_port_id' );
    }
}
