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
    Model,
    Relations\BelongsTo,
    Relations\HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\ConsoleServer
 *
 * @property int $id
 * @property int|null $vendor_id
 * @property int|null $cabinet_id
 * @property string|null $name
 * @property string|null $hostname
 * @property string|null $model
 * @property string|null $serialNumber
 * @property int|null $active
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServerConnection[] $consoleServerConnections
 * @property-read int|null $console_server_connections_count
 * @property-read \IXP\Models\Vendor|null $vendor
 * @method static Builder|ConsoleServer newModelQuery()
 * @method static Builder|ConsoleServer newQuery()
 * @method static Builder|ConsoleServer query()
 * @method static Builder|ConsoleServer whereActive($value)
 * @method static Builder|ConsoleServer whereCabinetId($value)
 * @method static Builder|ConsoleServer whereCreatedAt($value)
 * @method static Builder|ConsoleServer whereHostname($value)
 * @method static Builder|ConsoleServer whereId($value)
 * @method static Builder|ConsoleServer whereModel($value)
 * @method static Builder|ConsoleServer whereName($value)
 * @method static Builder|ConsoleServer whereNotes($value)
 * @method static Builder|ConsoleServer whereSerialNumber($value)
 * @method static Builder|ConsoleServer whereUpdatedAt($value)
 * @method static Builder|ConsoleServer whereVendorId($value)
 * @mixin \Eloquent
 */
class ConsoleServer extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'console_server';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendor_id',
        'cabinet_id',
        'name',
        'hostname',
        'model',
        'serialNumber',
        'active',
        'notes',
    ];

    /**
     * Get the vendor that own the console server
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id' );
    }

    /**
     * Get the cabinet that own the console server
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinet_id' );
    }

    /**
     * Get the console server connections for the console server
     */
    public function consoleServerConnections(): HasMany
    {
        return $this->hasMany(ConsoleServerConnection::class, 'console_server_id');
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
            "Console Server [id:%d] '%s'",
            $model->id,
            $model->name
        );
    }
}