<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Tags /
    <a href="<?= route( 'customer@overview', [ 'cust' => $t->c->id ] ) ?>">
        <?= $t->c->getFormattedName() ?>
    </a>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>
            <?php if( $t->tags->count() ): ?>
                <div class="card">
                    <div class="card-body">
                        <?= Former::open()->method( 'POST' )
                            ->action( route ('customer-tag@link', [ 'cust' => $t->c->id ] ) )
                            ->customInputWidthClass( 'col-sm-6' )
                            ->customLabelWidthClass( 'col-sm-2 col-2' )
                            ->actionButtonsCustomClass( "grey-box")
                        ?>

                        <?php foreach( $t->tags as $tag ): ?>
                            <div class="form-group row">
                                <label for="tags" class="control-label col-sm-2 col-2"> </label>
                                <div class="col-sm-6 col-md-6 col-lg-8 col-xl-6">
                                    <div class="form-check form-check-inline">
                                        <input id='tag_<?= $tag->id ?>' type='checkbox' name='tags[]' <?= $t->c->tags->contains( 'id', $tag->id ) ? 'checked' : ''  ?> value='<?= $tag->id ?>'>
                                        <label for="tag_<?= $tag->id ?>" class="form-check-label">
                                            <?= $tag->display_as . " (" . $tag->tag . ")" ?>
                                        </label>
                                    </div>
                                  <small class="form-text text-muted former-help-text">
                                      <?= $tag->description ?>
                                  </small>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?= Former::actions( Former::primary_submit( 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                            Former::secondary_link( 'Cancel' )->href( route( "customer@overview" , [ 'cust' => $t->c->id ] ) )->class( "mb-2 mb-sm-0" ),
                            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                        );?>

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
                            No Customer tag available.
                            <a class="btn btn-white" href="<?= route( "customer-tag@create" ) ?>">
                                Create one
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>