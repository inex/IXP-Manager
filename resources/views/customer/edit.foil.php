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
            <div class="well">
                <div>
                    <b>
                        Prepopulate this form from PeeringDB by entering the network ASN here
                    </b>
                </div>

                <div class="form-group col-sm-3">
                    <input type="text" class="form-control" id="asn-search">
                </div>

                <div class="btn-group">
                    <span class="btn btn-primary" id="btn-populate" style="margin-left: 15px" href="">
                        Populate
                    </span>
                </div>
            </div>

            <div class="col-md-6">
                <h3>Customer Details</h3>
                <hr>
                <?= Former::text( 'name' )
                    ->label( 'Name' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'type' )
                    ->label( 'Type' )
                    ->fromQuery( \Entities\Customer::$CUST_TYPES_TEXT )
                    ->placeholder( 'Choose a type' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::text( 'shortname' )
                    ->label( 'Short Name' )
                    ->blockHelp( '' );
                ?>

                <?= Former::url( 'corpwww' )
                    ->label( 'Corporate Website' )
                    ->blockHelp( '' );
                ?>

                <?= Former::date( 'datejoin' )
                    ->label( 'Date Joined' )
                    ->blockHelp( '' );
                ?>

                <?= Former::date( 'dateleft' )
                    ->label( 'Date Left' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'status' )
                    ->label( 'Status' )
                    ->fromQuery( \Entities\Customer::$CUST_STATUS_TEXT )
                    ->placeholder( 'Choose a status' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'md5support' )
                    ->label( 'MD5 Support' )
                    ->fromQuery( \Entities\Customer::$MD5_SUPPORT )
                    ->placeholder( 'Choose a MD5 Support' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::text( 'abbreviatedName' )
                    ->label( 'Abbreviated Name' )
                    ->blockHelp( '' );
                ?>
            </div>

            <div class="col-md-6 full-member-details">
                <h3>Peering Details</h3>
                <hr>
                <?= Former::number( 'autsys' )
                    ->label( 'AS Number' )
                    ->blockHelp( '' );
                ?>

                <?= Former::number( 'maxprefixes' )
                    ->label( 'Max Prefixes' )
                    ->blockHelp( '' );
                ?>

                <?= Former::email( 'peeringemail' )
                    ->label( 'Email' )
                    ->blockHelp( '' );
                ?>

                <?= Former::text( 'peeringmacro' )
                    ->label( 'IPv4 Peering Macro' )
                    ->blockHelp( '' );
                ?>

                <?= Former::text( 'peeringmacrov6' )
                    ->label( 'IPv6 Peering Macro' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'peeringpolicy' )
                    ->label( 'Peering Policy' )
                    ->fromQuery( \Entities\Customer::$PEERING_POLICIES )
                    ->placeholder( 'Choose a peering policy' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'irrdb' )
                    ->label( 'IRRDB Source' )
                    ->fromQuery( $t->irrdbs, 'source' )
                    ->placeholder( 'Choose a IRRDB source' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?= Former::checkbox( 'activepeeringmatrix' )
                    ->label( ' ' )
                    ->text( 'Active Peering Matrix' )
                    ->checked_value( 1 )
                    ->unchecked_value( 0 )
                    ->blockHelp('' );
                ?>

                <i>Note that the IPv4 peering macro is used when there is no v6 macro set. To force no macro for IPv6, set it as AS-NULL.</i>
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

            <?= Former::fax( 'nocfax' )
                ->label( 'Fax' )
                ->placeholder( '+353 1 765 4321' )
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

            <?= Former::hidden( 'ixp' )
                ->value( $t->ixp->getId() )
            ?>
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