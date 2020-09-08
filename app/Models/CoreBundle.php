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

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model,
    Relations\HasMany
};


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
 * @property int $stp
 * @property int|null $cost
 * @property int|null $preference
 * @property int $enabled
 * @property-read Collection|\IXP\Models\CoreLink[] $corelinks
 * @property-read int|null $corelinks_count
 * @method static Builder|CoreBundle newModelQuery()
 * @method static Builder|CoreBundle newQuery()
 * @method static Builder|CoreBundle query()
 * @method static Builder|CoreBundle whereBfd($value)
 * @method static Builder|CoreBundle whereCost($value)
 * @method static Builder|CoreBundle whereDescription($value)
 * @method static Builder|CoreBundle whereEnabled($value)
 * @method static Builder|CoreBundle whereGraphTitle($value)
 * @method static Builder|CoreBundle whereId($value)
 * @method static Builder|CoreBundle whereIpv4Subnet($value)
 * @method static Builder|CoreBundle whereIpv6Subnet($value)
 * @method static Builder|CoreBundle wherePreference($value)
 * @method static Builder|CoreBundle whereStp($value)
 * @method static Builder|CoreBundle whereType($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle active()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereUpdatedAt($value)
 */
class CoreBundle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'corebundles';


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
    public function isTypeECMP(): bool
    {
        return $this->type === self::TYPE_ECMP;
    }

    /**
     * Is the type isTypeL2Lag?
     *
     * @return bool
     */
    public function isTypeL2Lag(): bool
    {
        return type === self::TYPE_L2_LAG;
    }

    /**
     * Is the type isTypeL3Lag?
     *
     * @return bool
     */
    public function isTypeL3Lag(): bool
    {
        return $this->type === self::TYPE_L3_LAG;
    }

    /**
     * Turn the database integer representation of the type into text as
     * defined in the self::$TYPES array (or 'Unknown')
     * @return string
     */
    public function resolveType(): string
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
    public function getSwitchSideX( bool $sideA = true )
    {
        if( $this->corelinks()->exists() ){
            $cl = $this->corelinks()->first();
            /** @var CoreInterface $side */
            $side = $sideA ? $cl->coreInterfaceSideA : $cl->coreInterfaceSideB ;
            return $side->physicalinterface->switchPort->switcher;
        }

        return false;
    }

    /**
     * get the speed of the Physical interface
     *
     * @return int
     */
    public function getSpeedPi()
    {
        if( $this->corelinks()->exists() ){
            $cl = $this->corelinks()->first();

            return $cl->coreInterfaceSideA->physicalinterface->speed;
        }

        return 0;
    }
}
