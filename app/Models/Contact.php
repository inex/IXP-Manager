<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Auth, stdClass;
use Illuminate\Support\Collection;

/**
 * IXP\Models\Contact
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact query()
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $custid
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile
 * @property int $facilityaccess
 * @property int $mayauthorize
 * @property string|null $lastupdated
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property string|null $created
 * @property string|null $position
 * @property string|null $notes
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereFacilityaccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereLastupdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereLastupdatedby($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereMayauthorize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact wherePosition($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ContactGroup[] $contactGroups
 * @property-read int|null $contact_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ContactGroup[] $contactGroupsAll
 * @property-read int|null $contact_groups_all_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ContactGroup[] $contactRoles
 * @property-read int|null $contact_roles_count
 * @property-read \IXP\Models\Customer|null $customer
 */
class Contact extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact';

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
        'custid',
        'name',
        'email',
        'phone',
        'mobile',
        'facilityaccess',
        'mayauthorize',
        'lastupdated',
        'lastupdatedby',
        'creator',
        'created',
        'position',
        'notes',
    ];

    /**
     * Get the contact groups that are type role for the contact
     */
    public function contactRoles(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->where( 'type', '=', ContactGroup::TYPE_ROLE )
            ->orderBy( 'name', 'asc' );
    }

    /**
     * Get the contact groups that are not type role for the contact
     */
    public function contactGroups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->where( 'type', '!=', ContactGroup::TYPE_ROLE )
            ->orderBy( 'name', 'asc' );
    }

    /**
     * Get all the contact groups for the contact
     */
    public function contactGroupsAll(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->orderBy( 'name', 'asc' );
    }

    /**
     * Get the customer that own the contact
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid' );
    }


    /**
     * Gets a listing of contacts or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     * @param int|null $role
     * @param int|null $cgid
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null, int $role = null, int $cgid = null ): array
    {
        $query = self::select( [ 'contact.*', 'cust.name AS customer', 'cust.id AS custid' ])
            ->leftJoin( 'cust', 'cust.id', '=' , 'contact.custid'  )
            ->when( $id , function ( Builder $query, $id ) {
                return $query->where('contact.id', $id );
            })
            ->when( !Auth::getUser()->isSuperUser(), function ( Builder $query ) {
                return $query->where('cust.id', Auth::getUser()->getCustomer()->getId() );
            })
            ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            });

        if( config('contact_group.types.ROLE') ) {
            $groupid = $role ? $role : ( $cgid ?: null);
            $query->when( $groupid , function ( Builder $query, $groupid ) {
                return $query->leftJoin( 'contact_to_group', function( $join ) {
                    $join->on( 'contact.id', '=', 'contact_to_group.contact_id');
                })->where('contact_to_group.contact_group_id','=', $groupid );
            });

            if( Auth::getUser()->isSuperUser() ) {
                $query->with( 'contactRoles', 'contactGroups' );
            }
        }

        return $query->get()->toArray();
    }

    /**
     * Gets a listing of contacts or a single one if an ID is provided
     *
     * @param int $custid
     * @param int|null $groupid
     *
     * @return Collection
     */
    public static function getForCustomer( int $custid, int $groupid ): Collection
    {
        return self::when( $groupid , function ( Builder $query, $groupid ) {
            return $query->leftJoin( 'contact_to_group', function( $join ) {
                $join->on( 'contact.id', '=', 'contact_to_group.contact_id');
            })->where('contact_to_group.contact_group_id','=', $groupid );
        })
            ->where( 'custid', $custid  )
            ->get();
    }
}
