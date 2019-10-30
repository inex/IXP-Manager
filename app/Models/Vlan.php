<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\Vlan
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $number
 * @property string|null $notes
 * @property int $private
 * @property int $infrastructureid
 * @property int $peering_matrix
 * @property int $peering_manager
 * @property string|null $config_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereConfigName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereInfrastructureid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan wherePeeringManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan wherePeeringMatrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Vlan wherePrivate($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Router[] $routers
 * @property-read int|null $routers_count
 * @property-read int|null $vlan_interfaces_count
 */
class Vlan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlan';

    /**
     * Get the vlan interfaces that are in this vlan
     */
    public function vlanInterfaces()
    {
        return $this->hasMany('IXP\Models\VlanInterface', 'vlanid');
    }

    /**
     * Get the vlan interfaces that are in this vlan
     */
    public function routers()
    {
        return $this->hasMany('IXP\Models\Router' );
    }

}
