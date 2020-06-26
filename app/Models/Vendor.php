<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use stdClass;

/**
 * IXP\Models\Vendor
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property string|null $nagios_name
 * @property string|null $bundle_name
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor whereBundleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor whereNagiosName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vendor whereShortname($value)
 * @mixin \Eloquent
 */
class Vendor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vendor';

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
        'name',
        'shortname',
        'bundle_name',
    ];

    /**
     * Gets a listing of vendors or a single one if an ID is provided
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
