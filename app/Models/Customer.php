<?php

namespace IXP\Models;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM, Eloquent;

use Entities\Customer as CustomerEntity;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};

use Illuminate\Support\Carbon;

use IXP\Exceptions\GeneralException as IXP_Exception;

/**
 * IXP\Models\Customer
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $type
 * @property string|null $shortname
 * @property int|null $autsys
 * @property int|null $maxprefixes
 * @property string|null $peeringemail
 * @property string|null $nocphone
 * @property string|null $noc24hphone
 * @property string|null $nocfax
 * @property string|null $nocemail
 * @property string|null $nochours
 * @property string|null $nocwww
 * @property int|null $irrdb
 * @property string|null $peeringmacro
 * @property string|null $peeringpolicy
 * @property string|null $corpwww
 * @property \Illuminate\Support\Carbon|null $datejoin
 * @property \Illuminate\Support\Carbon|null $dateleave
 * @property int|null $status
 * @property int|null $activepeeringmatrix
 * @property \Illuminate\Support\Carbon|null $lastupdated
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property \Illuminate\Support\Carbon|null $created
 * @property int|null $company_registered_detail_id
 * @property int|null $company_billing_details_id
 * @property string|null $peeringmacrov6
 * @property string|null $abbreviatedName
 * @property string|null $MD5Support
 * @property int|null $reseller
 * @property int $isReseller
 * @property int $in_manrs
 * @property int $in_peeringdb
 * @property int $peeringdb_oauth
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VirtualInterface[] $virtualInterfaces
 * @property-read int|null $virtual_interfaces_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer current()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer trafficking()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereAbbreviatedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereActivepeeringmatrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereAutsys($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCompanyBillingDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCompanyRegisteredDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCorpwww($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereDatejoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereDateleave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereInManrs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereInPeeringdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereIrrdb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereIsReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereLastupdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereLastupdatedby($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereMD5Support($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereMaxprefixes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNoc24hphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocfax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNochours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereNocwww($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringdbOauth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringemail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringmacro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringmacrov6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer wherePeeringpolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Customer whereType($value)
 * @mixin \Eloquent
 */
class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'lastupdated';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'datejoin',
        'dateleave'
    ];


    const TYPE_FULL        = 1;
    const TYPE_ASSOCIATE   = 2;
    const TYPE_INTERNAL    = 3;
    const TYPE_PROBONO     = 4;

    public static $CUST_TYPES_TEXT = [
        self::TYPE_FULL          => 'Full',
        self::TYPE_ASSOCIATE     => 'Associate',
        self::TYPE_INTERNAL      => 'Internal',
        self::TYPE_PROBONO       => 'Pro-bono',
    ];


    const STATUS_NORMAL       = 1;
    const STATUS_NOTCONNECTED = 2;
    const STATUS_SUSPENDED    = 3;

    public static $CUST_STATUS_TEXT = [
        self::STATUS_NORMAL           => 'Normal',
        self::STATUS_NOTCONNECTED     => 'Not Connected',
        self::STATUS_SUSPENDED        => 'Suspended',
    ];


    /**
     * Get the virtual interfaces for the customer
     */
    public function virtualInterfaces()
    {
        return $this->hasMany('IXP\Models\VirtualInterface', 'custid');
    }


    /**
     * Scope a query to only include trafficking members.
     *
     * Not that the IXP's own internal customers are included in this.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTrafficking($query)
    {
        return $query->where('type', '!=', Customer::TYPE_ASSOCIATE );
    }

    /**
     * Scope a query to only include current members
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCurrent($query)
    {
        return $query->where('datejoin', '<=', today() )
            ->where( function ( Builder $query) {
                $query->whereNull( 'dateleave' )
                    ->orWhere( 'dateleave', '=', '0000-00-00' )
                    ->orWhere( 'dateleave', '>=', today() );
            });
    }



    /**
     * Get formatted name
     *
     * @return string
     */
    public function getFormattedName( $fmt = null )
    {
        if( $this->type === self::TYPE_ASSOCIATE ) {
            return $this->abbreviatedName;
        }

        if( $fmt === null || ( $fmt = config('ixp_fe.customer_name_format') ) === null ) {
            $fmt = "%a %j";
        }

        $as = $this->autsys ? $this->autsys : false;

        return str_replace(
            [ '%n', '%a', '%s', '%i', '%j', '%k', '%l' ],
            [
                $this->name,
                $this->abbreviatedName,
                $this->shortname,
                $as ? $as          : '',
                $as ? "[AS{$as}]"  : '',
                $as ? "AS{$as}"    : '',
                $as ? " - AS{$as}" : ''
            ],
            $fmt
        );
    }

    /**
     * Is the customer a route server client on any of their VLAN interfaces?
     * @param int $proto One of [4,6]. Defaults to 4.
     * @return boolean
     * @throws IXP_Exception
     */
    public function isRouteServerClient( int $proto = 4 ): bool
    {
        if( !in_array( $proto, [ 4, 6 ] ) ) {
            throw new IXP_Exception( 'Invalid protocol' );
        }

        foreach( $this->virtualInterfaces as $vi ) {
            foreach( $vi->vlanInterfaces as $vli ) {
                if( $vli->protocolEnabled( $proto ) && $vli->rsclient ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Is the customer IRRDB filtered (usually for route server clients) on any of their VLAN interfaces?
     * @return boolean
     */
    public function isIrrdbFiltered(): bool
    {
        foreach( $this->virtualInterfaces as $vi ) {
            foreach( $vi->vlanInterfaces as $vli ) {
                if( $vli->irrdbfilter ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Is the customer IPvX enabled on any of their VLAN interfaces?
     * @param int $proto One of [4,6]. Defaults to 4.
     * @return boolean
     * @throws IXP_Exception
     */
    public function isIPvXEnabled( int $proto = 4 ): bool
    {
        if( !in_array( $proto, [ 4, 6 ] ) ) {
            throw new IXP_Exception( 'Invalid protocol' );
        }

        foreach( $this->virtualInterfaces as $vi ) {
            foreach( $vi->vlanInterfaces as $vli ) {
                if( $vli->protocolEnabled( $proto ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * return the doctrine entity
     *
     * @return object|CustomerEntity
     */
    public function getDoctrineObject(): CustomerEntity {
        return D2EM::getRepository( CustomerEntity::class )->find( $this->id );
    }

}
