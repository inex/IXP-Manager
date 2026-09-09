<?php
    $isCustUser = Auth::getUser()->isCustUser();
    $c = $t->c; /** @var $c \IXP\Models\Customer */
?>

<div class="row">
    <div class="col-lg-6 mb-4">
        <h3>
            <?= __( 'NOC Details' ) ?>
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
            ->blockHelp( __c( 'Working hours phone number for contacting the :customer NOC.<br><br>This is available to all other :customers.' ) );
        ?>

        <?= Former::phone( 'noc24hphone' )
            ->label( '24h Phone' )
            ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
            ->blockHelp( __c( '24/7 emergency phone number for contacting the :customer NOC.<br><br>This is available to all other :customers.' ) );
        ?>

        <?= Former::email( 'nocemail' )
            ->label( 'Email' )
            ->placeholder( 'noc@example.com' )
            ->blockHelp( __c( 'The NOC email is used in :customer lists. We try to encourage the use of a role alias such as noc@example.com but this does not always work out.<br><br>This is available to all other :customers.' ) );
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
    </div>
    <div class="col-lg-6">
        <?php if( !config('ixp.reseller.no_billing') || !$t->resellerMode() || !$c->resellerObject()->exists() ): ?>
            <h3>
                <?= __( 'Billing Details' ) ?>
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
                    Former::primary_submit( 'Update Billing Details' ),
                    Former::success_button( 'Help' )->class( "help-btn mb-sm-0 mb-2" )
                );
                ?>
            <?php endif; ?>

            <?= Former::close() ?>
        <?php endif; ?>
    </div>

    <div class="col-lg-6">
        <h3>
            <?= __( 'AS-SETS' ) ?>
        </h3>
        <table class="table table-striped">
            <tr>
                <td>
                    <?= __( 'Peering Policy' ) ?>
                </td>
                <td>
                    <?= $t->ee( $c->peeringpolicy ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= __( 'IRRDB source' ) ?>
                </td>
                <td>
                    <?php if( $c->irrdb ): ?>
                        <?= $t->ee( $c->irrdbConfig->source )?>

                        <?php if( $c->routeServerClient() && $c->irrdbFiltered() ): ?>
                            (<a href="<?= route( "irrdb@list", [ "cust" => $c->id, "type" => 'prefix', "protocol" => $c->isIPvXEnabled( 4) ? 4 : 6 ] ) ?>"><?= __( 'entries' ) ?></a>)
                        <?php endif; ?>

                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= __( 'ASN' ) ?>
                </td>
                <td>
                    <?= $t->asNumber( $c->autsys ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= __( 'IPv4 AS-SET' ) ?>
                </td>
                <td>
                    <?= $t->ee( $c->peeringmacro ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= __( 'IPv6 AS-SET' ) ?>
                </td>
                <td>
                    <?= $t->ee( $c->peeringmacrov6 ) ?>
                </td>
            </tr>
        </table>
    </div>
</div>