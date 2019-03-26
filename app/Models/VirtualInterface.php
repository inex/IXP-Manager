<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\VirtualInterface
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $name
 * @property string|null $description
 * @property int|null $mtu
 * @property int|null $trunk
 * @property int|null $channelgroup
 * @property int $lag_framing
 * @property int $fastlacp
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereChannelgroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereFastlacp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereLagFraming($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereMtu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VirtualInterface whereTrunk($value)
 * @mixin \Eloquent
 * @property-read \IXP\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 */
class VirtualInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'virtualinterface';

    /**
     * Get the customer that owns the virtual interfaces.
     */
    public function customer()
    {
        return $this->belongsTo('IXP\Models\Customer', 'custid');
    }

    /**
     * Get the VLAN interfaces for the virtual interface
     */
    public function vlanInterfaces()
    {
        return $this->hasMany('IXP\Models\VlanInterface', 'virtualinterfaceid');
    }



}
