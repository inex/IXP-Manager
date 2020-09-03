<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\UserLoginHistory
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $ip
 * @property string $at
 * @property int|null $customer_to_user_id
 * @property string|null $via
 * @method static Builder|UserLoginHistory newModelQuery()
 * @method static Builder|UserLoginHistory newQuery()
 * @method static Builder|UserLoginHistory query()
 * @method static Builder|UserLoginHistory whereAt($value)
 * @method static Builder|UserLoginHistory whereCustomerToUserId($value)
 * @method static Builder|UserLoginHistory whereId($value)
 * @method static Builder|UserLoginHistory whereIp($value)
 * @method static Builder|UserLoginHistory whereUserId($value)
 * @method static Builder|UserLoginHistory whereVia($value)
 * @mixin \Eloquent
 */
class UserLoginHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_logins';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Gets a listing of user login histories or a single one if an ID is provided
     *
     * @param int|null $userid
     * @param int $limit
     *
     * @return array
     */
    public static function getFeList( int $userid = null , int $limit = 0 ): array
    {
        return self::select( [ 'user_logins.*', 'user.id AS user_id', 'cust.name AS cust_name' ] )
        ->leftJoin( 'customer_to_users', 'customer_to_users.id', 'user_logins.customer_to_user_id' )
        ->leftJoin( 'cust', 'cust.id', 'customer_to_users.customer_id' )
        ->leftJoin( 'user', 'user.id', 'customer_to_users.user_id' )
        ->when( $userid , function( Builder $q, $userid ) {
            return $q->where( 'user.id', $userid );
        })
        ->when( $limit > 0 , function( Builder $q, $l ) use( $limit ) {
            return $q->limit( $limit );
        })
        ->orderBy( 'at', 'DESC' )
        ->get()->toArray();
    }
}