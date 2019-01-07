<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>

    <?php if( auth::getUser()->isSuperUser() ): ?>

        <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>
            <a href="<?= route($t->feParams->route_prefix.'@list') ?>">
        <?php endif; ?>
        <?=  $t->feParams->pagetitle  ?>
        <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>
            </a>
        <?php endif; ?>

    <?php else: ?>

        <?=  $t->feParams->pagetitle  ?>

    <?php endif; ?>


<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( auth::getUser()->isSuperUser() ): ?>
        <li> <?= $t->data[ 'params']['isAdd'] ? 'Add' : 'Edit' ?> <?= $t->feParams->titleSingular  ?> </li>
    <?php else: ?>
        <h3 style="display:inline;color: #999999"><?= $t->data[ 'params']['isAdd'] ? 'Add' : 'Edit' ?> <?= $t->feParams->titleSingular  ?></h3>
    <?php endif; ?>

<?php $this->append() ?>


<?php $this->section( 'page-header-preamble' ) ?>

    <?php if( $t->data[ 'view' ]['editHeaderPreamble'] ): ?>

        <?= $t->insert( $t->data[ 'view' ]['editHeaderPreamble'] ) ?>

    <?php else: ?>

        <li class="pull-right">
            <div class="btn-group btn-group-xs" role="group">

                <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
                    <a type="button" target="_blank" class="btn btn-default" href="<?= $t->feParams->documentation ?>">Documentation</a>
                <?php endif; ?>

                <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                    <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>
                        <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@list') ?>">
                            <span class="glyphicon glyphicon-th-list"></span>
                        </a>
                    <?php endif; ?>
                <?php endif;?>

            </div>
        </li>

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