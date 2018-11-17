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
                ->blockHelp( "" );
            ?>

            <?= Former::text( 'snmppasswd' )
                ->label( 'SNMP Community' )
                ->blockHelp( "" );
            ?>


            <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
                Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') ),
                Former::success_button( 'Help' )->id( 'help-btn' ),
                Former::default_link( $t->data[ 'params'][ 'addBySnmp'] ? "Manual / Non-SNMP Add" : "Add by SNMP" )->href( route( $t->data[ 'params'][ 'addBySnmp'] ? $t->feParams->route_prefix.'@add' : $t->feParams->route_prefix.'@pre-add-by-snmp' ) )
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