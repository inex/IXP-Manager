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
 * @property-read \IXP\Models\User $user
 * @method static Builder|ApiKey newModelQuery()
 * @method static Builder|ApiKey newQuery()
 * @method static Builder|ApiKey query()
 * @method static Builder|ApiKey whereAllowedIPs($value)
 * @method static Builder|ApiKey whereApiKey($value)
 * @method static Builder|ApiKey whereCreated($value)
 * @method static Builder|ApiKey whereDescription($value)
 * @method static Builder|ApiKey whereExpires($value)
 * @method static Builder|ApiKey whereId($value)
 * @method static Builder|ApiKey whereLastseenAt($value)
 * @method static Builder|ApiKey whereLastseenFrom($value)
 * @method static Builder|ApiKey whereUserId($value)
 * @mixin \Eloquent
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
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id' );
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
