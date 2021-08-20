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
    Model,
    Relations\HasOne
};

use IXP\Traits\Observable;

/**
 * IXP\Models\CompanyRegisteredDetail
 *
 * @property int $id
 * @property string|null $registeredName
 * @property string|null $companyNumber
 * @property string|null $jurisdiction
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $address3
 * @property string|null $townCity
 * @property string|null $postcode
 * @property string|null $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereAddress3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereCompanyNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereJurisdiction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereRegisteredName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereTownCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyRegisteredDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompanyRegisteredDetail extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_registration_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registeredName',
        'companyNumber',
        'jurisdiction',
        'address1',
        'address2',
        'address3',
        'townCity',
        'postcode',
        'country',
    ];

    /**
     * Get the customer for the company registered detail
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'company_registered_detail_id' );
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
            "Registered Detail [id:%d] belonging to %s [id:%d] '%s'",
            $model->id,
            config( 'ixp_fe.lang.customer.one' ),
            $model->customer->id ?? null,
            $model->customer->shortname ?? null
        );
    }
}
