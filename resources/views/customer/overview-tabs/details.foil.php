<div class="row">
    <div class="col-sm-12">
        <br>
        <div class="col-sm-6">
            <?php $rdetails = $t->c->getRegistrationDetails() ?>
            <h3>Registration Details</h3>
            <table class="table">
                <tr>
                    <th>Registered Name</th>
                    <td><?= $rdetails->getRegisteredName() ?></td>
                </tr>
                <tr>
                    <th>Company Number</th>
                    <td><?= $rdetails->getCompanyNumber() ?></td>
                </tr>
                <tr>
                    <th>Jurisdiction</th>
                    <td><?= $rdetails->getJurisdiction() ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>
                        <?php if( $rdetails->getAddress1() ): ?><?= $rdetails->getAddress1() ?><br/><?php endif; ?>
                        <?php if( $rdetails->getAddress2() ): ?><?= $rdetails->getAddress2() ?><br/><?php endif; ?>
                        <?php if( $rdetails->getAddress3() ): ?><?= $rdetails->getAddress3() ?><br/><?php endif; ?>
                        <?php if( $rdetails->getTownCity() ): ?><?= $rdetails->getTownCity() ?><br/><?php endif; ?>
                        <?php if( $rdetails->getPostcode() ): ?><?= $rdetails->getPostcode() ?><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td><?= $rdetails->getCountryName() ?></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-6">
            <?php if ( ( config('ixp.reseller.reseller') !== null || !config('ixp.reseller.reseller') ) || !$t->resellerMode || !$t->c->isResoldCustomer() ): ?>
                <h3>Billing Details</h3>
                <?php $bdetails = $t->c->getBillingDetails() ?>
                <table class="table">
                    <tr>
                        <th>Contact Name</th>
                        <td><?= $bdetails->getBillingContactName() ?></td>
                    </tr>
                    <tr>
                        <th>VAT Number</th>
                        <td><?= $bdetails->getVatNumber() ?></td>
                    </tr>
                    <tr>
                        <th>VAT Rate</th>
                        <td><?= $bdetails->getVatRate() ?></td>
                    </tr>
                    <tr>
                        <th>Billing Period</th>
                        <td>
                            <?php if( $bdetails->getBillingFrequency() != '' ): ?>
                                <?php if( isset( \Entities\CompanyBillingDetail::$BILLING_FREQUENCIES[$bdetails->getBillingFrequency()] ) ): ?>
                                    <?= \Entities\CompanyBillingDetail::$BILLING_FREQUENCIES[ $bdetails->getBillingFrequency() ] ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>E-Mail</th>
                        <td><?= $bdetails->getBillingEmail() ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>
                            <?php if( $bdetails->getBillingAddress1() ): ?> <?= $bdetails->getBillingAddress1() ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingAddress2() ): ?> <?= $bdetails->getBillingAddress2() ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingAddress3() ): ?> <?= $bdetails->getBillingAddress3() ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingTownCity() ): ?> <?= $bdetails->getBillingTownCity() ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingPostcode() ): ?> <?= $bdetails->getBillingPostcode() ?><?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td><?= $bdetails->getBillingCountryName() ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?= $bdetails->getBillingTelephone() ?></td>
                    </tr>
                    <tr>
                        <th>P/O Required</th>
                        <td><?php if( $bdetails->getPurchaseOrderRequired() ): ?> <i class="icon-ok"></i><?php else: ?><i class="icon-remove"></i><?php endif; ?></td>
                    </tr>
                    <tr>
                        <th>Invoice Method</th>
                        <td>
                            <?php if( $bdetails->getInvoiceMethod() != '' ): ?>
                                <?php if( isset( \Entities\CompanyBillingDetail::$INVOICE_METHODS[ $bdetails->getInvoiceMethod() ] ) ): ?>
                                    <?= \Entities\CompanyBillingDetail::$INVOICE_METHODS[ $bdetails->getInvoiceMethod() ] ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Invoice E-Mail</th>
                        <td><?= $bdetails->getInvoiceEmail() ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>