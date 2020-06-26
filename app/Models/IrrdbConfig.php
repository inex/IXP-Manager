<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * IXP\Models\IrrdbConfig
 *
 * @property int $id
 * @property string|null $host
 * @property string|null $protocol
 * @property string|null $source
 * @property string|null $notes
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\IrrdbConfig whereSource($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Customer[] $customers
 * @property-read int|null $customers_count
 */
class IrrdbConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'irrdbconfig';

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
        'host',
        'protocol',
        'source',
        'notes',
    ];

    /**
     * Get the customers for the IrrdbConfig
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'irrdb');
    }

    /**
     * Gets a listing of IrrdbConfig or a single one if an ID is provided
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
}
