<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use stdClass;

/**
 * IXP\Models\CustomerEquipment
 *
 * @property int $id
 * @property int|null $custid
 * @property int|null $cabinetid
 * @property string|null $name
 * @property string|null $descr
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment whereCabinetid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment whereDescr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerEquipment whereName($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\Cabinet $cabinet
 */
class CustomerEquipment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'custkit';

    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'custid',
        'cabinetid',
        'name',
        'descr',
    ];

    /**
     * Get the cabinet that own the customerequipment
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class );
    }

    /**
     * Gets a listing of customer equipment list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return Collection
     */
    public static function getFeList( stdClass $feParams, int $id = null ): Collection
    {
        $query = self::select( [ 'custkit.*', 'cabinet.name AS cabinet', 'cust.name as customer' ] )
                ->leftJoin( 'cabinet', 'cabinet.id', '=', 'custkit.cabinetid' )
                ->leftJoin( 'cust', 'cust.id', '=', 'custkit.custid' )
                ->when( $id , function( Builder $q, $id ) {
                return $q->where('id', $id );
                } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                    return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
                });

        return $query->get();
    }
}
