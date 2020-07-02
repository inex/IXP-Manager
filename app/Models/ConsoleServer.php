<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * IXP\Models\ConsoleServer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer query()
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $vendor_id
 * @property int|null $cabinet_id
 * @property string|null $name
 * @property string|null $hostname
 * @property string|null $model
 * @property string|null $serialNumber
 * @property int|null $active
 * @property string|null $notes
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereCabinetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereHostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ConsoleServer whereVendorId($value)
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @property-read \IXP\Models\Vendor|null $vendor
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServerConnection[] $consoleServerConnections
 * @property-read int|null $console_server_connections_count
 */
class ConsoleServer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'console_server';
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
        'vendor_id',
        'cabinet_id',
        'name',
        'hostname',
        'model',
        'serialNumber',
        'active',
        'notes',
    ];

    /**
     * Get the vendor that own the console server
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id' );
    }

    /**
     * Get the cabinet that own the console server
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinet_id' );
    }

    /**
     * Get the console server connections for the console server
     */
    public function consoleServerConnections(): HasMany
    {
        return $this->hasMany(ConsoleServerConnection::class, 'console_server_id');
    }

    /**
     * Gets a listing of console servers list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        return self::selectRaw(
            'cs.*,
            v.id AS vendorid, v.name AS vendor,
            c.id AS cabinetid, c.name AS cabinet, 
            l.id AS locationid, l.shortname AS facility,
            COUNT( DISTINCT csc.id ) AS num_connections'
        )
            ->from( 'console_server AS cs' )
            ->leftJoin( 'consoleserverconnection AS csc', 'csc.console_server_id', 'cs.id')
            ->leftJoin( 'cabinet AS c', 'c.id', 'cs.cabinet_id')
            ->leftJoin( 'location AS l', 'l.id', 'c.locationid')
            ->leftJoin( 'vendor AS v', 'v.id', 'cs.vendor_id')
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('cs.id', $id );
            } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })
            ->groupBy('cs.id' )->get()->toArray();
    }

    /**
     * Gets a listing of console serves as array
     *
     * @return array
     */
    public static function getListAsArray(): array
    {
        return self::orderBy( 'name', 'asc')->get()->keyBy( 'id' )->toArray();
    }
}
