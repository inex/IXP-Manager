<div class="row">
    <div class="col-xl-6 col12">
        <div class="card mb-4">
            <div class="card-header  d-flex">
                <div class="mr-auto">
                    <h5>
                        Aggregate Traffic Statistics
                    </h5>
                </div>

                <a class="btn btn-white btn-sm my-auto" href="<?= route( "statistics@member-drilldown", [ 'type' => 'agg', 'typeid' => $t->c->id ] )?>">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <div class="card-body">
                <?= $t->grapher->customer( $t->c )->renderer()->boxLegacy() ?>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-12">
        <?= $t->insert( 'dashboard/dashboard-tabs/associate' ); ?>
    </div>

    <?php if( $t->logoManagementEnabled() ): ?>
        <div class="col-lg-12 mt-4">
            <h3>Your Logo</h3>

            <div class="row col-sm-12">
                <?php if( $logo = $t->c->logo ): ?>
                    <div class="col-sm-6">
                        This is your current logo.<br/>
                        Please <a href="<?= route( 'logo@manage', [ 'id' => $t->c->id ] ) ?>">click here</a> to change it.
                    </div>
                    <div class="col-sm-6">
                        <img class="img-responsive" src="<?= url( 'logos/' . $logo->shardedPath() ) ?>" />
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger mt-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="col-sm-12">
                                No logo uploaded which means it is not currently displayed on our public
                                website.
                                Please <a href="<?= route( 'logo@manage', [ 'id' => $t->c->id ] ) ?>">click here</a>
                                to add one now.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>