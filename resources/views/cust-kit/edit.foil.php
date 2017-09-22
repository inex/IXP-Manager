<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

    <?php $this->section( 'title' ) ?>
        <?= $t->data[ 'feParams' ]->pagetitle  ?>
    <?php $this->append() ?>

    <?php $this->section( 'page-header-postamble' ) ?>
        <li>Edit <?= $t->data[ 'feParams' ]->pagetitle  ?> </li>
    <?php $this->append() ?>

    <?php $this->section( 'page-header-preamble' ) ?>
        <li class="pull-right">
            <div class="btn-group btn-group-xs" role="group">
                <a type="button" class="btn btn-default" href="<?= action ($t->controller.'@listAction') ?>">
                    <span class="glyphicon glyphicon-th-list"></span>
                </a>
            </div>
        </li>
    <?php $this->append() ?>

    <?php $this->section('content') ?>

        <?= $t->alerts() ?>
        <div class="well col-sm-12">
            <?= Former::open()->method( 'POST' )
                ->id( 'form' )
                ->action( action ( $t->controller.'@storeAction' ) )
                ->customWidthClass( 'col-sm-3' )
            ?>

            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->blockHelp( "" );
            ?>

            <?= Former::select( 'customer' )
                ->label( 'Customer' )
                ->fromQuery( $t->custs, 'name' )
                ->placeholder( 'Choose a customer' )
                ->addClass( 'chzn-select' );
            ?>

            <?= Former::select( 'cabinet' )
                ->label( 'Cabinet' )
                ->fromQuery( $t->cabinets, 'name' )
                ->placeholder( 'Choose a Cabinet' )
                ->addClass( 'chzn-select' );
            ?>

            <?= Former::textarea( 'description' )
                ->label( 'Description' )
                ->rows( 5 )
                ->blockHelp( '' );
            ?>

            <?= Former::close() ?>

        </div>

    <?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>