<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>

    Customer / Logos

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-md-12">
            <div class="row content-center">
                <?php foreach( $t->logos as $logo ): ?>
                    <a class="col-lg-3 col-sm-6 my-2 tw-bg-white rounded-t-lg border tw-border-gray-400 p-4 justify-center tw-shadow-md hover:tw-bg-grey-lighter text-center" href="<?= route( "logo@manage" , [ "id" => $logo->getCustomer()->getId() ] ) ?>">
                        <div>
                            <img class="img-fluid mx-auto" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                            <hr>
                            <h5>
                                <?= $logo->getCustomer()->getName() ?>
                            </h5>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php $this->append() ?>

