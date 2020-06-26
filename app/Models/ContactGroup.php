<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use stdClass;

/**
 * IXP\Models\ContactGroup
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property int $active
 * @property int $limited_to
 * @property string $created
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup whereLimitedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\ContactGroup whereType($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 */
class ContactGroup extends Model
{
    public const TYPE_ROLE = 'ROLE';

    public static $TYPES = [
        self::TYPE_ROLE => 'Role'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_group';

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
        'name',
        'description',
        'type',
        'active',
        'limited_to',
        'created',
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class )->withPivot( 'contact_to_group', 'contact_group_id' );
    }

    /**
     * Gets a listing of contact groups or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
        return self::when( $id , function( Builder $q, $id ) {
            return $q->where('id', $id );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->when( $types = config( "contact_group.types" ) , function( Builder $q, $types ) {
            return $q->whereIn('type', array_keys( $types ) );
        } )->get()->toArray();
    }

    /**
     * Get contact group names as an array grouped by group type.
     *
     * Returned array structure:
     *
     *     $arr = [
     *         'ROLE' => [
     *              [ 'id' => 1, 'name' => 'Billing' ],
     *              [ 'id' => 2, 'name' => 'Admin']
     *         ]
     *         'OTHER' => [
     *              [ 'id' => n, 'name' => 'Other group' ]
     *         ]
     *     ];
     *
     *
     * @param string|null  $type   Optionally limit to a specific type
     * @param int|null  $cid    Contact id to filter for a particular contact
     * @param bool      $active Filter active
     *
     * @return array
     */
    public static function getGroupNamesTypeArray( string $type = null, int $cid = null, bool $active = false ): array
    {
        $result = self::when( $cid , function( Builder $q, $cid ) {
            return $q->leftJoin( 'contact_to_group', function( $join ) use( $cid ) {
                $join->on( 'contact_group.id', '=', 'contact_to_group.contact_group_id')
                    ->where('contact_to_group.contact_id','=', $cid );
            });
        })->when( $type , function( Builder $q, $type ) {
            return $q->where( 'type', $type );
        })->when( $active , function( Builder $q, $active ) {
            return $q->where('active', $active );
        } )->get();

        $groups = [];

        foreach( $result as $r ){
            $groups[ $r->type ][ $r->id ] = [ 'id' => $r->id, 'name' => $r->name ];
        }

        return $groups;
    }
}
