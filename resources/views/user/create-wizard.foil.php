<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Users / Create
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/usage/users/">
            Documentation
        </a>
        <a class="btn btn-white" href="<?= route('user@list') ?>">
            <span class="fa fa-th-list"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="card">
        <div class="card-body">
            <?= $t->alerts() ?>
            <?= Former::open()->method( 'POST' )
                ->id( 'form' )
                ->action( route( 'user@create-check-email' ) )
                ->customInputWidthClass( 'col-lg-3 col-md-5 col-sm-6' )
                ->customLabelWidthClass( 'col-lg-2 col-md-3 col-sm-4' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

            <div class="col-sm-12">
                <?= Former::text( 'email' )
                    ->label( 'Email' )
                    ->placeholder( 'name@example.com' )
                    ->blockHelp( "The user's email address." );
                ?>
            </div>

            <?= Former::hidden( 'custid' )
                ->value( $t->custid );
            ?>

            <?= Former::hidden( 'cancelBtn' ) ?>

            <?= Former::actions(
                Former::primary_submit( 'Create' ),
                Former::secondary_link( 'Cancel' )->href( $t->custid ? route( "customer@overview" , [ 'cust' => $t->custid ] ) : route( "user@list" ) ),
                Former::success_button( 'Help' )->id( 'help-btn' )
            );
            ?>

            <?= Former::close() ?>
        </div>
    </div>
<?php $this->append() ?>