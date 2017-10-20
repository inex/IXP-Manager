<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php if( !isset( $t->data[ 'feParams' ]->editonly ) || !$t->data[ 'feParams' ]->editonly ): ?>
    <?php $this->section( 'title' ) ?>
            <a href="<?= action($t->controller.'@list') ?>">
                <?=  $t->data[ 'feParams' ]->pagetitle  ?>
            </a>
    <?php $this->append() ?>
<?php endif; ?>
<?php $this->section( 'page-header-postamble' ) ?>
    <li> <?= $t->params['isAdd'] ? 'Add' : 'Edit' ?> <?= $t->data[ 'feParams' ]->titleSingular  ?> </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( !isset( $t->data[ 'feParams' ]->editonly ) || !$t->data[ 'feParams' ]->editonly ): ?>
        <li class="pull-right">
            <div class="btn-group btn-group-xs" role="group">
                <a type="button" class="btn btn-default" href="<?= action($t->controller.'@list') ?>">
                    <span class="glyphicon glyphicon-th-list"></span>
                </a>
            </div>
        </li>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <?= $t->view['editPreamble'] ? $t->insert( $t->view['editPreamble'] ) : '' ?>
    <?= $t->insert( $t->view['editForm' ] ) ?>
    <?= $t->view['editPostamble'] ? $t->insert( $t->view['editPostamble'] ) : '' ?>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <?= $t->view['editScript'] ? $t->insert( $t->view['editScript'] ) : '' ?>
<?php $this->append() ?>