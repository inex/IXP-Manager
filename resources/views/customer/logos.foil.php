<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?> / Logos
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<div class="btn-group btn-group-sm ml-auto" role="group">
    <a target="_blank" class="btn btn-white" href="<?= route( 'logo@logos', [ 'bg' => request('bg') ? 0 : 1 ] ) ?>">
        <?= request('bg') ? 'Transparent' : 'Add Background' ?>
    </a>
</div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-md-12">
            <div class="row content-center">
                <?php foreach( $t->customers as $cust ): ?>
                    <a class="mx-2 col-lg-3 col-sm-6 my-2 tw-bg-white rounded-t-lg border tw-border-gray-400 p-4 justify-center tw-shadow-md hover:tw-bg-grey-lighter text-center" href="<?= route( "logo@manage" , [ "id" => $cust->id ] ) ?>">
                        <div>
                            <div class="tw-h-24 <?= request('bg') ? 'tw-bg-gray-200' : '' ?>">
                                <img class="img-fluid mx-auto" src="<?= url( 'logos/'.$cust->logo->shardedPath() ) ?>" />
                            </div>
                            <hr class="tw-mt-4 tw-mb-2">
                            <h5>
                                <?= $cust->name ?>
                            </h5>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>