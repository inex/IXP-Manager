<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use stdClass;

/**
 * IXP\Models\CustomerTag
 *
 * @property int $id
 * @property string $tag
 * @property string $display_as
 * @property string|null $description
 * @property int $internal_only
 * @property \Illuminate\Support\Carbon $created
 * @property \Illuminate\Support\Carbon $updated
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag whereDisplayAs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag whereInternalOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerTag whereUpdated($value)
 * @mixin \Eloquent
 */
class CustomerTag extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_tag';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag',
        'display_as',
        'description',
        'internal_only',
    ];


    /**
     * Gets a listing of customer tag list or a single one if an ID is provided
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
