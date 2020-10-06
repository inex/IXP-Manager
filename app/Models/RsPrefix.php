<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * IXP\Models\RsPrefix
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $timestamp
 * @property string|null $prefix
 * @property int|null $protocol
 * @property int|null $irrdb
 * @property int|null $rs_origin
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereIrrdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereRsOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsPrefix whereTimestamp($value)
 * @mixin \Eloquent
 */
class RsPrefix extends Model
{
    /**
     * Map prefix acceptance types to summary functions
     * @var array Map prefix acceptance types to summary functions
     */
    public static $SUMMARY_TYPES_FNS = [
        'adv_acc'  => 'summaryRoutesAdvertisedAndAccepted',
        'adv_nacc' => 'summaryRoutesAdvertisedAndNotAccepted',
        'nadv_acc' => 'summaryRoutesNotAdvertisedButAcceptable'
    ];

    /**
     * Map prefix acceptance types to lookup functions
     * @var array Map prefix acceptance types to lookup functions
     */
    public static $ROUTES_TYPES_FNS = [
        'adv_acc'  => 'routesAdvertisedAndAccepted',
        'adv_nacc' => 'routesAdvertisedAndNotAccepted',
        'nadv_acc' => 'routesNotAdvertisedButAcceptable'
    ];

    /**
     * Get the the customer that own the rs prefix
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid' );
    }

}