<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?=  $t->feParams->pagetitle  ?>
    /
    Add

<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">

        <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
            <a target="_blank" class="btn btn-outline-secondary" href="<?= $t->feParams->documentation ?>">
                Documentation
            </a>
        <?php endif; ?>

        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
            <?php if( Route::has( $t->feParams->route_prefix . '@list' ) ): ?>
                <a class="btn btn-outline-secondary" href="<?= route($t->feParams->route_prefix.'@list') ?>">
                    <span class="fa fa-th-list"></span>
                </a>
            <?php endif; ?>
        <?php endif;?>

    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="card">
    <div class="card-body">

        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( 'user@add-check-email' ) )
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

        <?php
        // need to figure out where the cancel button foes. shouldn't be this hard :-(
        if( session()->get( 'user_post_store_redirect' ) === 'user@list' || session()->get( 'user_post_store_redirect' ) === 'user@add' ) {
            $cancel_url = route('user@list' );
        } else {
            $custid = null;
            if( isset( $t->data[ 'params'][ 'object'] ) && $t->data[ 'params'][ 'object'] instanceof \Entities\User ) {
                $custid = $t->data[ 'params'][ 'object']->getCustomer()->getId();
            } else if( session()->get( 'user_post_store_redirect_cid', null ) !== null ) {
                $custid = session()->get( 'user_post_store_redirect_cid' );
            }

            if( $custid !== null ) {
                $cancel_url = route( 'customer@overview', [ "id" => $custid,  "tab" => "users" ] );
            } else {
                $cancel_url = route( 'user@list' );
            }
        }

        ?>


        <?= Former::actions(
            Former::primary_submit( 'Add' ),
            Former::secondary_link( 'Cancel' )->href( $cancel_url ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        );
        ?>

        <?= Former::close() ?>

    </div>
</div>
<?php $this->append() ?>