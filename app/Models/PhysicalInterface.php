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
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasOne
};

use IXP\Traits\Observable;

/**
 * IXP\Models\PhysicalInterface
 *
 * @property int $id
 * @property int|null $switchportid
 * @property int|null $virtualinterfaceid
 * @property int|null $status
 * @property int|null $speed
 * @property string|null $duplex
 * @property string|null $notes
 * @property int|null $fanout_physical_interface_id
 * @property bool $autoneg
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\CoreInterface|null $coreInterface
 * @property-read PhysicalInterface|null $fanoutPhysicalInterface
 * @property-read PhysicalInterface|null $peeringPhysicalInterface
 * @property-write mixed $rate_limit
 * @property-read \IXP\Models\SwitchPort|null $switchPort
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\TrafficDailyPhysInt[] $trafficDailiesPhysInt
 * @property-read int|null $traffic_dailies_phys_int_count
 * @property-read \IXP\Models\VirtualInterface|null $virtualInterface
 * @method static Builder|PhysicalInterface connected()
 * @method static Builder|PhysicalInterface graphable()
 * @method static Builder|PhysicalInterface newModelQuery()
 * @method static Builder|PhysicalInterface newQuery()
 * @method static Builder|PhysicalInterface query()
 * @method static Builder|PhysicalInterface whereAutoneg($value)
 * @method static Builder|PhysicalInterface whereCreatedAt($value)
 * @method static Builder|PhysicalInterface whereDuplex($value)
 * @method static Builder|PhysicalInterface whereFanoutPhysicalInterfaceId($value)
 * @method static Builder|PhysicalInterface whereId($value)
 * @method static Builder|PhysicalInterface whereNotes($value)
 * @method static Builder|PhysicalInterface whereSpeed($value)
 * @method static Builder|PhysicalInterface whereStatus($value)
 * @method static Builder|PhysicalInterface whereSwitchportid($value)
 * @method static Builder|PhysicalInterface whereUpdatedAt($value)
 * @method static Builder|PhysicalInterface whereVirtualinterfaceid($value)
 * @mixin Eloquent
 */
class PhysicalInterface extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'physicalinterface';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'switchportid',
        'virtualinterfaceid',
        'status',
        'speed',
        'duplex',
        'rate_limit',
        'autoneg',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'autoneg'         => 'boolean',
    ];

    /**
     * Mutator for rate limit
     *
     * @param  ?int  $value
     * @return void
     */
    public function setRateLimitAttribute($value)
    {
        $this->attributes['rate_limit'] = $value ?: null;
    }

    public const STATUS_CONNECTED       = 1;
    public const STATUS_DISABLED        = 2;
    public const STATUS_NOTCONNECTED    = 3;
    public const STATUS_XCONNECT        = 4;
    public const STATUS_QUARANTINE      = 5;

    public static $STATES = [
        self::STATUS_CONNECTED    => 'Connected',
        self::STATUS_DISABLED     => 'Disabled',
        self::STATUS_NOTCONNECTED => 'Not Connected',
        self::STATUS_XCONNECT     => 'Awaiting X-Connect',
        self::STATUS_QUARANTINE   => 'Quarantine'
    ];

    public static $APISTATES = [
        self::STATUS_CONNECTED    => 'connected',
        self::STATUS_DISABLED     => 'disabled',
        self::STATUS_NOTCONNECTED => 'notconnected',
        self::STATUS_XCONNECT     => 'awaitingxconnect',
        self::STATUS_QUARANTINE   => 'quarantine'
    ];

    public static $SPEED = [
        10    => '10 Mbps',
        100   => '100 Mbps',
        1000  => '1 Gbps',
        10000 => '10 Gbps',
        25000 => '25 Gbps',
        40000 => '40 Gbps',
        100000 => '100 Gbps',
        400000 => '400 Gbps'
    ];

    public static $DUPLEX = [
        'full'   => 'full',
        'half'   => 'half'
    ];

    /**
     * Get the virtual interface that owns the physical interface.
     */
    public function virtualInterface(): BelongsTo
    {
        return $this->belongsTo(VirtualInterface::class, 'virtualinterfaceid' );
    }

    /**
     * Get the switch port that owns the physical interface.
     */
    public function switchPort(): BelongsTo
    {
        return $this->belongsTo(SwitchPort::class, 'switchportid');
    }

    /**
     * Get the fanout physical interface associated with the physical interface.
     */
    public function fanoutPhysicalInterface(): BelongsTo
    {
        return $this->belongsTo( __CLASS__, 'fanout_physical_interface_id' );
    }

    /**
     * Get the core interface associated with the physical interface.
     */
    public function coreInterface(): HasOne
    {
        return $this->hasOne(CoreInterface::class, 'physical_interface_id' );
    }

    /**
     * Get the peering physical interface associated with the physical interface.
     */
    public function peeringPhysicalInterface(): HasOne
    {
        return $this->hasOne( __CLASS__, 'fanout_physical_interface_id' );
    }

    /**
     * Get the trafficDailiesPhysInt associated with the physical interface.
     */
    public function trafficDailiesPhysInt(): HasMany
    {
        return $this->hasMany(TrafficDailyPhysInt::class, 'physicalinterface_id' );
    }

    /**
     * Determine if the port's status is set to QUARANTINE / CONNECTED
     *
     * @return bool True if the port's status is QUARANTINE / CONNECTED
     */
    public function isConnectedOrQuarantine(): bool
    {
        return $this->statusConnected() || $this->statusQuarantine();
    }

    /**
     * Determine if the port's status is set to CONNECTED
     *
     * @return bool True if the port's status is CONNECTED
     */
    public function statusConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED;
    }

    /**
     * Determine if the port's status is set to DISABLED
     *
     * @return bool True if the port's status is DISABLED
     */
    public function statusDisabled(): bool
    {
        return $this->status === self::STATUS_DISABLED;
    }

    /**
     * Determine if the port's status is set to NOTCONNECTED
     *
     * @return bool True if the port's status is NOTCONNECTED
     */
    public function statusNotConnected(): bool
    {
        return $this->status === self::STATUS_NOTCONNECTED;
    }

    /**
     * Determine if the port's status is set to XCONNECT
     *
     * @return bool True if the port's status is XCONNECT
     */
    public function statusAwaitingXConnect(): bool
    {
        return $this->status === self::STATUS_XCONNECT;
    }

    /**
     * Determine if the port's status is set to QUARANTINE
     *
     * @return bool True if the port's status is QUARANTINE
     */
    public function statusQuarantine(): bool
    {
        return $this->status === self::STATUS_QUARANTINE;
    }

    /**
     * Try to find the most accurate version of the port's speed.
     *
     * I.e. try the actual SNMP-discovered port speed first, otherwise use the configured speed
     *
     * @return int|null
     */
    public function detectedSpeed(): ?int
    {
        // try the actual SNMP-discovered port speed first, otherwise use the configured speed:
        return $this->switchPort->ifHighSpeed > 0 ? $this->switchPort->ifHighSpeed : $this->speed;
    }

    /**
     * Turn the database integer representation of the speed into text as
     * defined in the self::$SPEEDS array (or 'Unknown')
     *
     * @return string
     */
    public function speed(): string
    {
        return self::$SPEED[ $this->speed ] ?? 'Unknown';
    }

    /**
     * Is this port rate limited?
     */
    public function isRateLimited(): bool
    {
        return $this->rate_limit !== null;
    }

    /**
     * Get the configured speed
     */
    public function configuredSpeed(): int
    {
        return $this->rate_limit ?: $this->speed;
    }



    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     *
     * @return string
     */
    public function status(): string
    {
        return self::$STATES[ $this->status ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text suitable
     * for API output as defined in the self::$STATES array (or 'unknown')
     * @return string
     */
    public function apiStatus(): string
    {
        return self::$APISTATES[ $this->status ] ?? 'unknown';
    }

    /**
     * Is this port graphable?
     *
     * @return bool
     */
    public function isGraphable(): bool
    {
        return $this->isConnectedOrQuarantine();
    }

    /**
     * Scope to get connected virtual interface
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeGraphable( Builder $query ): Builder
    {
        return $query->where( 'status' , self::STATUS_CONNECTED )
            ->orWhere( 'status' , self::STATUS_QUARANTINE );
    }

    /**
     * Gets the related peering / fanout port for the current fanout / peering port
     *
     * For reseller functionality, we have the option of having fanout ports connected to
     * peering ports. In this case, this function will return the related peering or
     * fanout port as appropriate.
     *
     * @return PhysicalInterface|bool The related peering / fanout port (or false for none / n/a)
     */
    public function relatedInterface()
    {
        if( $sp = $this->switchPort ) {
            if( $sp->typeFanout() && $this->peeringPhysicalInterface ){
                return $this->peeringPhysicalInterface;
            }

            if( $sp->typePeering() && $this->fanoutPhysicalInterface ) {
                return $this->fanoutPhysicalInterface;
            }
            return false;
        }
        return false;
    }

    /**
     * Get the other physical interface associated to the core link of the current Physical Interface
     *
     * @return PhysicalInterface|bool
     */
    public function otherPICoreLink()
    {
        if( $ci = $this->coreInterface ){
            if( $this->id === $ci->coreLink()->coreInterfaceSideA->physical_interface_id ){
                return $ci->coreLink()->coreInterfaceSideB->physicalInterface;
            }
            return $ci->coreLink()->coreInterfaceSideA->physicalInterface;
        }
        return false;
    }

    /**
     * Scope to get connected virtual interface
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeConnected( Builder $query ): Builder
    {
        return $query->where( 'status' , self::STATUS_CONNECTED );
    }

    /**
     * Get the core bundle if the physical interface is associated to a core bundle
     *
     * @return CoreBundle|bool
     */
    public function coreBundle()
    {
        if( $ci = $this->coreInterface ){
            return $ci->coreLink()->coreBundle;
        }
        return false;
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
            "Physical Interface [id:%d] belonging to Virtual Interface [id:%d]",
            $model->id,
            $model->virtualinterfaceid,
        );
    }
}