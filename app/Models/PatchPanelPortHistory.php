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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\PatchPanelPort|null $patchPanelPort
 * @property-read Collection|\IXP\Models\PatchPanelPortHistoryFile[] $patchPanelPortHistoryFiles
 * @property-read int|null $patch_panel_port_history_files_count
 * @method static Builder|PatchPanelPortHistory masterPort()
 * @method static Builder|PatchPanelPortHistory newModelQuery()
 * @method static Builder|PatchPanelPortHistory newQuery()
 * @method static Builder|PatchPanelPortHistory query()
 * @method static Builder|PatchPanelPortHistory whereAssignedAt($value)
 * @method static Builder|PatchPanelPortHistory whereCeaseRequestedAt($value)
 * @method static Builder|PatchPanelPortHistory whereCeasedAt($value)
 * @method static Builder|PatchPanelPortHistory whereChargeable($value)
 * @method static Builder|PatchPanelPortHistory whereColoBillingRef($value)
 * @method static Builder|PatchPanelPortHistory whereColoCircuitRef($value)
 * @method static Builder|PatchPanelPortHistory whereConnectedAt($value)
 * @method static Builder|PatchPanelPortHistory whereCreatedAt($value)
 * @method static Builder|PatchPanelPortHistory whereCustId($value)
 * @method static Builder|PatchPanelPortHistory whereCustomer($value)
 * @method static Builder|PatchPanelPortHistory whereDescription($value)
 * @method static Builder|PatchPanelPortHistory whereDuplexMasterId($value)
 * @method static Builder|PatchPanelPortHistory whereId($value)
 * @method static Builder|PatchPanelPortHistory whereInternalUse($value)
 * @method static Builder|PatchPanelPortHistory whereNotes($value)
 * @method static Builder|PatchPanelPortHistory whereNumber($value)
 * @method static Builder|PatchPanelPortHistory whereOwnedBy($value)
 * @method static Builder|PatchPanelPortHistory wherePatchPanelPortId($value)
 * @method static Builder|PatchPanelPortHistory wherePrivateNotes($value)
 * @method static Builder|PatchPanelPortHistory whereState($value)
 * @method static Builder|PatchPanelPortHistory whereSwitchport($value)
 * @method static Builder|PatchPanelPortHistory whereTicketRef($value)
 * @method static Builder|PatchPanelPortHistory whereUpdatedAt($value)
 * @mixin Eloquent
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'switchport',
        'patch_panel_port_id',
        'customer',
        'cust_id',
        'state',
        'notes',
        'assigned_at',
        'connected_at',
        'cease_requested_at',
        'ceased_at',
        'internal_use',
        'chargeable',
        'duplex_master_id',
        'number',
        'colo_circuit_ref',
        'ticket_ref',
        'private_notes',
        'owned_by',
        'description',
        'colo_billing_ref',
    ];

    /**
     * Get the Patch Panel Port that owns this patch panel port history
     */
    public function patchPanelPort(): BelongsTo
    {
        return $this->belongsTo( PatchPanelPort::class , 'patch_panel_port_id' );
    }

    /**
     * Get the patch panel port history files for this patch panel port history
     */
    public function patchPanelPortHistoryFiles(): HasMany
    {
        return $this->hasMany(PatchPanelPortHistoryFile::class, 'patch_panel_port_history_id' );
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$CHARGEABLES array (or 'Unknown')
     *
     * @return string
     */
    public function chargeable(): string
    {
        return self::$CHARGEABLES[ $this->chargeable ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     *
     * @return string
     */
    public function ownedBy(): string
    {
        return self::$OWNED_BY[ $this->owned_by ] ?? 'Unknown';
    }

    /**
     * Populate the history model with details from a patch panel port.
     *
     * @param PatchPanelPort $ppp
     *
     * @return PatchPanelPortHistory
     *
     * @throws
     */
    public static function createFromPort( PatchPanelPort $ppp ): PatchPanelPortHistory
    {
        return self::create( [
            'switchport'            => $ppp->switchPort ? $ppp->switchPort->switcher->name . '::' . $ppp->switchPort->name : '',
            'patch_panel_port_id'   => $ppp->id,
            'customer'              => $ppp->customer->name ?? '',
            'cust_id'               => $ppp->customer->id ?? '',
            'state'                 => $ppp->state,
            'notes'                 => $ppp->notes,
            'assigned_at'           => $ppp->assigned_at,
            'connected_at'          => $ppp->connected_at,
            'cease_requested_at'    => $ppp->cease_requested_at,
            'ceased_at'             => $ppp->ceased_at ?? now(),
            'internal_use'          => $ppp->internal_use,
            'chargeable'            => $ppp->chargeable,
            'number'                => $ppp->number,
            'colo_circuit_ref'      => $ppp->colo_circuit_ref,
            'ticket_ref'            => $ppp->ticket_ref,
            'private_notes'         => $ppp->private_notes,
            'owned_by'              => $ppp->owned_by,
            'description'           => $ppp->description,
            'colo_billing_ref'      => $ppp->colo_billing_ref,
        ] );
    }

    /**
     * Scope a query to match master ports only
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeMasterPort( Builder $query ): Builder
    {
        return $query->where('duplex_master_id', null );
    }
}
