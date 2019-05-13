<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'page-header-preamble' ) ?>

    <a href="<?= route( 'customer@overview', [ 'id' => $t->c->getId() ] ) ?>">
        <?= $t->c->getFormattedName() ?>
    </a>
    /
    Tags

<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">

        <div class="col-lg-12">

            <?= $t->alerts() ?>


            <?php if( count( $t->tags ) > 0 ): ?>
                <div class="card">
                    <div class="card-body">
                        <?= Former::open()->method( 'POST' )
                            ->action( route ('customer@store-tags' ) )
                            ->customInputWidthClass( 'col-sm-6' )
                            ->customLabelWidthClass( 'col-sm-2 col-2' )
                            ->actionButtonsCustomClass( "grey-box")
                        ?>


                            <?php foreach( $t->tags as $tag ): ?>

                                <?= Former::checkbox( 'tag-' . $tag->getId() )
                                    ->label( ' ' )
                                    ->text( $tag->getDisplayAs() . " (" . $tag->getTag() . ")" )
                                    ->value( 1 )
                                    ->blockHelp( $tag->getDescription() )
                                    ->inline()
                                    ->check( array_key_exists( $tag->getId(), $t->selectedTags ) ? true : false );
                                ?>

                            <?php endforeach; ?>



                            <?= Former::actions( Former::primary_submit( $t->c ? 'Save Changes' : 'Add' )->class( "mb-2 mb-sm-0" ),
                                Former::secondary_link( 'Cancel' )->href( route( "customer@overview" , [ "id" => $t->c->getId() ] ) )->class( "mb-2 mb-sm-0" ),
                                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                            );?>


                        <?= Former::hidden( 'id' )
                            ->value( $t->c ? $t->c->getId() : '' )
                        ?>

                        <?= Former::close() ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            No Customer tag available.  <a class="btn btn-white" href="<?= route( "customer-tag@add" ) ?>">Add one</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>

<?php $this->append() ?>