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

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

use Illuminate\Support\Carbon;

/**
 * IXP\Models\PatchPanelPort
 *
 * @property int $id
 * @property int|null $switch_port_id
 * @property int|null $patch_panel_id
 * @property int|null $customer_id
 * @property int $state
 * @property string|null $notes
 * @property string|null $assigned_at
 * @property string|null $connected_at
 * @property string|null $cease_requested_at
 * @property string|null $ceased_at
 * @property string|null $last_state_change
 * @property int $internal_use
 * @property int $chargeable
 * @property int|null $duplex_master_id
 * @property int $number
 * @property string|null $colo_circuit_ref
 * @property string|null $ticket_ref
 * @property string|null $private_notes
 * @property int $owned_by
 * @property string|null $loa_code
 * @property string|null $description
 * @property string|null $colo_billing_ref
 * @property-read \IXP\Models\PatchPanel|null $patchPanel
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPortFile[] $patchPanelPortFiles
 * @property-read int|null $patch_panel_port_files_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereCeaseRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereCeasedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereChargeable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereColoBillingRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereColoCircuitRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereConnectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereDuplexMasterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereInternalUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereLastStateChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereLoaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereOwnedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort wherePatchPanelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereSwitchPortId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPort whereTicketRef($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\SwitchPort|null $switchPort
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPort[] $slavePort
 * @property-read int|null $slave_port_count
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
     * Get the Patch Panel that owns this patch panel port
     */
    public function patchPanel(): BelongsTo
    {
        return $this->belongsTo( PatchPanel::class , 'patch_panel_id' );
    }

    /**
     * Get the switch port that owns this patch panel port
     */
    public function switchPort(): BelongsTo
    {
        return $this->belongsTo( SwitchPort::class , 'switch_port_id' );
    }

    /**
     * Get the patch panel port files for this patch panel port
     */
    public function patchPanelPortFiles(): HasMany
    {
        return $this->hasMany(PatchPanelPortFile::class, 'patch_panel_port_id' );
    }

    /**
     * Get the slave port for this patch panel port
     */
    public function slavePort(): HasMany
    {
        return $this->hasMany(PatchPanelPort::class, 'du' );
    }

    /**
     * Get name
     *
     * @return integer
     */
    public function getName()
    {
        $name = $this->patchPanel->port_prefix . $this->number;


        // finish me
//        if( $this->hasSlavePort() ) {
//            $name .= '/' . $this->getDuplexSlavePortName() . ' ';
//            $name .= '(' . ( $this->getNumber() % 2 ? ( floor( $this->getNumber() / 2 ) ) + 1 : $this->getNumber() / 2 ) . ')';
//        }
        return $name;
    }
}
