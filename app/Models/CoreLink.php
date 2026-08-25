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
 * IXP\Models\CoreLink
 *
 * @property int $id
 * @property int $core_interface_sidea_id
 * @property int $core_interface_sideb_id
 * @property int $core_bundle_id
 * @property int $bfd
 * @property string|null $ipv4_subnet
 * @property string|null $ipv6_subnet
 * @property int $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\CoreBundle $coreBundle
 * @property-read \IXP\Models\CoreInterface $coreInterfaceSideA
 * @property-read \IXP\Models\CoreInterface $coreInterfaceSideB
 * @method static Builder<static>|CoreLink active()
 * @method static Builder<static>|CoreLink newModelQuery()
 * @method static Builder<static>|CoreLink newQuery()
 * @method static Builder<static>|CoreLink query()
 * @method static Builder<static>|CoreLink whereBfd($value)
 * @method static Builder<static>|CoreLink whereCoreBundleId($value)
 * @method static Builder<static>|CoreLink whereCoreInterfaceSideaId($value)
 * @method static Builder<static>|CoreLink whereCoreInterfaceSidebId($value)
 * @method static Builder<static>|CoreLink whereCreatedAt($value)
 * @method static Builder<static>|CoreLink whereEnabled($value)
 * @method static Builder<static>|CoreLink whereId($value)
 * @method static Builder<static>|CoreLink whereIpv4Subnet($value)
 * @method static Builder<static>|CoreLink whereIpv6Subnet($value)
 * @method static Builder<static>|CoreLink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CoreLink extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'corelinks';

    /**
     * Get the core interface side A  associated with the corelink.
     *
     * @return BelongsTo<CoreInterface, CoreLink>
     */
    public function coreInterfaceSideA(): BelongsTo
    {
        return $this->belongsTo(CoreInterface::class, 'core_interface_sidea_id' );
    }

    /**
     * Get the core interface side B  associated with the corelink.
     *
     * @return BelongsTo<CoreInterface, CoreLink>
     */
    public function coreInterfaceSideB(): BelongsTo
    {
        return $this->belongsTo(CoreInterface::class, 'core_interface_sideb_id' );
    }

    /**
     * Get the core interface (A/B)
     *
     * @return CoreInterface[]
     *
     * @psalm-return list{CoreInterface, CoreInterface}
     */
    public function coreInterfaces(): array
    {
        return [ $this->coreInterfaceSideA, $this->coreInterfaceSideB ];
    }

    /**
     * Get the corebundle that own the corelink
     *
     * @return BelongsTo<CoreBundle, CoreLink>
     */
    public function coreBundle(): BelongsTo
    {
        return $this->belongsTo(CoreBundle::class, 'core_bundle_id' );
    }

    /**
     * Return all active core link
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive( Builder $query ): Builder
    {
        return $query->where( 'enabled' , true );
    }

    /**
     * Deletes the core link and associated physical and core interfaces
     */
    public function deleteObject(): void
    {
        $this->delete();
        foreach( $this->coreInterfaces() as $ci ){
            /** @var CoreInterface  $ci */
            $pi = $ci->physicalInterface;
            $pi->switchPort->update( [ 'type' => SwitchPort::TYPE_UNSET ] );

            $ci->delete();
            $pi->delete();
        }
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
            "Core Link [id:%d] belonging to Core Bundle [id:%d] '%s'",
            $model->id,
            $model->core_bundle_id,
            $model->coreBundle->description
        );
    }
}