<div class="row">
    <div class="col-sm-12">
        <br>
        <div class="col-sm-6">
            <?php $rdetails = $t->c->getRegistrationDetails() ?>
            <h3>Registration Details</h3>
            <table class="table">
                <tr>
                    <th>Registered Name</th>
                    <td><?= $t->ee( $rdetails->getRegisteredName() ) ?></td>
                </tr>
                <tr>
                    <th>Company Number</th>
                    <td><?= $t->ee( $rdetails->getCompanyNumber() ) ?></td>
                </tr>
                <tr>
                    <th>Jurisdiction</th>
                    <td><?= $t->ee( $rdetails->getJurisdiction() ) ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>
                        <?php if( $rdetails->getAddress1() ): ?><?= $t->ee( $rdetails->getAddress1() ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->getAddress2() ): ?><?= $t->ee( $rdetails->getAddress2() ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->getAddress3() ): ?><?= $t->ee( $rdetails->getAddress3() ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->getTownCity() ): ?><?= $t->ee( $rdetails->getTownCity() ) ?><br/><?php endif; ?>
                        <?php if( $rdetails->getPostcode() ): ?><?= $t->ee( $rdetails->getPostcode() ) ?><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td><?= $t->ee( $rdetails->getCountryName() ) ?></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-6">
            <?php if( !config('ixp.reseller.no_billing') || !$t->resellerMode || !$t->c->isResoldCustomer() ): ?>
                <h3>Billing Details</h3>
                <?php $bdetails = $t->c->getBillingDetails() ?>
                <table class="table">
                    <tr>
                        <th>Contact Name</th>
                        <td><?= $t->ee( $bdetails->getBillingContactName() ) ?></td>
                    </tr>
                    <tr>
                        <th>VAT Number</th>
                        <td><?= $t->ee( $bdetails->getVatNumber() ) ?></td>
                    </tr>
                    <tr>
                        <th>VAT Rate</th>
                        <td><?= $t->ee( $bdetails->getVatRate() ) ?></td>
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
                        <td><?= $t->ee( $bdetails->getBillingEmail() ) ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>
                            <?php if( $bdetails->getBillingAddress1() ): ?> <?= $t->ee( $bdetails->getBillingAddress1() ) ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingAddress2() ): ?> <?= $t->ee( $bdetails->getBillingAddress2() ) ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingAddress3() ): ?> <?= $t->ee( $bdetails->getBillingAddress3() ) ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingTownCity() ): ?> <?= $t->ee( $bdetails->getBillingTownCity() ) ?><br/><?php endif; ?>
                            <?php if( $bdetails->getBillingPostcode() ): ?> <?= $t->ee( $bdetails->getBillingPostcode() ) ?><?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td><?= $t->ee( $bdetails->getBillingCountryName() ) ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?= $t->ee( $bdetails->getBillingTelephone() ) ?></td>
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
                        <td><?= $t->ee( $bdetails->getInvoiceEmail() ) ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>