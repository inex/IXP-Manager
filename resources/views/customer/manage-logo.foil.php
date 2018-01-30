<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route ( 'customer@list' )?>">
        Customer
    </a>
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

<?php else: ?>

<?php endif; ?>

<?php $this->section( 'content' ) ?>

<?= $t->alerts() ?>

<?php if( $t->logo ): ?>
    <div class="row">
        <div class="col-md-6">
            <h3>Your Existing Logo:</h3>
        </div>
        <div class="col-md-6">
            <img src="<?= url( 'logos/'.$t->logo->getShardedPath() ) ?>" class="www80-padding">

        </div>
    </div>
    <div class="row-fluid">
        <br><br>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <p>
            If you would like your logo displayed on our website, please <?= $t->logo ? "replace" : "add" ?> your logo below.
        </p>

        <div class="alert alert-info">
            <strong>For best results, your logo must be uploaded as a PNG image with a <u>transparent background</u> that is 80px high and without any margin.</strong>
        </div>

        <p>
            Any other size will be scaled to 80px high which may affect the quality. We'll add a margin when displaying so you should not include any.
        </p>
        <p>
            If your logo does not conform to these specifications, we will remove it as it will not look appropriate to our website design.
            <br><br><br>
        </p>

        <?= Former::open()->method( 'POST' )
            ->action( route ('customer@storeLogo' ) )
            ->enctype( "multipart/form-data" )
            ->customWidthClass( 'col-sm-3' )
            ->addClass( 'col-md-10' );
        ?>

            <?= Former::file( 'logo' )
                ->label( 'Upload a PNG logo' )
                ->accept( 'png' );

            ?>

            <?=Former::actions( Former::primary_submit( 'Upload' ),
                Former::default_link( 'Cancel' )->href( Auth::getUser()->isSuperUser() ? route( "customer@overview" , [ "id" => $t->c->getId() ] ) : url( "dashboard/index" ) ),
                Former::success_button( 'Help' )->id( 'help-btn' )
            );?>

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
            let urlAction = '<?= route( "customer@deleteLogo" , [ 'id' => $t->c->getId() ] )  ?>';

            $.ajax( urlAction, {
                type: 'POST'
            })
            .done( function( data ) {
                if( data.superUser ){
                    window.location.href = "<?= route( "customer@overview" , [ "id" => $t->c->getId() ] ) ?>";
                } else {
                    window.location.href = "<?= url( 'dashboard/index' ) ?>";
                }

            })
            .fail( function(){
                alert( 'Could not update notes. API / AJAX / network error' );
                throw new Error("Error running ajax query for "+urlAction);
            })
            .always( function() {
                $('#notes-modal').modal('hide');
            });
        });


    </script>
<?php $this->append() ?>
