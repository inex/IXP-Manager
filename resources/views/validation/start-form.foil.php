<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Start System Validation
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="col-sm-12">
        <div class="card ">
            <div class="card-body">

                <?= $t->alerts() ?>

                <?= Former::open()->method( 'post' )
                    ->action( route ('validation@start-submit' ) )
                    ->customInputWidthClass( 'col-lg-4 col-sm-6' )
                    ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
                    ->actionButtonsCustomClass( "grey-box");
                ?>

                <?=Former::actions( Former::primary_submit( 'Run Validations' )->class( "mb-2 mb-sm-0"),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0")
                );?>

                <?= Former::close() ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<?php $this->append() ?>