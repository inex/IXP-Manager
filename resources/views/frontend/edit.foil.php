<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?=  $t->feParams->pagetitle  ?>
        /
    <?php if( isset( $t->feParams->customBreadcrumb ) ): ?>
        <?= $t->feParams->customBreadcrumb ?>
    <?php else: ?>
        <?= $t->data[ 'params']['isAdd'] ? 'Create' : 'Edit' ?> <?= $t->feParams->titleSingular  ?>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( $t->data[ 'view' ]['editHeaderPreamble'] ): ?>
        <?= $t->insert( $t->data[ 'view' ]['editHeaderPreamble'] ) ?>
    <?php else: ?>
        <div class="btn-group btn-group-sm" role="group">
            <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
                <a target="_blank" class="btn btn-white" href="<?= $t->feParams->documentation ?>">
                    Documentation
                </a>
            <?php endif; ?>

            <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>
                    <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@list') ?>">
                        <span class="fa fa-th-list"></span>
                    </a>
                <?php endif; ?>
            <?php endif;?>
        </div>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
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