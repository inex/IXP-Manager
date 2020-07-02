<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * IXP\Models\Cabinet
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet query()
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $locationid
 * @property string|null $name
 * @property string|null $cololocation
 * @property int|null $height
 * @property string|null $type
 * @property string|null $notes
 * @property int|null $u_counts_from
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereCololocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereLocationid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereUCountsFrom($value)
 * @property-read \IXP\Models\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Switcher[] $switchers
 * @property-read int|null $switchers_count
 * @property string|null $colocation
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\CustomerEquipment[] $customerEquipment
 * @property-read int|null $customer_equipment_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Cabinet whereColocation($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServer[] $consoleServers
 * @property-read int|null $console_servers_count
 */
class Cabinet extends Model
{
    /**
     * Constants to indicate whether 'u' positions count from top or bottom
     */
    const U_COUNTS_FROM_TOP    = 1;
    const U_COUNTS_FROM_BOTTOM = 2;

    /**
     * @var array Textual representations of where u's count from
     */
    public static $U_COUNTS_FROM = [
        self::U_COUNTS_FROM_TOP     => 'Top',
        self::U_COUNTS_FROM_BOTTOM  => 'Bottom',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cabinet';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locationid',
        'name',
        'colocation',
        'height',
        'type',
        'notes',
        'u_counts_from'
    ];

    /**
     * Get the switchers for the cabinet
     */
    public function switchers(): HasMany
    {
        return $this->hasMany(Switcher::class, 'cabinetid' );
    }

    /**
     * Get the customerEquipments for the cabinet
     */
    public function customerEquipment(): HasMany
    {
        return $this->hasMany(CustomerEquipment::class, 'cabinetid' );
    }

    /**
     * Get the console servers for the cabinet
     */
    public function consoleServers(): HasMany
    {
        return $this->hasMany(ConsoleServer::class, 'cabinet_id' );
    }

    /**
     * Get the infrastructure that own the switcher
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'locationid' );
    }

    /**
     * Gets a listing of cabinets list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        return self::when( $id , function( Builder $q, $id ) {
            return $q->where('id', $id );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    /**
     * Gets a listing of cabinets from dropdown
     *
     * @return array
     */
    public static function getListAsArray(): array
    {
        return self::selectRaw( "id, concat( name, ' [', colocation, ']') AS name" )
            ->orderBy( 'name', 'asc' )
            ->get()->toArray();
    }
}
