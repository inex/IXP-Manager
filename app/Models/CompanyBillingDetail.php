<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * IXP\Models\CompanyBillingDetail
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail query()
 * @mixin \Eloquent
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
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereInvoiceEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereInvoiceMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail wherePurchaseOrderRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyBillingDetail whereVatRate($value)
 * @property-read \IXP\Models\Customer|null $customer
 */
class CompanyBillingDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_billing_detail';

    /**
     * Get the customer for the company billing detail
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'company_billing_details_id' );
    }
}
