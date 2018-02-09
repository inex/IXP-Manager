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
        ->id( "form" )
        ->action( route ('customer@store' ) )
        ->customWidthClass( 'col-sm-6' )

    ?>
        <div class="col-md-12">

            <div id="instructions-alert" class="alert alert-info" style="display: none;">
                Official <b>IXP Manager</b> documentation for adding / editing customers can be found at <a href="http://docs.ixpmanager.org/usage/customers/">http://docs.ixpmanager.org/</a>.
            </div>

            <div class="well">
                <p>
                    <b>Prepopulate this form from PeeringDB by entering the network ASN here:</b>
                </p>

                <div class="form-group col-sm-3">
                    <input type="text" class="form-control" id="asn-search">
                </div>

                <div class="btn-group">
                    <span class="btn btn-primary" id="btn-populate" style="margin-left: 15px" href="">
                        Populate
                    </span>
                </div>

                <div class="pull-right">
                    <button class="btn-success btn" id="help-btn" type="button">Help</button>
                </div>
            </div>



            <div class="col-md-6">

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



            <div class="col-md-6 full-member-details">

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
                        . "servers, AS112). The calculated value is also included in emails from the Peering Manager from customer to customer.<br>br>"
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
                    ->fromQuery( $t->irrdbs, 'source' )
                    ->placeholder( 'Choose a IRRDB Source' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( "The IRRDB source sets the database where IXP Manager queries the customer's IRR data from. See "
                        . '<a href="http://docs.ixpmanager.org/features/irrdb/">the IRRDB feature page</a> for more information.' );
                ?>

                <?= Former::checkbox( 'activepeeringmatrix' )
                    ->label( '&nbsp;' )
                    ->text( 'Active Peering Matrix' )
                    ->checked_value( 1 )
                    ->unchecked_value( 0 )
                    ->blockHelp( "Indicates whether or not the customer's route server and bilateral peering sessions should appear in the public peering matrix." );
                ?>

            </div>
        </div>

    <div class="col-md-12 full-member-details">
        <div class="col-md-6">
            <h3>NOC Details</h3>
            <hr>
            <?= Former::phone( 'nocphone' )
                ->label( 'Phone' )
                ->placeholder( '+353 1 123 4567' )
                ->blockHelp( '' );
            ?>

            <?= Former::phone( 'noc24hphone' )
                ->label( '24h Phone' )
                ->placeholder( '+353 86 876 5432' )
                ->blockHelp( '' );
            ?>

            <?= Former::email( 'nocemail' )
                ->label( 'Email' )
                ->placeholder( 'noc@example.com' )
                ->blockHelp( '' );
            ?>

            <?= Former::select( 'nochours' )
                ->label( 'Hours' )
                ->fromQuery( \Entities\Customer::$NOC_HOURS )
                ->placeholder( 'Choose an hours' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

            <?= Former::url( 'nocwww' )
                ->label( 'Website' )
                ->placeholder( 'http://www.noc.example.com/' )
                ->blockHelp( '' );
            ?>

        </div>

        <div class="col-md-6">
            <?php if( $t->resellerMode ): ?>
                <h3>Reseller Details</h3>
                <hr>
                <?= Former::checkbox( 'isReseller' )
                    ->label( ' ' )
                    ->text( 'Is a Reseller' )
                    ->blockHelp('' );
                ?>

                <?= Former::checkbox( 'isResold' )
                    ->label( ' ' )
                    ->text( 'Resold Customer' )
                    ->blockHelp('' );
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
            <?php endif; ?>

        </div>
    </div>


    <?=Former::actions( Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( route ( 'customer@list' ) ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );?>


    <?= Former::hidden( 'id' )
        ->value( $t->cust ? $t->cust->getId() : '' )
    ?>

    <?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'customer/js/edit' ); ?>
<?php $this->append() ?>