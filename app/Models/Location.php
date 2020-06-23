<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * IXP\Models\Location
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location query()
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property string|null $tag
 * @property string|null $address
 * @property string|null $nocphone
 * @property string|null $nocfax
 * @property string|null $nocemail
 * @property string|null $officephone
 * @property string|null $officefax
 * @property string|null $officeemail
 * @property string|null $notes
 * @property int|null $pdb_facility_id
 * @property string|null $city
 * @property string|null $country
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereNocemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereNocfax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereNocphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereOfficeemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereOfficefax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereOfficephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location wherePdbFacilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Location whereTag($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Cabinet[] $cabinets
 * @property-read int|null $cabinets_count
 */
class Location extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'location';

    /**
     * Get the switchers for the cabinet
     */
    public function cabinets(): HasMany
    {
        return $this->hasMany(Cabinet::class, 'locationid' );
    }

    /**
     * Gets a listing of location for dropdown
     *
     * @return Collection
     */
    public static function getListForDropdown(): Collection
    {
        return self::orderBy( 'name', 'asc' )->get();
    }

}
