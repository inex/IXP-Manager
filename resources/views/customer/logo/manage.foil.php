<?php
    $this->layout( 'layouts/ixpv4' );
    $isSuperUser = Auth::getUser()->isSuperUser();
?>

<?php if( $isSuperUser ): ?>
    <?php $this->section( 'page-header-preamble' ) ?>
        <a href="<?= route( "customer@overview" , [ 'cust' => $t->c->id ] ) ?>" >
            <?= $t->ee( $t->c->name ) ?>
        </a>
        /
        Manage Logo
    <?php $this->append() ?>
<?php endif; ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>

            <?php if( $t->logo ): ?>
                <div class="row mb-4">
                    <div class="col-md-6 col-sm-12">
                        <h3>Your Existing Logo:</h3>
                    </div>
                    <div class="col-md-6 text-center col-sm-12">
                        <img src="<?= url( 'logos/' . $t->logo->shardedPath() ) ?>" class="www80-padding img-responsive">
                    </div>
                </div>
            <?php endif; ?>

            <p>
                <?php if( $t->logo ): ?>
                    You have already provided a logo. If you would like to update it, please replace it below.
                <?php else: ?>
                    If you would like your logo displayed on our website, please <?= $t->logo ? "replace" : "upload" ?> your logo below.
                <?php endif; ?>
            </p>

            <div class="alert alert-info mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        <b>
                            For best results, your logo must be uploaded as a PNG image with a <u>transparent background</u> that is 80px high and without any margin.
                        </b>
                    </div>
                </div>
            </div>
            <p>
                Any other size will be scaled to 80px high which may affect the quality. We'll add a margin when displaying so you should not include any.
            </p>
            <p>
                If your logo does not conform to these specifications, we may remove it as it will not look appropriate to our website design.
            </p>

            <div class="card">
                <div class="card-body">
                    <?= Former::open()->method( 'POST' )
                        ->action( route ('logo@store' ) )
                        ->enctype( "multipart/form-data" )
                        ->customInputWidthClass( 'col-sm-3' )
                        ->actionButtonsCustomClass( "grey-box")
                        ->addClass( 'col-md-12 mt-4' );
                    ?>

                    <?= Former::file( 'logo' )
                        ->label( 'Upload a PNG logo' )
                        ->accept( 'png' );
                    ?>

                    <?= Former::actions(
                        Former::primary_submit( 'Upload' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href( $isSuperUser ? route( "customer@overview" , [ 'cust' => $t->c->id ] ) : route( "dashboard@index" ) )->class( "mb-2 mb-sm-0" ),
                        $isSuperUser ? Former::success_link( 'Help' )->href('http://docs.ixpmanager.org/usage/customers/#customer-logos')->class( "mb-2 mb-sm-0" ) : ''
                    );
                    ?>

                    <?= Former::hidden( 'id' )
                        ->value( $t->c->id ?? false )
                    ?>

                    <?= Former::close() ?>
                </div>
            </div>

            <?php if( $t->logo ): ?>
                <div class="alert alert-danger mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="col-sm-12 d-flex">
                            <b class="mr-auto my-auto">
                                Delete your logo ...
                            </b>
                            <a id="btn-delete" class="btn btn-danger mr-4 " href="<?= route( "logo@delete" , [ 'id' => $t->c->id ] )  ?>">
                                Remove My Logo
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'customer/logo/js/manage' ); ?>
<?php $this->append() ?>