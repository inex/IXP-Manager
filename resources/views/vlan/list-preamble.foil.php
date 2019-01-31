
<?php if( isset( $t->feParams->infra ) ): ?>
    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center">
                <i class="fa fa-info-circle fa-2x"></i>
            </div>
            <div class="col-sm-12 d-flex">
                <div class="mr-auto">
                    Only showing <?php if( isset( $t->feParams->publicOnly ) and $t->feParams->publicOnly ): ?> public <?php endif; ?>
                    VLANs for: <b><?=  $t->feParams->infra->getName() ?></b>.
                </div>



                <div class="btn-group btn-group-sm" role="group">

                    <?php if( isset( $t->feParams->publicOnly ) and $t->feParams->publicOnly ): ?>
                        <a href="<?= route( $t->feParams->route_prefix . '@infra',       [ 'id' => $t->feParams->infra->getId() ] )                 ?>" class='btn btn-outline-info'>
                            Include Private
                        </a>
                    <?php else: ?>
                        <a href="<?= route( $t->feParams->route_prefix . '@infraPublic', [ 'id' => $t->feParams->infra->getId(), 'public' => 1 ] )  ?>" class='btn btn-outline-info'>
                            Public Only
                        </a>
                    <?php endif; ?>

                    <a href="<?= route( $t->feParams->route_prefix . '@list' ) ?>" class='btn btn-outline-info'>
                        Show All VLANs
                    </a>
                </div>
            </div>

        </div>
    </div>
<?php endif; ?>
