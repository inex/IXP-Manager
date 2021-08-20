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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use IXP\Traits\Observable;

/**
 * IXP\Models\CompanyBillingDetail
 *
 * @property int $id
 * @property string|null $billingContactName
 * @property string|null $billingAddress1
 * @property string|null $billingAddress2
 * @property string|null $billingAddress3
 * @property string|null $billingTownCity
 * @property string|null $billingPostcode
 * @property string|null $billingCountry
 * @property string|null $billingEmail
 * @property string|null $billingTelephone
 * @property string|null $vatNumber
 * @property string|null $vatRate
 * @property int $purchaseOrderRequired
 * @property string|null $invoiceMethod
 * @property string|null $invoiceEmail
 * @property string|null $billingFrequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingAddress3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingPostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereBillingTownCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereInvoiceEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereInvoiceMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail wherePurchaseOrderRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereVatRate($value)
 * @mixin \Eloquent
 */
class CompanyBillingDetail extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_billing_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'billingContactName',
        'billingAddress1',
        'billingAddress2',
        'billingAddress3',
        'billingTownCity',
        'billingPostcode',
        'billingCountry',
        'billingEmail',
        'billingTelephone',
        'vatNumber',
        'vatRate',
        'purchaseOrderRequired',
        'invoiceMethod',
        'invoiceEmail',
        'billingFrequency',
    ];

    public const INVOICE_METHOD_EMAIL = 'EMAIL';
    public const INVOICE_METHOD_POST  = 'POST';

    public static $INVOICE_METHODS = [
        self::INVOICE_METHOD_EMAIL => 'Email',
        self::INVOICE_METHOD_POST  => 'Post'
    ];

    public const BILLING_FREQUENCY_MONTHLY    = 'MONTHLY';
    public const BILLING_FREQUENCY_2MONTHLY   = '2MONTHLY';
    public const BILLING_FREQUENCY_QUARTERLY  = 'QUARTERLY';
    public const BILLING_FREQUENCY_HALFYEARLY = 'HALFYEARLY';
    public const BILLING_FREQUENCY_ANNUALLY   = 'ANNUALLY';
    public const BILLING_FREQUENCY_NOBILLING  = 'NOBILLING';

    public static $BILLING_FREQUENCIES = [
        self::BILLING_FREQUENCY_MONTHLY    => 'Monthly',
        self::BILLING_FREQUENCY_2MONTHLY   => 'Every 2 Months',
        self::BILLING_FREQUENCY_QUARTERLY  => 'Quarterly',
        self::BILLING_FREQUENCY_HALFYEARLY => 'Half-Yearly',
        self::BILLING_FREQUENCY_ANNUALLY   => 'Annually',
        self::BILLING_FREQUENCY_NOBILLING  => 'No Billing'
    ];

    /**
     * Get the customer for the company billing detail
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'company_billing_details_id' );
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
            "Billing Detail [id:%d] belonging to %s [id:%d] '%s'",
            $model->id,
            config( 'ixp_fe.lang.customer.one' ),
            $model->customer->id ?? null,
            $model->customer->shortname ?? null
        );
    }
}
