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


        <?php if( $t->c->routeServerFiltersInProduction()->count() && !config( 'ixp_fe.frontend.disabled.rs-filters') ): ?>

            <div class="alert alert-info mb-16" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center"><i class="fa fa-info-circle fa-2x "></i></div>
                    <div class="col-sm-12">
                        You have <?= $t->c->routeServerFiltersInProduction()->count() ?> active route server filter(s) configured.
                        <a href="<?= route('rs-filter@list', [ 'cust' => Auth::getUser()->customer ] ) ?>">Click here</a> to view/edit them.
                    </div>
                </div>
            </div>

        <?php endif; ?>


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

    <div class="col-xl-6 col-12">
        <?= $t->insert( 'dashboard/dashboard-tabs/associate' ); ?>

        <?php if( count( $t->p2pstats ) ): ?>

            <div class="col-12">
                <h4>Your Top Peers</h4>
                <div class="mb-4 tw-text-sm">
                    Your top peers <?= \Carbon\Carbon::parse( $t->p2pstats[0]->day )->diffForHumans() ?>.
                    See all <a href="<?= route( 'statistics@p2ps-get', [ 'customer' => $t->c->id ] ) ?>">here</a>.
                </div>

                <table  class="table table-sm table-hover" >
                    <thead class="thead-dark">
                    <tr>
                        <th>
                            Peer
                        </th>
                        <th class="tw-text-right">
                            Total Traffic
                        </th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        /** @var \IXP\Models\P2pDailyStats $p2p */
                    foreach( $t->p2pstats as $p2p ):
                ?>
                    <tr>
                        <td>
                            <?= $p2p->peer->abbreviatedName ?>
                        </td>
                        <td class="tw-text-right tw-font-mono">
                            <?= \IXP\IXP::scaleBytes( $p2p->total_traffic() ) ?>
                        </td>
                        <td>
                            <a class="btn btn-white btn-sm my-auto" href="<?= route( "statistics@p2p-get", [ 'srcVli' => $t->c->virtualInterfaces[0]->vlanInterfaces[0]->id,
                                         'dstVli' => $p2p->peer->virtualInterfaces[0]->vlanInterfaces[0]->id ] )?>">
                                <i class="fa fa-search"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>


</div>