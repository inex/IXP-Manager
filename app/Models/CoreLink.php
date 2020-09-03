<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @property-read \IXP\Models\CoreBundle $coreBundle
 * @property-read \IXP\Models\CoreInterface $coreInterfaceSideA
 * @property-read \IXP\Models\CoreInterface $coreInterfaceSideB
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereBfd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereCoreBundleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereCoreInterfaceSideaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereCoreInterfaceSidebId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereIpv4Subnet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CoreLink whereIpv6Subnet($value)
 * @mixin \Eloquent
 */
class CoreLink extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'corelinks';

    /**
     * Get the core interface side A  associated with the corelink.
     */
    public function coreInterfaceSideA(): BelongsTo
    {
        return $this->belongsTo(CoreInterface::class, 'core_interface_sidea_id' );
    }

    /**
     * Get the core interface side B  associated with the corelink.
     */
    public function coreInterfaceSideB(): BelongsTo
    {
        return $this->belongsTo(CoreInterface::class, 'core_interface_sideb_id' );
    }

    /**
     * Get the corebundle that own the corelink
     */
    public function coreBundle(): BelongsTo
    {
        return $this->belongsTo(CoreBundle::class, 'core_bundle_id' );
    }
}
