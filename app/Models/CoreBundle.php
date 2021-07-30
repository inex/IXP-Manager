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
use DB, Exception;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model,
    Relations\HasMany
};

use IXP\Traits\Observable;

use OSS_SNMP\MIBS\Iface;

/**
 * IXP\Models\CoreBundle
 *
 * @property int $id
 * @property string $description
 * @property int $type
 * @property string $graph_title
 * @property int $bfd
 * @property string|null $ipv4_subnet
 * @property string|null $ipv6_subnet
 * @property bool $stp
 * @property int|null $cost
 * @property int|null $preference
 * @property int $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|\IXP\Models\CoreLink[] $corelinks
 * @property-read int|null $corelinks_count
 * @method static Builder|CoreBundle active()
 * @method static Builder|CoreBundle newModelQuery()
 * @method static Builder|CoreBundle newQuery()
 * @method static Builder|CoreBundle query()
 * @method static Builder|CoreBundle whereBfd($value)
 * @method static Builder|CoreBundle whereCost($value)
 * @method static Builder|CoreBundle whereCreatedAt($value)
 * @method static Builder|CoreBundle whereDescription($value)
 * @method static Builder|CoreBundle whereEnabled($value)
 * @method static Builder|CoreBundle whereGraphTitle($value)
 * @method static Builder|CoreBundle whereId($value)
 * @method static Builder|CoreBundle whereIpv4Subnet($value)
 * @method static Builder|CoreBundle whereIpv6Subnet($value)
 * @method static Builder|CoreBundle wherePreference($value)
 * @method static Builder|CoreBundle whereStp($value)
 * @method static Builder|CoreBundle whereType($value)
 * @method static Builder|CoreBundle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CoreBundle extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'corebundles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'type',
        'graph_title',
        'bfd',
        'ipv4_subnet',
        'stp',
        'cost',
        'preference',
        'enabled'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'stp'         => 'boolean',
    ];

    /**
     * CONST TYPES
     */
    public const TYPE_ECMP              = 1;
    public const TYPE_L2_LAG            = 2;
    public const TYPE_L3_LAG            = 3;

    /**
     * Array STATES
     */
    public static $TYPES = [
        self::TYPE_ECMP          => "ECMP",
        self::TYPE_L2_LAG        => "L2-LAG (e.g. LACP)",
        self::TYPE_L3_LAG        => "L3-LAG",
    ];

    /**
     * Get the corelinks that belong to the corebundle
     */
    public function corelinks(): HasMany
    {
        return $this->HasMany(CoreLink::class, 'core_bundle_id' );
    }

    /**
     * Is the type TYPE_ECMP?
     *
     * @return bool
     */
    public function typeECMP(): bool
    {
        return $this->type === self::TYPE_ECMP;
    }

    /**
     * Is the type isTypeL2Lag?
     *
     * @return bool
     */
    public function typeL2Lag(): bool
    {
        return $this->type === self::TYPE_L2_LAG;
    }

    /**
     * Is the type isTypeL3Lag?
     *
     * @return bool
     */
    public function typeL3Lag(): bool
    {
        return $this->type === self::TYPE_L3_LAG;
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     *
     * @return string
     */
    public function typeText(): string
    {
        return self::$TYPES[ $this->type ] ?? 'Unknown';
    }

    /**
     * Return all active core bundles
     *
     * @param Builder $query
     *
     * @return Builder
     */

    public function scopeActive( Builder $query ): Builder
    {
        return $query->where( 'enabled' , true )
            ->orderBy( 'description' );
    }

    /**
     * get switch from side A or B
     *
     * @param bool $sideA if true get the side A if false Side B
     *
     * @return Switcher|bool
     */
    public function switchSideX( bool $sideA = true )
    {
        $cl = $this->corelinks->first() ?? false;

        if( $cl ){
            /** @var CoreInterface $side */
            $side = $sideA ? $cl->coreInterfaceSideA : $cl->coreInterfaceSideB;
            return $side->physicalinterface->switchPort->switcher;
        }

        return false;
    }

    /**
     * Check if all the core links for the core bundle are enabled
     *
     * @return boolean
     */
    public function allCoreLinksEnabled(): bool
    {
        return $this->corelinks->where( 'enabled', false )->count() <= 0;
    }

    /**
     * get the speed of the Physical interface
     *
     * @return int
     */
    public function speedPi(): int
    {
        $cl = $this->corelinks->first() ?? false;
        if( $cl ){
            return $cl->coreInterfaceSideA->physicalinterface->speed;
        }
        return 0;
    }

    /**
     * get the duplex of the Physical interface
     *
     * @return int|false
     */
    public function duplexPi()
    {
        if( $cl = $this->corelinks()->first() ){
            return $cl->coreInterfaceSideA->physicalinterface->duplex;
        }

        return false;
    }

    /**
     * get the auto neg of the Physical interface
     *
     * @return int|false
     */
    public function autoNegPi()
    {
        if( $cl = $this->corelinks()->first() ){
            return $cl->coreInterfaceSideA->physicalinterface->autoneg;
        }

        return false;
    }

    /**
     * get the customer associated virtual interface of the core bundle
     *
     * @return Customer|bool
     */
    public function customer()
    {
        $cl = $this->corelinks[ 0 ] ?? false;
        if( $cl ){
            return $cl->coreInterfaceSideA->physicalinterface->virtualInterface->customer;
        }
        return false;
    }

    /**
     * get the virtual interfaces linked to the core links of the side A and B
     *
     * @return array
     */
    public function virtualInterfaces(): array
    {
        $vis = [];
        if( $cl = $this->corelinks()->first() ){
            $vis[ 'a' ] = $cl->coreInterfaceSideA->physicalInterface->virtualInterface;
            $vis[ 'b' ] = $cl->coreInterfaceSideB->physicalInterface->virtualInterface;
        }
        return $vis;
    }

    /**
     * Get all core links where each side of the link has an SNMP IF Oper State as provided
     * (defaults to operational state: UP).
     *
     * @param  int  $operstate
     * @param  bool  $onlyEnabled
     *
     * @return CoreLink[]
     */
    public function coreLinksWithIfOperStateX( int $operstate = Iface::IF_ADMIN_STATUS_UP, bool $onlyEnabled = true ): array {
        return self::select( 'cl.*' )
            ->from( 'corebundles AS cb' )
            ->leftJoin( 'corelinks AS cl', 'cl.core_bundle_id', 'cb.id' )
            ->leftJoin( 'coreinterfaces AS cia', 'cia.id', 'cl.core_interface_sidea_id' )
            ->leftJoin( 'coreinterfaces AS cib', 'cib.id', 'cl.core_interface_sideb_id' )
            ->leftJoin( 'physicalinterface AS pia', 'pia.id', 'cia.physical_interface_id' )
            ->leftJoin( 'physicalinterface AS pib', 'pib.id', 'cib.physical_interface_id' )
            ->leftJoin( 'switchport AS spa', 'spa.id', 'pia.switchportid' )
            ->leftJoin( 'switchport AS spb', 'spb.id', 'pib.switchportid' )
            ->where( 'spa.ifOperStatus', $operstate )
            ->where( 'spb.ifOperStatus', $operstate )
            ->where( 'cb.id', $this->id )
            ->when( $onlyEnabled , function( Builder $q ) {
                return $q->where( 'cl.enabled', true );
            })->get()->toArray();
    }


        /**
     * Check if the switch is the same for the Physical interfaces of the core links associated to the core bundle
     *
     * @param bool $sideA if true get the side A if false Side B
     *
     * @return bool
     */
    public function sameSwitchForEachPIFromCL( bool $sideA = true ): bool
    {
        $switches = [];

        foreach( $this->corelinks as $cl ) {
            /** @var CoreInterface $side */
            $side = $sideA ? $cl->coreInterfaceSideA : $cl->coreInterfaceSideB ;
            $switches[] = $side->physicalInterface->switchPort->switcher->id;
        }

        return count(array_unique($switches)) == 1;
    }

    /**
     * Delete the Core Bundle and everything related.
     *
     * @return bool
     *
     * @throws
     */
    public function deleteObject(): bool
    {
        try {
            DB::beginTransaction();
            $vis = [];
            foreach( $this->corelinks as $cl ){
                $cl->delete();
                foreach( $cl->coreInterfaces() as $ci ){
                    /** @var CoreInterface  $ci */
                    $ci->delete();
                    $vis[] = $ci->physicalInterface->virtualInterface;
                    $ci->physicalInterface->delete();
                }
            }

            foreach( $vis as $vi ){
                $vi->delete();
            }

            $this->delete();
            DB::commit();
        } catch( Exception $e ) {
            DB::rollBack();
            throw $e;
        }
        return true;
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
            "Core Bundle [id:%d] '%s'",
            $model->id,
            $model->description,
        );
    }
}