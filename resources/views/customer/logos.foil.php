<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>

    Customer / Logos

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">

        <div class="col-md-12">
            <div class="row">
                <?php $count = 0 ?>
                <?php foreach( $t->logos as $logo ): ?>

                    <div class="col-sm-3">

                        <a href="<?= route( "logo@manage" , [ "id" => $logo->getCustomer()->getId() ] ) ?>">
                            <img class="www80-padding img-responsive" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                        </a>

                    </div>

                    <?php $count++ ?>

                <?php endforeach; ?>

            </div>
        </div>

    </div>



<?php $this->append() ?>

