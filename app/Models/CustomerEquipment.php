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
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @method static Builder|CustomerEquipment newModelQuery()
 * @method static Builder|CustomerEquipment newQuery()
 * @method static Builder|CustomerEquipment query()
 * @method static Builder|CustomerEquipment whereCabinetid($value)
 * @method static Builder|CustomerEquipment whereCustid($value)
 * @method static Builder|CustomerEquipment whereDescr($value)
 * @method static Builder|CustomerEquipment whereId($value)
 * @method static Builder|CustomerEquipment whereName($value)
 * @mixin \Eloquent
 */
class CustomerEquipment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'custkit';

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
        'cabinetid',
        'name',
        'descr',
    ];

    /**
     * Get the cabinet that own the customer equipment
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinetid' );
    }

    /**
     * Gets a listing of customer equipment list or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     *
     * @return array
     */
    public static function getFeList( stdClass $feParams, int $id = null ): array
    {
         return self::select( [ 'custkit.*', 'cabinet.name AS cabinet', 'cust.name as customer' ] )
                ->leftJoin( 'cabinet', 'cabinet.id', 'custkit.cabinetid' )
                ->leftJoin( 'cust', 'cust.id', 'custkit.custid' )
                ->when( $id , function( Builder $q, $id ) {
                return $q->where('id', $id );
                } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                    return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
                })->get()->toArray();
    }
}