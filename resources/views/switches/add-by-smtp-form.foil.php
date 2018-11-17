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

    <li> <?= $t->data[ 'params']['isAdd'] ? 'Add' : 'Edit' ?> <?= $t->feParams->titleSingular  ?> </li>

<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>

<?php if( $t->data[ 'view' ]['editHeaderPreamble'] ): ?>

    <?= $t->insert( $t->data[ 'view' ]['editHeaderPreamble'] ) ?>

<?php else: ?>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>

        <li class="pull-right">
            <div class="btn-group btn-group-xs" role="group">
                <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>
                    <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@list') ?>">
                        <span class="glyphicon glyphicon-th-list"></span>
                    </a>
                <?php endif; ?>
            </div>
        </li>

    <?php endif;?>

<?php endif;?>

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
                ->action( route( $t->feParams->route_prefix.'@pre-store-by-snmp' ) )
                ->customWidthClass( 'col-sm-3' )
            ?>

            <?= Former::text( 'hostname' )
                ->label( 'Hostname' )
                ->blockHelp( "Ideally this should be the fully qualified hostname of your switch.<br><br>"
                    . "E.g. <code>switch01.mgmt.example.com</code><br><br>"
                    . "You can use an IP address here but that is strongly discouraged." );
            ?>

            <?= Former::text( 'snmppasswd' )
                ->label( 'SNMP Community' )
                ->blockHelp( "The SNMP v2c community of your switch. You switch <b>must</b> be reachable and SNMP accessible from the host which runs IXP Manager." );
            ?>


            <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
                Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') ),
                Former::success_button( 'Help' )->id( 'help-btn' ),
                Former::default_link( $t->data[ 'params'][ 'addBySnmp'] ? "Manual / Non-SNMP Add" : "Add by SNMP" )->href( route( $t->data[ 'params'][ 'addBySnmp'] ? $t->feParams->route_prefix.'@add' : $t->feParams->route_prefix.'@add-by-snmp' ) )
            );
            ?>

            <?= Former::hidden( 'add_by_snnp' )
                ->value( $t->data[ 'params'][ 'addBySnmp'] ? true : false )
            ?>

            <?= Former::close() ?>

        </div>

    </div>

</div>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>

    <?= $t->data[ 'view' ]['editScript'] ? $t->insert( $t->data[ 'view' ]['editScript'] ) : '' ?>

<?php $this->append() ?>