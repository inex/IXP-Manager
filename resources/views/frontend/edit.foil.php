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

            <?= $t->alerts() ?>

            <?= $t->data[ 'view' ]['editPreamble'] ? $t->insert( $t->data[ 'view' ]['editPreamble'] ) : '' ?>
            <?= $t->insert( $t->data[ 'view' ]['editForm' ] ) ?>
            <?= $t->data[ 'view' ]['editPostamble'] ? $t->insert( $t->data[ 'view' ]['editPostamble'] ) : '' ?>

        </div>
    </div>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <?= $t->data[ 'view' ]['editScript'] ? $t->insert( $t->data[ 'view' ]['editScript'] ) : '' ?>
<?php $this->append() ?>