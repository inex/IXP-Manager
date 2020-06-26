<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use stdClass;

/**
 * IXP\Models\ApiKey
 *
 * @property int $id
 * @property int $user_id
 * @property string $apiKey
 * @property string|null $expires
 * @property string|null $allowedIPs
 * @property string $created
 * @property string|null $lastseenAt
 * @property string|null $lastseenFrom
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereAllowedIPs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereLastseenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereLastseenFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ApiKey whereUserId($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\User $user
 */
class ApiKey extends Model
{
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
        'user_id',
        'apiKey',
        'expires',
        'allowedIPs',
        'created',
        'lastseenAt',
        'lastseenFrom',
        'description'
    ];

    /**
     * Get the customer
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class );
    }

    /**
     * Gets a listing of apikeys list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int $userid
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $userid, int $id = null ): array
    {
        return self::where( 'user_id', $userid )
            ->when( $id , function( Builder $q, $id ) {
                return $q->where('id', $id );
            } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            })->get()->toArray();
    }
}
