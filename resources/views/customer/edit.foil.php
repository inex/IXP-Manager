<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Customers / <?= $t->cust ? "Edit" : "Add" ?>
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="container-fluid">

    <div class="row">

        <div class="col-lg-12">

            <?= Former::open()->method( 'POST' )
                ->id( "form" )
                ->action( route('customer@store' ) )
                ->customInputWidthClass( 'col-sm-6' )
                ->customLabelWidthClass( 'col-sm-3' )
                ->actionButtonsCustomClass( "grey-box")

            ?>


            <div id="instructions-alert" class="alert alert-info" style="display: none;">
                Official <b>IXP Manager</b> documentation for adding / editing customers can be found at <a href="http://docs.ixpmanager.org/usage/customers/">http://docs.ixpmanager.org/</a>.
            </div>

            <div class="bg-light shadow-sm p-4">
                <p>
                    <b>Prepopulate this form from PeeringDB by entering the network ASN here:</b>
                </p>

                <div class="d-sm-flex">
                    <div class="form-group col-lg-4 col-sm-6 col-12">
                        <input type="text" class="form-control " id="asn-search">
                    </div>
                    <div class="form-group ml-2">
                        <button class="btn btn-primary " id="btn-populate">
                            Populate
                        </button>
                        <button class="btn-success btn help-btn " type="button">
                            Help
                        </button>
                    </div>
                </div>


            </div>


            <div class="mt-4 row">

                <div class="col-lg-6 col-md-12 mb-4 mb-sm-0">

                    <h3>Customer Details</h3>
                    <hr>

                    <?= Former::text( 'name' )
                        ->label( 'Name' )
                        ->placeholder( "Acme Intermet Access" )
                        ->blockHelp( "The customer's name as you/they typically want it to appear in IXP Manager. It is not necessarily "
                            . "their full legal entity name (that goes elsewhere). The <em>abbreviated name</em> is a shorter version "
                            . "of the name that is used in space constrained areas such as graph labels." );
                    ?>

                    <?= Former::select( 'type' )
                        ->label( 'Type' )
                        ->fromQuery( \Entities\Customer::$CUST_TYPES_TEXT )
                        ->placeholder( 'Choose Type' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Please see the external documentation referenced above for full details of customer types. For a normal IXP customer, you just want <code>Full</code>.' );
                    ?>

                    <?= Former::text( 'shortname' )
                        ->label( 'Short Name' )
                        ->placeholder( "acme" )
                        ->blockHelp( "Short name is something we are slowly removing. It is currently visible in some URLs and a couple other "
                            . "areas. It should be a lowercase single word (<code>[a-z0-9]</code>) and it should not be changed after it is set." );
                    ?>

                    <?= Former::url( 'corpwww' )
                        ->label( 'Corporate Website' )
                        ->placeholder( 'http://www.example.com/' )
                        ->blockHelp( "The corporate website is used when linking the customer name in various customer lists. It must be a valid "
                            . "URL. Try and stick to the scheme: <code>http://www.example.com/</code> - i.e. include <code>http[s]://</code> and "
                            . "end with a trailing slash." );
                    ?>

                    <?= Former::date( 'datejoin' )
                        ->label( 'Date Joined' )
                        ->blockHelp( 'The data this customer joined the exchange.' );
                    ?>

                    <?= Former::date( 'dateleft' )
                        ->label( 'Date Left' )
                        ->blockHelp( "The date this customer left the exchange. <b>This has real consequences: setting Date Left effectively closes "
                            . "the customer's account.</b> This means configuration will no longer be included for graphing, router configuration, etc.<br><br>"
                            . "Generally we tend to not delete customers but mark them as closed by setting this field." );
                    ?>

                    <?= Former::select( 'status' )
                        ->label( 'Status' )
                        ->fromQuery( \Entities\Customer::$CUST_STATUS_TEXT )
                        ->placeholder( 'Choose Status' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( "The state of the customer. The most important of which is <code>Normal</code> which is what you'll use nearly 100% of the "
                            . "time. Setting either of the other two otions (<code>Suspended</code> / <code>Not Connected</code>) will have the same effect as "
                            . "closing the accout as described above: removing route server / collector sessions, graphing configuration, etc." );
                    ?>

                    <?= Former::select( 'md5support' )
                        ->label( 'MD5 Support' )
                        ->fromQuery( \Entities\Customer::$MD5_SUPPORT )
                        ->placeholder( 'Choose MD5 Support' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( "This is not something that has been fully integrated into all view screens. You should probably default to Yes for now as "
                            . "this will cover >=95% of cases. It is an informational flag only for member to member bilateral peering." );
                    ?>

                    <?= Former::text( 'abbreviatedName' )
                        ->label( 'Abbreviated Name' )
                        ->placeholder( "Acme" )
                        ->blockHelp( "The Abbreviated Name is a shorter version of the name that is used in space constrained areas such as graph labels." );
                    ?>
                </div>


                <div class="col-lg-6 col-md-12 full-member-details" style="<?=
                old( 'type' ) == Entities\Customer::TYPE_ASSOCIATE || ( $t->cust && $t->cust->isTypeAssociate() ) ? 'display: none;' : ''
                ?>">

                    <h3>Peering Details</h3>
                    <hr>

                    <?= Former::number( 'autsys' )
                        ->label( 'AS Number' )
                        ->placeholder('65500')
                        ->blockHelp( 'The AS Number is just the integer value without any AS prefix, etc.' );
                    ?>

                    <?= Former::number( 'maxprefixes' )
                        ->label( 'Max Prefixes' )
                        ->placeholder('250')
                        ->blockHelp( "Max Prefixes is known as <em>the global max prefixes</em> value. It is used to work out the appropriate "
                            . "max prefixes value to apply to all router configurations in the stock / default templates (route collector and "
                            . "servers, AS112). The calculated value is also included in emails from the Peering Manager from customer to customer.<br><br>"
                            . "Please see the external documentation referenced above for full details of this and alternative ways of setting it." );
                    ?>

                    <?= Former::email( 'peeringemail' )
                        ->label( 'Email' )
                        ->placeholder( "peering@example.com" )
                        ->blockHelp( "The Peering Email is used in member lists and by the Peering Manager for sending emails. We try and encourage "
                            . "using a role alias such as peering@example.com but this does not always work out." );
                    ?>

                    <?= Former::text( 'peeringmacro' )
                        ->label( 'IPv4 Peering Macro' )
                        ->placeholder( "AS-ACME-EXAMPLE" )
                        ->blockHelp( "The IPv4 Peering Macro is used instead of the AS number when set to generate inbound prefix filters for the "
                            . "route servers based on the member's published IRR records. AS-BTIRE in the RIPE database is an example for BT Ireland." );
                    ?>

                    <?= Former::text( 'peeringmacrov6' )
                        ->label( 'IPv6 Peering Macro' )
                        ->placeholder( "AS-ACME-V6-EXAMPLE" )
                        ->blockHelp( "In the event that IPv6 Peering Macro is set, this will be used to generate IPv6 inbound prefix filters, "
                            . "otherwise the IPv4 Peering Macro will be used for both. If neither is set, the IRR policy of the AS number will "
                            . "be used. Use <code>AS-NULL</code> to disable one or the other protocol peering macro if only one is required." );
                    ?>

                    <?= Former::select( 'peeringpolicy' )
                        ->label( 'Peering Policy' )
                        ->fromQuery( \Entities\Customer::$PEERING_POLICIES )
                        ->placeholder( 'Choose a Peering Policy' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( "The Peering Policy is informational only and is displayed in member lists. Typically speaking, route "
                            . "server members should have an open peering policy but others are possible if you use standard route server "
                            . "communities for controlling the distribution of prefixes." );
                    ?>

                    <?= Former::select( 'irrdb' )
                        ->label( 'IRRDB Source' )
                        ->placeholder( 'Choose a IRRDB Source' )
                        ->fromQuery( $t->irrdbs, 'source' )
                        ->addClass( 'chzn-select-deselect' )
                        ->blockHelp( "The IRRDB source sets the database where IXP Manager queries the customer's IRR data from. See "
                            . '<a href="http://docs.ixpmanager.org/features/irrdb/">the IRRDB feature page</a> for more information.' );
                    ?>

                    <?= Former::checkbox( 'activepeeringmatrix' )
                        ->label( '&nbsp;' )
                        ->text( 'Active Peering Matrix' )
                        ->value( 1 )
                        ->inline()
                        ->blockHelp( "Indicates whether or not the customer's route server and bilateral peering sessions should appear in the public peering matrix." );
                    ?>

                </div>

            </div>



            <div class="clearfix"></div>

            <div class="full-member-details row mt-4" style="<?=
            old( 'type' ) == Entities\Customer::TYPE_ASSOCIATE || ( $t->cust && $t->cust->isTypeAssociate() ) ? 'display: none;' : ''
            ?>">

                <div class="col-lg-6 col-md-12">
                    <h3>NOC Details</h3>
                    <hr>
                    <?= Former::phone( 'nocphone' )
                        ->label( 'Phone' )
                        ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
                        ->blockHelp( 'Working hours phone number for contacting the customer NOC.<br><br>'
                            . 'This is available to all other customers.' );
                    ?>

                    <?= Former::phone( 'noc24hphone' )
                        ->label( '24h Phone' )
                        ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
                        ->blockHelp( '24/7 emergency phone number for contacting the customer NOC..<br><br>'
                            . 'This is available to all other customers.' );
                    ?>

                    <?= Former::email( 'nocemail' )
                        ->label( 'Email' )
                        ->placeholder( 'noc@example.com' )
                        ->blockHelp( 'The NOC email is used in customer lists. We try and encourage "
                . "the use of a role alias such as noc@example.com but this does not "
                . "always work out.<br><br>'
                            . 'This is available to all other customers.' );
                    ?>

                    <?= Former::select( 'nochours' )
                        ->label( 'Hours' )
                        ->fromQuery( \Entities\Customer::$NOC_HOURS )
                        ->placeholder( 'Choose NOC Hours' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The hours during which the NOC is available.' );
                    ?>

                    <?= Former::url( 'nocwww' )
                        ->label( 'Website' )
                        ->placeholder( 'http://www.noc.example.com/' )
                        ->blockHelp( 'An optional NOC information email page / status page.' );
                    ?>

                </div>



                <?php if( $t->resellerMode() ): ?>

                    <div class="col-lg-6 col-md-12">
                        <h3>Reseller Details</h3>
                        <hr>
                        <?= Former::checkbox( 'isReseller' )
                            ->label( '&nbsp;' )
                            ->text( 'Is a Reseller' )
                            ->value( 1 )
                            ->inline()
                            ->blockHelp( 'Check this if this customer is (also) a reseller.' );
                        ?>

                        <?= Former::checkbox( 'isResold' )
                            ->label( '&nbsp;' )
                            ->text( 'Resold Customer' )
                            ->value( 1 )
                            ->inline()
                            ->blockHelp( 'Check this if this customer comes via a reseller. Then chose the reseller.' );
                        ?>

                        <div id="reseller-area" class="collapse">
                            <?= Former::select( 'reseller' )
                                ->label( 'Reseller' )
                                ->fromQuery( $t->resellers )
                                ->placeholder( 'Choose an reseller' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( '' );
                            ?>
                        </div>
                    </div>

                <?php endif; ?>

            </div>





            <?= Former::hidden( 'id' )->value( $t->cust ? $t->cust->getId() : '' ) ?>

            <div style="clear: both"></div>
            <br/>



                <?= Former::actions( Former::primary_submit( $t->cust ? 'Save Changes' : 'Add' )->class( "mb-2 mb-sm-0" ),
                    Former::secondary_link( 'Cancel' )->href( route( 'customer@list' ) )->class( "mb-2 mb-sm-0" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                );
                ?>



            <?= Former::close() ?>

        </div>

    </div>

</div>



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'customer/js/edit' ); ?>
<?php $this->append() ?>