<div class="row">

    <div class="col-sm-12">

        <br>

        <div class=" col-sm-12">

            <div class="well col-sm-6">
                <h3>
                    Aggregate Traffic Statistics
                    <a class="btn btn-default" href="<?= route( "statistics@member-drilldown", [ 'type' => 'agg', 'typeid' => $t->c->getId() ] )?>">
                        <i class="glyphicon glyphicon-zoom-in"></i>
                    </a>
                </h3>

                <?= $t->grapher->customer( $t->c )->renderer()->boxLegacy() ?>
            </div>

            <div class="col-sm-6">

                <?= $t->insert( 'dashboard/dashboard-tabs/associate' ); ?>

            </div>

        </div>


        <?php if( $t->logoManagementEnabled() ): ?>

            <div class="row-fluid">
                <h3>Your Logo</h3>

                <?php if( $logo = $t->c->getLogo( Entities\Logo::TYPE_WWW80 ) ): ?>
                    <div class="col-sm-3">
                        Your actual logo.<br/>
                        Please <a href="<?= route( 'logo@manage', [ 'id' => $t->c->getId() ] ) ?>">click here</a> to change it.
                    </div>
                    <div class="col-sm-3">
                        <img class="www80-padding img-responsive" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                    </div>

                <?php else: ?>

                    <div class="alert alert-danger">
                        No logo uploaded which means it is not currently displayed on our public
                        website.
                        Please <a href="<?= route( 'logo@manage', [ 'id' => $t->c->getId() ] ) ?>">click here</a>
                        to add one now.
                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>



    </div>

</div>