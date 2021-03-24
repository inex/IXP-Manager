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
    Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\Cabinet
 *
 * @property int $id
 * @property int|null $locationid
 * @property string|null $name
 * @property string|null $colocation
 * @property int|null $height
 * @property string|null $type
 * @property string|null $notes
 * @property int|null $u_counts_from
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|\IXP\Models\ConsoleServer[] $consoleServers
 * @property-read int|null $console_servers_count
 * @property-read Collection|\IXP\Models\CustomerEquipment[] $customerEquipment
 * @property-read int|null $customer_equipment_count
 * @property-read \IXP\Models\Location|null $location
 * @property-read Collection|\IXP\Models\PatchPanel[] $patchPanels
 * @property-read int|null $patch_panels_count
 * @property-read Collection|\IXP\Models\Switcher[] $switchers
 * @property-read int|null $switchers_count
 * @method static Builder|Cabinet newModelQuery()
 * @method static Builder|Cabinet newQuery()
 * @method static Builder|Cabinet query()
 * @method static Builder|Cabinet whereColocation($value)
 * @method static Builder|Cabinet whereCreatedAt($value)
 * @method static Builder|Cabinet whereHeight($value)
 * @method static Builder|Cabinet whereId($value)
 * @method static Builder|Cabinet whereLocationid($value)
 * @method static Builder|Cabinet whereName($value)
 * @method static Builder|Cabinet whereNotes($value)
 * @method static Builder|Cabinet whereType($value)
 * @method static Builder|Cabinet whereUCountsFrom($value)
 * @method static Builder|Cabinet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cabinet extends Model
{
    use Observable;

    /**
     * Constants to indicate whether 'u' positions count from top or bottom
     */
    public const U_COUNTS_FROM_TOP    = 1;
    public const U_COUNTS_FROM_BOTTOM = 2;

    /**
     * @var array Textual representations of where u's count from
     */
    public static $U_COUNTS_FROM = [
        self::U_COUNTS_FROM_TOP     => 'Top',
        self::U_COUNTS_FROM_BOTTOM  => 'Bottom',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cabinet';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locationid',
        'name',
        'colocation',
        'height',
        'type',
        'notes',
        'u_counts_from'
    ];

    /**
     * Get the switchers for the cabinet
     */
    public function switchers(): HasMany
    {
        return $this->hasMany(Switcher::class, 'cabinetid' );
    }

    /**
     * Get the customerEquipments for the cabinet
     */
    public function customerEquipment(): HasMany
    {
        return $this->hasMany(CustomerEquipment::class, 'cabinetid' );
    }

    /**
     * Get the console servers for the cabinet
     */
    public function consoleServers(): HasMany
    {
        return $this->hasMany(ConsoleServer::class, 'cabinet_id' );
    }

    /**
     * Get the patch panels for the cabinet
     */
    public function patchPanels(): HasMany
    {
        return $this->hasMany(PatchPanel::class, 'cabinet_id' );
    }

    /**
     * Get the location for the cabinet
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'locationid' );
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
            "Rack (Cabinet) [id:%d] '%s'",
            $model->id,
            $model->name,
        );
    }
}