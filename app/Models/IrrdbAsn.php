<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\IrrdbAsn
 *
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $customer_id
 * @property int $asn
 * @property int $protocol
 * @property string|null $first_seen
 * @property string|null $last_seen
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereFirstSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereProtocol($value)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IrrdbAsn whereUpdatedAt($value)
 */
class IrrdbAsn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'irrdb_asn';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'asn',
        'protocol',
        'first_seen',
        'last_seen',
    ];

}
