<?php
  $c = $t->c; /** @var \IXP\Models\Customer $c */
  $rdetails = $c->companyRegisteredDetail; /** @var \IXP\Models\CompanyRegisteredDetail $rdetails */
?>

<div class="d-flex row">
    <div class="col-md-6">
        <div class="tw-rounded-sm tw-p-4 tw-shadow-md tw-border-1 tw-border-grey-light">
            <header class="tw-pb-2 tw-pl-2 tw-flex tw-border-b-1 tw-border-gray-300">
                <h3>
                    Registration Details
                </h3>
            </header>
            <table class="table ">
                <tr>
                    <th>
                        Registered Name
                    </th>
                    <td>
                        <?= $t->ee( $rdetails->registeredName ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Company Number
                    </th>
                    <td>
                        <?= $t->ee( $rdetails->companyNumber ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Jurisdiction
                    </th>
                    <td>
                        <?= $t->ee( $rdetails->jurisdiction ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Address
                    </th>
                    <td>
                        <?php if( $rdetails->address1 ): ?><?= $t->ee( $rdetails->address1 ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->address2 ): ?><?= $t->ee( $rdetails->address2 ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->address3 ): ?><?= $t->ee( $rdetails->address3 ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->townCity ): ?><?= $t->ee( $rdetails->townCity ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->postcode ): ?><?= $t->ee( $rdetails->postcode ) ?><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Country
                    </th>
                    <td>
                        <?= $rdetails->country ? array_column( Countries::getList(), 'name', 'iso_3166_2')[ $rdetails->country ] : null ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <?php if( !config('ixp.reseller.no_billing') && !$c->reseller ): ?>
        <div class="tw-rounded-sm tw-p-4 tw-shadow-md tw-border-1 tw-border-grey-light">
            <header class="tw-pb-2 tw-pl-2 tw-flex tw-border-b-1 tw-border-gray-300">
                <h3>
                    Billing Details
                </h3>
            </header>
            <?php $bdetails = $c->companyBillingDetail /** @var $bdetails \IXP\Models\CompanyBillingDetail  */ ?>
            <table class="table">
                <tr>
                    <th>
                        Contact Name
                    </th>
                    <td>
                        <?= $t->ee( $bdetails->billingContactName ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        VAT Number
                    </th>
                    <td>
                        <?= $t->ee( $bdetails->vatNumber ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        VAT Rate
                    </th>
                    <td>
                        <?= $t->ee( $bdetails->vatRate ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Billing Period
                    </th>
                    <td>
                        <?= \IXP\Models\CompanyBillingDetail::$BILLING_FREQUENCIES[ $bdetails->billingFrequency ] ?? null ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        E-Mail
                    </th>
                    <td>
                        <?= $t->ee( $bdetails->billingEmail ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Address
                    </th>
                    <td>
                        <?php if( $bdetails->billingAddress1 ): ?> <?= $t->ee( $bdetails->billingAddress1 ) ?><br/><?php endif; ?>
                        <?php if( $bdetails->billingAddress2 ): ?> <?= $t->ee( $bdetails->billingAddress2 ) ?><br/><?php endif; ?>
                        <?php if( $bdetails->billingAddress3 ): ?> <?= $t->ee( $bdetails->billingAddress3 ) ?><br/><?php endif; ?>
                        <?php if( $bdetails->billingTownCity ): ?> <?= $t->ee( $bdetails->billingTownCity ) ?><br/><?php endif; ?>
                        <?php if( $bdetails->billingPostcode ): ?> <?= $t->ee( $bdetails->billingPostcode ) ?><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Country
                    </th>
                    <td>
                        <?= $bdetails->billingCountry ? array_column( Countries::getList(), 'name', 'iso_3166_2')[ $bdetails->billingCountry ] : null ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Phone
                    </th>
                    <td>
                        <?= $t->ee( $bdetails->billingTelephone ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        P/O Required
                    </th>
                    <td>
                        <?= $bdetails->purchaseOrderRequired ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'  ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Invoice Method
                    </th>
                    <td>
                        <?= \IXP\Models\CompanyBillingDetail::$INVOICE_METHODS[ $bdetails->invoiceMethod ] ?? null ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Invoice E-Mail
                    </th>
                    <td>
                        <?= $t->ee( $bdetails->invoiceEmail ) ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>