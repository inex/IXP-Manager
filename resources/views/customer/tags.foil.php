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

        <div class="col-md-12">

            <?= $t->alerts() ?>

            <div class="well">

                <?= Former::open()->method( 'POST' )
                    ->action( route ('customer@store-tags' ) )
                    ->customInputWidthClass( 'col-sm-6' )
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

                <div class="col-sm-12 text-center mt-4 bg-light p-3 shadow-sm">
                    <?= Former::actions( Former::primary_submit( 'Save Changes' ),
                        Former::secondary_link( 'Cancel' )->href( route( "customer@overview" , [ "id" => $t->c->getId() ] ) ),
                        Former::success_button( 'Help' )->id( 'help-btn' )
                    );?>
                </div>

                <?= Former::hidden( 'id' )
                    ->value( $t->c ? $t->c->getId() : '' )
                ?>

                <?= Former::close() ?>

            </div>

        </div>

    </div>

<?php $this->append() ?>