<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?=  $t->feParams->pagetitle  ?>
    /
    Create Switch via SNMP
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?= $t->insert( $t->data[ 'view' ]['editHeaderPreamble'] ) ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info" role="alert">
                <div class="d-flex align-items-center">
                    <div class="mr-4 text-center">
                        <i class="fa fa-question-circle fa-2x"></i>
                    </div>
                    <div>
                        <p>
                            Adding switches in IXP Manager requires SNMP v2 access from the host that runs IXP Manager. When you complete the details below, IXP Manager
                            queries the switch using SNMP to discover its make, model, number and names of ports, etc.
                        </p>
                        See <a target="_blank" href="<?= url( 'https://docs.ixpmanager.org/usage/switches/' ) ?>">the official documentation for more information</a>
                        or click the <em>Help</em> button below.
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <?= Former::open()->method( 'POST' )
                        ->id( 'form' )
                        ->action( route( $t->feParams->route_prefix.'@store-by-snmp' ) )
                        ->customInputWidthClass( 'col-lg-4 col-sm-6' )
                        ->customLabelWidthClass( 'col-lg-3 col-sm-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                    <?= Former::text( 'hostname' )
                        ->label( 'Hostname' )
                        ->placeholder( 'switch01.mgmt.example.com' )
                        ->blockHelp( "Ideally this should be the fully qualified hostname of your switch.<br><br>"
                            . "E.g. <code>switch01.mgmt.example.com</code><br><br>"
                            . "You can use an IP address here but that is strongly discouraged." );
                    ?>

                    <?= Former::text( 'snmppasswd' )
                        ->label( 'SNMP Community' )
                        ->placeholder( 'yourcommunity' )
                        ->blockHelp( "The SNMP v2c community of your switch. You switch <b>must</b> be reachable and SNMP accessible from the host which runs IXP Manager." );
                    ?>

                    <?= Former::actions(
                            Former::primary_submit( 'Next &gg;' )->id( 'btn-submit' )->class( "mb-2 mb-md-0" ),
                            Former::secondary_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') )->class( "mb-2 mb-md-0" ),
                            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-md-0" ),
                            Former::secondary_link( "Manual / Non-SNMP Add" )->href( route( $t->feParams->route_prefix.'@create' ) . '?manual=1' )->class( "mb-2 mb-md-0" )
                        );
                    ?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>