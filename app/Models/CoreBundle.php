<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * IXP\Models\CoreBundle
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle query()
 * @mixin \Eloquent
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
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereBfd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereGraphTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereIpv4Subnet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereIpv6Subnet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle wherePreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereStp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CoreBundle whereType($value)
 * @property-read \IXP\Models\CoreLink $corelinks
 * @property-read int|null $corelinks_count
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
     * @return Collection
     */
    public static function getActive() : Collection
    {
        return self::where( 'enabled' , true )
            ->orderBy( 'description' )->get();
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
