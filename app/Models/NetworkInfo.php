<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use stdClass;

/**
 * IXP\Models\NetworkInfo
 *
 * @property int $id
 * @property int|null $vlanid
 * @property int|null $protocol
 * @property string|null $network
 * @property int|null $masklen
 * @property string|null $rs1address
 * @property string|null $rs2address
 * @property string|null $dnsfile
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereDnsfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereMasklen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereNetwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereRs1address($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereRs2address($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\NetworkInfo whereVlanid($value)
 * @mixin \Eloquent
 */
class NetworkInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'networkinfo';

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
        'vlanid',
        'protocol',
        'network',
        'masklen',
    ];

    /**
     * Gets a listing of mailing list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        return self::select( [ 'networkinfo.*', 'vlan.id AS vlan_id', 'vlan.name AS vlanname' ] )
            ->leftJoin( 'vlan', 'vlan.id','networkinfo.vlanid' )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where( 'networkinfo.id', $id );
            })
            ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })
            ->get()->toArray();
    }
}