<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li><?= $t->cust ? "Edit" : "Add" ?> Customer</li>
<?php $this->append() ?>

<?php $this->section('content') ?>
<?= Former::open()->method( 'POST' )
    ->action( route ('customer@storeBillingInfo' ) )
    ->customWidthClass( 'col-sm-6' )

?>

    <div class="col-md-12">
        <div class="col-md-6">
            <h3>Registration Details</h3>
            <hr>
            <?= Former::text( 'registeredName' )
                ->label( 'Registered Name' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'companyNumber' )
                ->label( 'Company Number' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'jurisdiction' )
                ->label( 'Jurisdiction' )
                ->fromQuery( $t->juridictions, 'jurisdiction' )
                ->placeholder( 'Choose a juridiction' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>


            <?= Former::text( 'address1' )
                ->id( 'address1' )
                ->label( 'Address' )
                ->blockHelp( '' )
            ?>

            <?= Former::text( 'address2' )
                ->id( 'address2' )
                ->label( ' ' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'address3' )
                ->id( 'address3' )
                ->label( ' ' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'townCity' )
                ->id( 'townCity' )
                ->label( 'City' )
                ->blockHelp( '' );
            ?>

            <?= Former::text( 'postcode' )
                ->id( 'postcode' )
                ->label( 'Postcode' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'country' )
                ->label( 'Country' )
                ->fromQuery( $t->countries, 'name', 'iso_3166_2' )
                ->placeholder( 'Choose a country' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>
        </div>
        <?php if( ( !isset( $t->billingNotify ) || !$t->billingNotify ) || !$t->resellerMode || !$t->cust->isResoldCustomer() ): ?>
            <div class="col-md-6 full-member-details">
                <h3>Billing Details</h3>
                <hr>
                <?= Former::text( 'billingContactName' )
                    ->label( 'Contact' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'billingFrequency' )
                    ->label( 'Billing Frequency' )
                    ->fromQuery( \Entities\CompanyBillingDetail::$BILLING_FREQUENCIES )
                    ->placeholder( 'Choose a billing frequency' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::text( 'billingAddress1' )
                    ->id( 'billingAddress1' )
                    ->label( 'Address' )
                    ->blockHelp( '' )
                    ->append( '<button class="btn-default btn" id="copy-address" type="button"><i class="glyphicon glyphicon-retweet"> </i></button>' );
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

                <?= Former::checkbox( 'purchaseOrderRequired' )
                    ->label( ' ' )
                    ->text( 'Purchase Order Required' )
                    ->checked_value( 1 )
                    ->unchecked_value( 0 )
                    ->blockHelp('' );
                ?>

                <?= Former::select( 'invoiceMethod' )
                    ->label( 'Invoice Method' )
                    ->fromQuery( \Entities\CompanyBillingDetail::$INVOICE_METHODS )
                    ->placeholder( 'Choose an invoice Method' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::text( 'invoiceEmail' )
                    ->id( 'invoiceEmail' )
                    ->label( 'Invoice E-Mail' )
                    ->placeholder( 'invoicing@example.com8' )
                    ->blockHelp( '' );
                ?>

                <?= Former::text( 'vatRate' )
                    ->id( 'vatRate' )
                    ->label( 'VAT Rate' )
                    ->blockHelp( '' );
                ?>
                <?= Former::text( 'vatNumber' )
                    ->id( 'vatNumber' )
                    ->label( 'VAT Number' )
                    ->blockHelp( '' );
                ?>
            </div>
        <?php endif; ?>
    </div>


<?=Former::actions( Former::primary_submit( 'Save Changes' ),
    Former::default_link( 'Cancel' )->href( url ( 'customer/overview/id/' )."/".$t->cust->getId() ),
    Former::success_button( 'Help' )->id( 'help-btn' )
);?>


<?= Former::hidden( 'id' )
    ->value( $t->cust ? $t->cust->getId() : '' )
?>

<?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            //////////////////////////////////////////////////////////////////////////////////////
            // we'll need these handles to html elements in a few places:

            const input_name              = $( '#name' );
            const input_colo_ref          = $( '#colo_reference' );

            /**
             * set the address information to the billing address info
             */
            $( "#copy-address" ).click( () => {
                $( "#billingAddress1"   ).val( $( "#address1" ).val(  ) );
                $( "#billingAddress2"   ).val( $( "#address2" ).val(  ) );
                $( "#billingAddress3"   ).val( $( "#address3" ).val(  ) );
                $( "#billingTownCity"   ).val( $( "#townCity" ).val(  ) );
                $( "#billingPostcode"   ).val( $( "#postcode" ).val(  ) );
                $( "#billingCountry"    ).val( $( "#country"  ).val(  ) ).trigger('change.select2');
            } );
        });
    </script>
<?php $this->append() ?>