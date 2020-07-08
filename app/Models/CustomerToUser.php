<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use stdClass;

/**
 * IXP\Models\CustomerToUser
 *
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property int $privs
 * @property mixed|null $extra_attributes
 * @property string|null $last_login_date
 * @property string|null $last_login_from
 * @property string $created_at
 * @property string|null $last_login_via
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereExtraAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereLastLoginDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereLastLoginFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereLastLoginVia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser wherePrivs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereUserId($value)
 * @mixin \Eloquent
 */
class CustomerToUser extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Return an array of users with their last login time ordered from most recent to oldest. (DQL)
     *
     * As an example, an element of the returned array contains:
     *
     *     [0] => array(6) {
     *         ["attribute"] => string(18) "auth.last_login_at"
     *         ["lastlogin"] => string(10) "1338329771"
     *         ["username"]  => string(4) "auser"
     *         ["email"]     => string(12) "auser@example.com"
     *         ["cust_name"] => string(4) "INEX"
     *         ["cust_id"]   => string(2) "15"
     *     }
     *
     * @param stdClass $feParams
     *
     * @return array Users with their last login time ordered from most recent to oldest.
     */
    public static function getLastLoginsForFeList( stdClass $feParams ): array
    {
        return self::select( [
            'customer_to_users.last_login_date AS last_login_date',
            'customer_to_users.last_login_via AS last_login_via',
            'customer_to_users.id AS AS c2u_id',
            'user.id AS id',
            'user.username AS username',
            'user.email AS email',
            'cust.id AS cust_id',
            'cust.name AS cust_name'
        ] )
        ->join( 'user', 'user.id', 'customer_to_users.user_id' )
        ->join( 'cust', 'cust.id', 'customer_to_users.customer_id' )
        ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }
}