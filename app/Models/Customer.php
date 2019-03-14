<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\Customer
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $type
 * @property string|null $shortname
 * @property int|null $autsys
 * @property int|null $maxprefixes
 * @property string|null $peeringemail
 * @property string|null $nocphone
 * @property string|null $noc24hphone
 * @property string|null $nocfax
 * @property string|null $nocemail
 * @property string|null $nochours
 * @property string|null $nocwww
 * @property int|null $irrdb
 * @property string|null $peeringmacro
 * @property string|null $peeringpolicy
 * @property string|null $corpwww
 * @property string|null $datejoin
 * @property string|null $dateleave
 * @property int|null $status
 * @property int|null $activepeeringmatrix
 * @property \Illuminate\Support\Carbon|null $lastupdated
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property \Illuminate\Support\Carbon|null $created
 * @property int|null $company_registered_detail_id
 * @property int|null $company_billing_details_id
 * @property string|null $peeringmacrov6
 * @property string|null $abbreviatedName
 * @property string|null $MD5Support
 * @property int|null $reseller
 * @property int $isReseller
 * @property int $in_manrs
 * @property int $in_peeringdb
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereAbbreviatedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereActivepeeringmatrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereAutsys($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCompanyBillingDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCompanyRegisteredDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCorpwww($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereDatejoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereDateleave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereInManrs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereInPeeringdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereIrrdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereIsReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereLastupdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereLastupdatedby($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereMD5Support($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereMaxprefixes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNoc24hphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocfax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNochours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocwww($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringmacro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringmacrov6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringpolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereType($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer trafficking()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer current()
 */
class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'lastupdated';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'datejoin',
        'dateleave'
    ];


    const TYPE_FULL        = 1;
    const TYPE_ASSOCIATE   = 2;
    const TYPE_INTERNAL    = 3;
    const TYPE_PROBONO     = 4;

    public static $CUST_TYPES_TEXT = [
        self::TYPE_FULL          => 'Full',
        self::TYPE_ASSOCIATE     => 'Associate',
        self::TYPE_INTERNAL      => 'Internal',
        self::TYPE_PROBONO       => 'Pro-bono',
    ];


    const STATUS_NORMAL       = 1;
    const STATUS_NOTCONNECTED = 2;
    const STATUS_SUSPENDED    = 3;

    public static $CUST_STATUS_TEXT = [
        self::STATUS_NORMAL           => 'Normal',
        self::STATUS_NOTCONNECTED     => 'Not Connected',
        self::STATUS_SUSPENDED        => 'Suspended',
    ];


    /**
     * Scope a query to only include trafficking members.
     *
     * Not that the IXP's own internal customers are included in this.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTrafficking($query)
    {
        return $query->where('type', '!=', Customer::TYPE_ASSOCIATE );
    }

    /**
     * Scope a query to only include current members
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent($query)
    {
        return $query->where('datejoin', '<=', today() )
            ->where( function (\Illuminate\Database\Eloquent\Builder $query) {
                $query->whereNull( 'dateleave' )
                    ->orWhere( 'dateleave', '=', '0000-00-00' )
                    ->orWhere( 'dateleave', '>=', today() );
            });
    }



}
