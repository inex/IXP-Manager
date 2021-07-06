<?php

namespace IXP\Models;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo
};

use IXP\Traits\Observable;

/**
 * IXP\Models\NetworkInfo
 *
 * @property int $id
 * @property int|null $vlanid
 * @property int|null $protocol
 * @property string|null $network
 * @property int|null $masklen
 * @property string|null $rs1address
 * @property string|null $rs2address
 * @property string|null $dnsfile
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Vlan|null $vlan
 * @method static Builder|NetworkInfo newModelQuery()
 * @method static Builder|NetworkInfo newQuery()
 * @method static Builder|NetworkInfo query()
 * @method static Builder|NetworkInfo whereCreatedAt($value)
 * @method static Builder|NetworkInfo whereDnsfile($value)
 * @method static Builder|NetworkInfo whereId($value)
 * @method static Builder|NetworkInfo whereMasklen($value)
 * @method static Builder|NetworkInfo whereNetwork($value)
 * @method static Builder|NetworkInfo whereProtocol($value)
 * @method static Builder|NetworkInfo whereRs1address($value)
 * @method static Builder|NetworkInfo whereRs2address($value)
 * @method static Builder|NetworkInfo whereUpdatedAt($value)
 * @method static Builder|NetworkInfo whereVlanid($value)
 * @mixin \Eloquent
 */
class NetworkInfo extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'networkinfo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vlanid',
        'protocol',
        'network',
        'masklen',
    ];

    /**
     * Get the vlan that own the network info
     */
    public function vlan(): BelongsTo
    {
        return $this->belongsTo(Vlan::class, 'vlanid' );
    }

    /**
     * Returns an array of the network information indexed by Vlan.id with
     * sub-arrays indexed by protocol.
     *
     * For example (where `x` is the vlan ID):
     *
     *     [x] => array(2) {
     *       [4] => array(9) {
     *         ["id"] => string(1) "1"
     *           ["protocol"] => string(1) "4"
     *           ["network"] => string(13) "193.242.111.0"
     *           ["masklen"] => string(2) "25"
     *           ["rs1address"] => string(13) "193.242.111.8"
     *           ["rs2address"] => string(13) "193.242.111.9"
     *           ["dnsfile"] => string(44) "/opt/bind/zones/reverse-vlan-10-ipv4.include"
     *           ["Vlan"] => array(5) {
     *             ["id"] => string(1) "2"
     *             ["name"] => string(15) "Peering VLAN #1"
     *             ["number"] => string(2) "10"
     *             ["rcvrfname"] => string(0) ""
     *             ["notes"] => string(0) ""
     *           }
     *       }
     *       [6] => array(9) {
     *         ["id"] => string(1) "2"
     *           ["vlanid"] => string(1) "2"
     *           ["protocol"] => string(1) "6"
     *           ["network"] => string(16) "2001:07F8:0018::"
     *           ["masklen"] => string(2) "64"
     *           ["rs1address"] => string(14) "2001:7f8:18::8"
     *           ["rs2address"] => string(14) "2001:7f8:18::9"
     *           ["dnsfile"] => string(44) "/opt/bind/zones/reverse-vlan-10-ipv6.include"
     *           ["Vlan"] => array(5) {
     *             ["id"] => string(1) "2"
     *             ["name"] => string(15) "Peering VLAN #1"
     *             ["number"] => string(2) "10"
     *             ["rcvrfname"] => string(0) ""
     *             ["notes"] => string(0) ""
     *           }
     *         }
     *     }
     *
     * @return array As described above
     */
    public static function vlanProtocol(): array
    {
        $result = [];
        foreach( self::with( 'vlan' )->get()->toArray() as $ni ) {
            $result[ $ni[ 'vlan' ][ 'id' ] ][ $ni[ 'protocol' ] ] = $ni;
        }

        return $result;
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Network Info [id:%d] '%s' belonging to Vlan [id:%d] '%s'",
            $model->id,
            $model->network,
            $model->vlanid,
            $model->vlan->name,
        );
    }
}