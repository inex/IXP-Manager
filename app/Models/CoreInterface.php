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
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreInterface wherePhysicalInterfaceId($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\CoreLink|null $corelinksidea
 * @property-read \IXP\Models\CoreLink|null $corelinksideb
 * @property-read \IXP\Models\PhysicalInterface|null $physicalinterface
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
    public function physicalinterface(): BelongsTo
    {
        return $this->belongsTo(PhysicalInterface::class, 'physical_interface_id' );
    }

    /**
     * Get the corelink associated with the core interface side A.
     */
    public function corelinksidea(): HasOne
    {
        return $this->hasOne(CoreLink::class, 'core_interface_sidea_id' );
    }

    /**
     * Get the corelink associated with the core interface side B.
     */
    public function corelinksideb(): HasOne
    {
        return $this->hasOne(CoreLink::class, 'core_interface_sideb_id' );
    }
}
