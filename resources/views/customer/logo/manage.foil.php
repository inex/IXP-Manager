<?php
    $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'title' ) ?>

    <?php if( Auth::user()->isSuperUser() ): ?>

        <a href="<?= route ( 'customer@list' )?>">
            Customer
        </a>

    <?php else: ?>

        Manage Your Logo

    <?php endif; ?>

<?php $this->append() ?>



<?php if( Auth::user()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-postamble' ) ?>

        <li>
            <a href="<?= route( "customer@overview" , [ "id" => $t->c->getId() ] ) ?>" >
                <?= $t->ee( $t->c->getName() ) ?>
            </a>
        </li>
        <li>Manage Logo</li>

    <?php $this->append() ?>

<?php endif; ?>



<?php $this->section( 'content' ) ?>

    <?= $t->alerts() ?>

    <?php if( $t->logo ): ?>

        <div class="row">
            <div class="col-md-6">
                <h3>Your Existing Logo:</h3>
            </div>
            <div class="col-md-6">
                <img src="<?= url( 'logos/'.$t->logo->getShardedPath() ) ?>" class="www80-padding img-responsive">
            </div>
        </div>

        <div class="row-fluid">
            <br><br>
        </div>

    <?php endif; ?>


    <div class="row">
        <div class="col-md-12">

            <p>
                <?php if( $t->logo ): ?>

                    You have already provided a logo. If you would like to update it, please replace it below.

                <?php else: ?>

                    If you would like your logo displayed on our website, please <?= $t->logo ? "replace" : "add" ?> your logo below.

                <?php endif; ?>
            </p>


            <div class="alert alert-info">
                <strong>For best results, your logo must be uploaded as a PNG image with a <u>transparent background</u> that is 80px high and without any margin.</strong>
            </div>

            <p>
                Any other size will be scaled to 80px high which may affect the quality. We'll add a margin when displaying so you should not include any.
            </p>

            <p>
                If your logo does not conform to these specifications, we may remove it as it will not look appropriate to our website design.
                <br><br><br>
            </p>

            <?= Former::open()->method( 'POST' )
                ->action( route ('logo@store' ) )
                ->enctype( "multipart/form-data" )
                ->customWidthClass( 'col-sm-3' )
                ->addClass( 'col-md-10' );
            ?>

            <?= Former::file( 'logo' )
                ->label( 'Upload a PNG logo' )
                ->accept( 'png' );

            ?>

            <?= Former::actions(
                    Former::primary_submit( 'Upload' ),
                    Former::default_link( 'Cancel' )->href( Auth::getUser()->isSuperUser() ? route( "customer@overview" , [ "id" => $t->c->getId() ] ) : url( "dashboard/index" ) ),
                    Auth::getUser()->isSuperUser() ? Former::success_link( 'Help' )->href('http://docs.ixpmanager.org/usage/customers/#customer-logos') : ''
                );
            ?>

            <?= Former::hidden( 'id' )
                ->value( $t->c ? $t->c->getId() : false )
            ?>

            <?= Former::close() ?>


            <?php if( $t->logo ): ?>

                <div class="col-md-12">
                    <hr>
                    If you want to remove your logo, please click here:
                    <a id="delete" class="btn btn-danger" href="">
                        Remove My Logo
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </div>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <script>

        $( "#delete" ).on( 'click', function(e){
            e.preventDefault();
            let urlAction = '<?= route( "logo@delete" , [ 'id' => $t->c->getId() ] )  ?>';

            $.ajax( urlAction, {
                type: 'POST'
            })
            .done( function( data ) {
                window.location.href = "<?= Auth::user()->isSuperUser() ? route( 'customer@overview', [ 'id' => $t->c->getId() ] ) : url( 'dashboard/index' ) ?>";
            })
            .fail( function(){
                alert( 'Could not delete logo. API / AJAX / network error' );
                throw new Error("Error running ajax query for "+urlAction);
            })
        });

    </script>
<?php $this->append() ?>
