<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>

    <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>

        <a href="<?= route($t->feParams->route_prefix.'@list') ?>">
            <?php endif; ?>
            <?=  $t->feParams->pagetitle  ?>
            <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>
        </a>

    <?php endif; ?>

<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <li> Add Switch via SNMP </li>

<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>

<?= $t->insert( $t->data[ 'view' ]['editHeaderPreamble'] ) ?>

<?php $this->append() ?>





<?php $this->section('content') ?>


<div class="row-fluid">
    <div class="well">
        Adding switches in IXP Manager requires SNMP v2 access from the host that runs IXP Manager. When you complete the details below, IXP Manager
        queries the switch using SNMP to discover its make, model, number and names of ports, etc.
        <br><br>
        See <a target="_blank" href="<?= url( 'https://docs.ixpmanager.org/usage/switches/' ) ?>">the official documentation for more information</a>
        or click the <em>Help</em> button below.
    </div>
</div>


<div class="row">

    <div class="col-sm-12">

        <div class="well">

            <?= Former::open()->method( 'POST' )
                ->id( 'form' )
                ->action( route( $t->feParams->route_prefix.'@store-by-snmp' ) )
                ->customWidthClass( 'col-sm-3' )
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
                    Former::primary_submit( 'Next &gg;' )->id( 'btn-submit' ),
                    Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') ),
                    Former::success_button( 'Help' )->id( 'help-btn' ),
                    Former::default_link( "Manual / Non-SNMP Add" )->href( route( $t->feParams->route_prefix.'@add' ) . '?manual=1' )
                );
            ?>

            <?= Former::close() ?>

        </div>

    </div>

</div>

<?php $this->append() ?>

