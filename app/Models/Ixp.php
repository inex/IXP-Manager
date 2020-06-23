<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\Ixp
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp query()
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $address3
 * @property string|null $address4
 * @property string|null $country
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereAddress3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereAddress4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Ixp default()
 */
class Ixp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ixp';

    /**
     * Scope a query to only include route servers
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDefault( $query )
    {
        return $query->where('id', 1 )->first();
    }
}
