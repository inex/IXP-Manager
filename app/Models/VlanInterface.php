<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\VlanInterface
 *
 * @property int $id
 * @property int|null $ipv4addressid
 * @property int|null $ipv6addressid
 * @property int|null $virtualinterfaceid
 * @property int|null $vlanid
 * @property int|null $ipv4enabled
 * @property string|null $ipv4hostname
 * @property int|null $ipv6enabled
 * @property string|null $ipv6hostname
 * @property int|null $mcastenabled
 * @property int|null $irrdbfilter
 * @property string|null $bgpmd5secret
 * @property string|null $ipv4bgpmd5secret
 * @property string|null $ipv6bgpmd5secret
 * @property int|null $maxbgpprefix
 * @property int|null $rsclient
 * @property int|null $ipv4canping
 * @property int|null $ipv6canping
 * @property int|null $ipv4monitorrcbgp
 * @property int|null $ipv6monitorrcbgp
 * @property int|null $as112client
 * @property int|null $busyhost
 * @property string|null $notes
 * @property int $rsmorespecifics
 * @property-read \IXP\Models\VirtualInterface $virtualInterface
 * @property-read \IXP\Models\Vlan $vlan
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface public()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereAs112client($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereBgpmd5secret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereBusyhost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4addressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4bgpmd5secret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4canping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4enabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4hostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv4monitorrcbgp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6addressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6bgpmd5secret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6canping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6enabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6hostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIpv6monitorrcbgp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereIrrdbfilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereMaxbgpprefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereMcastenabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereRsclient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereRsmorespecifics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereVirtualinterfaceid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\VlanInterface whereVlanid($value)
 * @mixin \Eloquent
 */
class VlanInterface extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vlaninterface';

    /**
     * Get the customer that owns the virtual interfaces.
     */
    public function virtualInterface()
    {
        return $this->belongsTo('IXP\Models\VirtualInterface', 'virtualinterfaceid');
    }

    /**
     * Get the vlan that holds the vlan interface.
     */
    public function vlan()
    {
        return $this->belongsTo('IXP\Models\Vlan', 'vlanid');
    }


    /**
     * See if a given protocol is enabled
     */
    public function protocolEnabled( int $p ): bool {
        return $p === 4 ? $this->ipv4enabled : $this->ipv6enabled;
    }

}
