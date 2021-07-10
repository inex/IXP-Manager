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
    Relations\BelongsTo
};

use IXP\Traits\Observable;

/**
 * IXP\Models\ConsoleServerConnection
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $description
 * @property string|null $port
 * @property int|null $speed
 * @property int|null $parity
 * @property int|null $stopbits
 * @property int|null $flowcontrol
 * @property int|null $autobaud
 * @property string|null $notes
 * @property int|null $console_server_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\ConsoleServer|null $consoleServer
 * @property-read \IXP\Models\CustomerTag|null $customer
 * @property-read \IXP\Models\Switcher $switcher
 * @method static Builder|ConsoleServerConnection newModelQuery()
 * @method static Builder|ConsoleServerConnection newQuery()
 * @method static Builder|ConsoleServerConnection query()
 * @method static Builder|ConsoleServerConnection whereAutobaud($value)
 * @method static Builder|ConsoleServerConnection whereConsoleServerId($value)
 * @method static Builder|ConsoleServerConnection whereCreatedAt($value)
 * @method static Builder|ConsoleServerConnection whereCustid($value)
 * @method static Builder|ConsoleServerConnection whereDescription($value)
 * @method static Builder|ConsoleServerConnection whereFlowcontrol($value)
 * @method static Builder|ConsoleServerConnection whereId($value)
 * @method static Builder|ConsoleServerConnection whereNotes($value)
 * @method static Builder|ConsoleServerConnection whereParity($value)
 * @method static Builder|ConsoleServerConnection wherePort($value)
 * @method static Builder|ConsoleServerConnection whereSpeed($value)
 * @method static Builder|ConsoleServerConnection whereStopbits($value)
 * @method static Builder|ConsoleServerConnection whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConsoleServerConnection extends Model
{
    use Observable;

    public static $SPEED = [
        300     => 300,
        600     => 600,
        1200    => 1200,
        2400    => 2400,
        4800    => 4800,
        9600    => 9600,
        14400   => 14400,
        19200   => 19200,
        28800   => 28800,
        38400   => 38400,
        57600   => 57600,
        115200  => 115200,
        230400  => 230400
    ];

    public const PARITY_EVEN       = 1;
    public const PARITY_ODD        = 2;
    public const PARITY_NONE       = 3;

    public static $PARITY = [
        self::PARITY_EVEN   => "even",
        self::PARITY_ODD    => "odd",
        self::PARITY_NONE   => "none"
    ];

    public const FLOW_CONTROL_NONE         = 1;
    public const FLOW_CONTROL_RTS_CTS      = 2;
    public const FLOW_CONTROL_XON_XOFF     = 3;

    public static $FLOW_CONTROL = [
        self::FLOW_CONTROL_NONE         => "none",
        self::FLOW_CONTROL_RTS_CTS      => "rts/cts",
        self::FLOW_CONTROL_XON_XOFF     => "xon/xoff"
    ];

    public static $STOP_BITS = [
        1 => 1,
        2 => 2,
    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'consoleserverconnection';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'port',
        'speed',
        'parity',
        'stopbits',
        'flowcontrol',
        'autobaud',
        'notes',
        'console_server_id',
    ];

    /**
     * Get the customer that own the console server connection
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerTag::class, 'custid' );
    }

    /**
     * Get the customer that own the console server connection
     */
    public function switcher(): BelongsTo
    {
        return $this->belongsTo(Switcher::class, 'switchid' );
    }

    /**
     * Get the console server that own the console server connection
     */
    public function consoleServer(): BelongsTo
    {
        return $this->belongsTo(ConsoleServer::class, 'console_server_id' );
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
            "Console Server Connection [id:%d] belonging to Console Server [id:%d] '%s'",
            $model->id,
            $model->console_server_id,
            $model->consoleServer->name
        );
    }
}