<?php
    $isCustUser = Auth::getUser()->isCustUser();
    $c = $t->c; /** @var $c \IXP\Models\Customer */
    $rdetails = $c->companyRegisteredDetail; /** @var \IXP\Models\CompanyRegisteredDetail $rdetails */    
?>

<div class="row">
    <div class="col-lg-6 mb-4">
        <h3>
            NOC Details
        </h3>
        <hr>
        <?= Former::open()
            ->populate( $t->dataNocDetail )
            ->method( 'post' )
            ->id( "noc" )
            ->action( route ( "dashboard@store-noc-details" ) )
            ->customInputWidthClass( 'col-sm-6' )
            ->customLabelWidthClass( 'col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::phone( 'nocphone' )
            ->label( 'Phone' )
            ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
            ->blockHelp( 'Working hours phone number for contacting the ' . config( 'ixp_fe.lang.customer.one' ) . ' NOC.<br><br>'
                . 'This is available to all other ' . config( 'ixp_fe.lang.customer.many' ) . '.' );
        ?>

        <?= Former::phone( 'noc24hphone' )
            ->label( '24h Phone' )
            ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
            ->blockHelp( '24/7 emergency phone number for contacting the ' . config( 'ixp_fe.lang.customer.one' ) . ' NOC.<br><br>'
                . 'This is available to all other ' . config( 'ixp_fe.lang.customer.many' ) . '.' );
        ?>

        <?= Former::email( 'nocemail' )
            ->label( 'Email' )
            ->placeholder( 'noc@example.com' )
            ->blockHelp( 'The NOC email is used in ' . config( 'ixp_fe.lang.customer.one' ) . ' lists. We try to encourage '
                . 'the use of a role alias such as noc@example.com but this does not '
                . 'always work out.<br><br>'
                . 'This is available to all other ' . config( 'ixp_fe.lang.customer.many' ) . '.' );
        ?>

        <?= Former::select( 'nochours' )
            ->label( 'Hours' )
            ->fromQuery( \IXP\Models\Customer::$NOC_HOURS )
            ->placeholder( 'Choose NOC Hours' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'The hours during which the NOC is available.' );
        ?>

        <?= Former::url( 'nocwww' )
            ->label( 'Website' )
            ->placeholder( 'http://www.noc.example.com/' )
            ->blockHelp( 'An optional NOC information email page / status page.' );
        ?>

        <?php if( !$isCustUser ): ?>
            <?= Former::actions(
                Former::primary_submit( 'Update NOC Details' )->class( "mb-sm-0 mb-2" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-sm-0 mb-2" )
            );
            ?>
        <?php endif; ?>

        <?= Former::close() ?>

        <h3>
            AS-SETS
        </h3>
        <table class="table table-striped">
            <tr>
                <th>
                    Peering Policy
                </th>
                <td>
                    <?= $t->ee( $c->peeringpolicy ) ?>
                </td>
            </tr>
            <tr>
                <th>
                    IRRDB source
                </th>
                <td>
                    <?php if( $c->irrdb ): ?>
                        <?= $t->ee( $c->irrdbConfig->source )?>

                        <?php if( $c->routeServerClient() && $c->irrdbFiltered() ): ?>
                            (<a href="<?= route( "irrdb@list", [ "cust" => $c->id, "type" => 'prefix', "protocol" => $c->isIPvXEnabled( 4) ? 4 : 6 ] ) ?>">entries</a>)
                        <?php endif; ?>

                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>
                    ASN
                </th>
                <td>
                    <?= $t->asNumber( $c->autsys ) ?>
                </td>
            </tr>
            <tr>
                <th>
                    IPv4 AS-SET
                </th>
                <td>
                    <?= $t->ee( $c->peeringmacro ) ?>
                </td>
            </tr>
            <tr>
                <th>
                    IPv6 AS-SET
                </th>
                <td>
                    <?= $t->ee( $c->peeringmacrov6 ) ?>
                </td>
            </tr>
        </table>
    </div>


    <div class="col-lg-6">
        <?php if( !config('ixp.reseller.no_billing') || !$t->resellerMode() || !$c->resellerObject()->exists() ): ?>
            <h3>
                Billing Details
            </h3>
            <hr>
            <?= Former::open()
                ->populate( $t->dataBillingDetail )
                ->method( 'post' )
                ->id( "billing" )
                ->action( route ( "dashboard@store-billing-details" ) )
                ->customInputWidthClass( 'col-sm-6' )
                ->customLabelWidthClass( 'col-sm-3' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

            <?= Former::text( 'billingContactName' )
                ->label( 'Contact' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'billingAddress1' )
                ->id( 'billingAddress1' )
                ->label( 'Address' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'billingAddress2' )
                ->id( 'billingAddress2' )
                ->label( ' ' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'billingAddress3' )
                ->id( 'billingAddress3' )
                ->label( ' ' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'billingTownCity' )
                ->id( 'billingTownCity' )
                ->label( 'City' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'billingPostcode' )
                ->id( 'billingPostcode' )
                ->label( 'Postcode' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'billingCountry' )
                ->label( 'Country' )
                ->fromQuery( $t->countries, 'name', 'iso_3166_2' )
                ->placeholder( 'Choose a country' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'billingEmail' )
                ->id( 'billingEmail' )
                ->label( 'Email' )
                ->placeholder( 'billing@example.com' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'billingTelephone' )
                ->id( 'billingTelephone' )
                ->label( 'Telephone' )
                ->placeholder( '+353 1 234 5678' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'invoiceEmail' )
                ->id( 'invoiceEmail' )
                ->label( 'Invoice E-Mail' )
                ->placeholder( 'invoicing@example.com' )
                ->blockHelp( '' );
            ?>

            <?php if( !$isCustUser ): ?>
                <?= Former::actions(
                    Former::primary_submit( 'Update Billing Details' )
                );
                ?>
            <?php endif; ?>

            <?= Former::close() ?>
        <?php endif; ?>
        <h3>
            Registration Details
        </h3>
        <div class="mb-4 tw-text-sm">
                   To update these details, please contact us at <a href="mailto:<?= config( 'identity.support_email' ) ?>"><?= config( 'identity.support_email' ) ?></a> </div>
        <table class="table table-striped">
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
