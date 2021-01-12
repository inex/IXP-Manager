<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <a href="<?= route( 'customer@overview', [ 'cust' => $t->c->id ] ) ?>">
        <?= $t->c->getFormattedName() ?>
    </a>
    /
    Billing and Registration Details
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?= $t->alerts() ?>

                <?php if( config( 'ixp_fe.customer.billing_updates_notify' ) ): ?>
                    <div class="alert alert-info mt-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fa fa-question-circle fa-2x"></i>
                            </div>
                            <div class="col-sm-12">
                                <b>NB:</b> Billing update notifications have been enabled. As such, any changes to the below form will be
                                emailed to
                                <a href="mailto:<?= config( 'ixp_fe.customer.billing_updates_notify' ) ?>"><?= config( 'ixp_fe.customer.billing_updates_notify' ) ?></a>
                                on submission.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div id="instructions-alert" class="alert alert-info collapse">
                    <b>IXP Manager</b> does not provide any accounting / invoicing functionality. All the information on this page is
                    informational for your own record keeping. None of it is required.
                </div>

                <?= Former::open()->method( 'POST' )
                    ->action( route ('customer@store-billing-and-reg-details', [ 'cust' => $t->c->id ] ) )
                    ->customInputWidthClass( 'col-sm-6' )
                    ->customLabelWidthClass( 'col-sm-4' )
                    ->actionButtonsCustomClass( "grey-box")
                ?>

                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <h3>Registration Details</h3>
                        <hr class="tw-mb-6">

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
                            ->placeholder( 'Choose a jurisdiction' )
                            ->addClass( 'chzn-select-tag' )
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

                    <?php if( !( $t->resellerMode() && $t->c->reseller ) ): ?>
                        <div class="col-lg-6 col-md-12 full-member-details">
                            <h3>Billing Details</h3>
                            <hr class="tw-mb-6">

                            <?= Former::text( 'billingContactName' )
                                ->label( 'Contact' )
                                ->blockHelp( '' );
                            ?>

                            <?= Former::select( 'billingFrequency' )
                                ->label( 'Billing Frequency' )
                                ->fromQuery( \IXP\Models\CompanyBillingDetail::$BILLING_FREQUENCIES )
                                ->placeholder( 'Choose a billing frequency' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( '' );
                            ?>

                            <?= Former::text( 'billingAddress1' )
                                ->id( 'billingAddress1' )
                                ->label( 'Address' )
                                ->blockHelp( '' )
                                ->append( '<button class="btn-white btn" id="copy-address" type="button"><i class="fa fa-retweet"> </i></button>' );
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
                                ->value( 1 )
                                ->inline()
                                ->blockHelp('' );
                            ?>

                            <?= Former::select( 'invoiceMethod' )
                                ->label( 'Invoice Method' )
                                ->fromQuery( \IXP\Models\CompanyBillingDetail::$INVOICE_METHODS )
                                ->placeholder( 'Choose an invoice method' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( '' );
                            ?>

                            <?= Former::text( 'invoiceEmail' )
                                ->id( 'invoiceEmail' )
                                ->label( 'Invoice E-Mail' )
                                ->placeholder( 'invoicing@example.com' )
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

                <?=Former::actions( Former::primary_submit( 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                    Former::secondary_link( 'Cancel' )->href( route( "customer@overview" , [ 'cust' => $t->c->id ] ) )->class( "mb-2 mb-sm-0" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0")
                );?>

                <?= Former::close() ?>

            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            /**
             * set the address information to the billing address info
             */
            $( "#copy-address" ).click( () => {
                $( "#address1" ).val(  ) !== ''   ? $( "#billingAddress1"   ).val( $( "#address1" ).val() ) : '';
                $( "#address2" ).val(  ) !== ''   ? $( "#billingAddress2"   ).val( $( "#address2" ).val() ) : '';
                $( "#address3" ).val(  ) !== ''   ? $( "#billingAddress3"   ).val( $( "#address3" ).val() ) : '';
                $( "#townCity" ).val(  ) !== ''   ? $( "#billingTownCity"   ).val( $( "#townCity" ).val() ): '';
                $( "#postcode" ).val(  ) !== ''   ? $( "#billingPostcode"   ).val( $( "#postcode" ).val() ): '';
                $( "#country" ).val(  )  !== ''   ? $( "#billingCountry"    ).val( $( "#country"  ).val() ).trigger('change.select2') : '';
            } );
        });
    </script>
<?php $this->append() ?>