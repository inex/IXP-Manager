<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * IXP\Models\CoreInterface
 *
 * @property int $id
 * @property int|null $physical_interface_id
 * @property-read \IXP\Models\CoreLink|null $coreLinkSideA
 * @property-read \IXP\Models\CoreLink|null $coreLinkSideB
 * @property-read \IXP\Models\PhysicalInterface|null $physicalInterface
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreInterface wherePhysicalInterfaceId($value)
 * @mixin \Eloquent
 */
class CoreInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coreinterfaces';

    /**
     * Get the physical interface associated with the core interface.
     */
    public function physicalInterface(): BelongsTo
    {
        return $this->belongsTo(PhysicalInterface::class, 'physical_interface_id' );
    }

    /**
     * Get the corelink associated with the core interface side A.
     */
    public function coreLinkSideA(): HasOne
    {
        return $this->hasOne(CoreLink::class, 'core_interface_sidea_id' );
    }

    /**
     * Get the corelink associated with the core interface side B.
     */
    public function coreLinkSideB(): HasOne
    {
        return $this->hasOne(CoreLink::class, 'core_interface_sideb_id' );
    }

    /**
     * Check which side has a core link linked
     *
     * @return CoreLink
     */
    public function getCoreLink(): CoreLink
    {
        if( $this->coreLinkSideA()->exists() ) {
            return $this->coreLinkSideA;
        }

        return $this->coreLinkSideB;
    }
}
