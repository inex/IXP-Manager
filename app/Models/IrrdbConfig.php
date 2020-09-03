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
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Customer[] $customers
 * @property-read int|null $customers_count
 * @method static Builder|IrrdbConfig newModelQuery()
 * @method static Builder|IrrdbConfig newQuery()
 * @method static Builder|IrrdbConfig query()
 * @method static Builder|IrrdbConfig whereHost($value)
 * @method static Builder|IrrdbConfig whereId($value)
 * @method static Builder|IrrdbConfig whereNotes($value)
 * @method static Builder|IrrdbConfig whereProtocol($value)
 * @method static Builder|IrrdbConfig whereSource($value)
 * @mixin \Eloquent
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
     * Get the customers for the Irrdb Config
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