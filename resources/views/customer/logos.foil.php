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

                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 my-4">

                        <a href="<?= route( "logo@manage" , [ "id" => $logo->getCustomer()->getId() ] ) ?>">
                            <img class="img-fluid" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                        </a>

                    </div>

                    <?php $count++ ?>

                <?php endforeach; ?>

            </div>
        </div>
    </div>

<?php $this->append() ?>

