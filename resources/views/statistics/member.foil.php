<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'title' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( 'customer@list' )?>">Customers</a>
       <li>
           <a href="<?= route( 'customer@overview', [ 'id' => $t->c->getId() ] )?>" >
               <?= $t->c->getFormattedName() ?>
           </a>
       </li>
    <?php else: ?>
        IXP Port Graphs :: <?= $t->c->getFormattedName() ?>
    <?php endif; ?>
<?php $this->append() ?>

<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <li>
            Statistics
            (
                <?= IXP\Services\Grapher\Graph::resolveCategory( $t->category ) ?>
                /
                <?= IXP\Services\Grapher\Graph::resolvePeriod( $t->period ) ?>
            )
        </li>
    <?php $this->append() ?>
<?php endif; ?>



<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <nav class="navbar navbar-default">

                <div class="navbar-header">
                    <a class="navbar-brand">Graph Options:</a>
                </div>

                <form class="navbar-form navbar-left form-inline"  action="<?= route( "statistics@member", [ "id" => $t->c->getId() ] ) ?>" method="GET">

                    <div class="form-group">
                        <label for="category">Type:</label>
                        <select id="category" name="category" onchange="" class="form-control">
                            <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $cvalue => $cname ): ?>
                                <option value="<?= $cvalue ?>" <?php if( $t->category == $cvalue ): ?> selected <?php endif; ?> ><?= $cname ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="period">Period:</label>
                        <select id="period" name="period" onchange="" class="form-control" placeholder="Select State">
                            <option></option>
                            <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pvalue => $pname ): ?>
                                <option value="<?= $pvalue ?>" <?php if( $t->period == $pvalue ): ?> selected <?php endif; ?>  ><?= $pname ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-default">Change</button>

                </form>

                <?php if( config('grapher.backends.sflow.enabled') && $t->grapher()->canAccessAllCustomerP2pGraphs() ): ?>
                    <form class="navbar-form navbar-right form-inline">
                        <a class="btn btn-default" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] ) ?>">
                            <span class="glyphicon glyphicon-random"></span>&nbsp;&nbsp;P2P Graphs
                        </a>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </form>
                <?php endif; ?>
            </nav>



            <div class="row col-sm-6">

                <div class="well">
                    <h3>
                        Aggregate Peering Traffic
                        <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>
                            <small><em>(Peering ports only)</em></small>
                        <?php endif; ?>

                        <div class="btn-group pull-right">
                            <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                                <a class="btn btn-default btn-sm" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] ) ?>">
                                    <span class="glyphicon glyphicon-random"></span>
                                </a>
                            <?php endif; ?>
                            <a class="btn btn-default btn-sm" href="<?= route( "statistics@member-drilldown" , [ "typeid" => $t->c->getId(), "type" => "agg" ] ) ?>/?category=<?= $t->category ?>">
                                <i class="glyphicon glyphicon-zoom-in"></i>
                            </a>
                        </div>
                    </h3>
                    <p>
                        <br />
                        <?= $t->grapher->customer( $t->c )->setCategory( $t->category )->setPeriod( $t->period )->renderer()->boxLegacy() ?>
                    </p>
                </div>
            </div>



            <?php
                /** @var Entities\VirtualInterface $vi */
                foreach( $t->c->getVirtualInterfaces() as $vi ): ?>


                <div class="well col-sm-12" style="background-color: #fafafa">

                    <?php
                        if( !isset( $vi->getPhysicalInterfaces()[ 0 ] ) ) {
                            continue;
                        }

                        $pi = $vi->getPhysicalInterfaces()[ 0 ];
                        $isLAG = count( $vi->getPhysicalInterfaces() ) > 1;
                    ?>


                    <?php if( $isLAG ): ?>

                        <div class="col-sm-6">
                            <div class="well">
                                <h4>
                                    LAG on <?= $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ?>
                                    / <?= $pi->getSwitchPort()->getSwitcher()->getName() ?>

                                    <?php if( $vi->isGraphable() ): ?>
                                        <div class="btn-group pull-right">

                                            <?= $t->insert( 'statistics/snippets/latency-dropup', [ 'vi' => $vi ] ) ?>

                                            <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                                                <a class="btn btn-default btn-sm" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] )
                                                    . ( $vi->getVlanInterfaces() ? '?svli=' . $vi->getVlanInterfaces()[0]->getId() : '' )
                                                ?>">
                                                    <span class="glyphicon glyphicon-random"></span>
                                                </a>
                                            <?php endif; ?>

                                            <a class="btn btn-default btn-sm" href="<?= route( "statistics@member-drilldown" , [ "type" => "vi", "typeid" => $vi->getId()  ] ) ?>/?category=<?= $t->category ?>" title="Drilldown">
                                                <i class="glyphicon glyphicon-zoom-in"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </h4>
                                <p>
                                    <?php if( $vi->isGraphable() ): ?>
                                        <br />
                                        <?= $t->grapher->virtint( $vi )->setCategory( $t->category )->setPeriod( $t->period )->renderer()->boxLegacy() ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                    <?php endif; ?>


                    <?php foreach( $vi->getPhysicalInterfaces() as $idx => $pi ): ?>

                        <div class="col-sm-6 <?php if( $isLAG && $idx > 0 ): ?>col-md-offset-6 <?php endif; ?>">

                            <div class="well">

                                <h4>
                                    <?php if( $isLAG ): ?>
                                        <?= $pi->getSwitchPort()->getSwitcher()->getName() ?> ::
                                        <?= $pi->getSwitchPort()->getName() ?> (<?=$pi->resolveSpeed() ?>)
                                    <?php else: ?>
                                        <?= $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ?>
                                            / <?= $pi->getSwitchPort()->getSwitcher()->getName() ?> (<?=$pi->resolveSpeed() ?>)
                                    <?php endif; ?>

                                    <?php if( $pi->statusIsConnectedOrQuarantine() ): ?>
                                        <div class="btn-group pull-right">
                                            <?php if( !$isLAG ): ?>
                                                <?= $t->insert( 'statistics/snippets/latency-dropup', [ 'vi' => $vi ] ) ?>
                                            <?php endif; ?>
                                            <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                                                <a class="btn btn-default btn-sm" href="<?= route( 'statistics@p2p', [ 'cid' => $t->c->getId() ] )
                                                . ( $vi->getVlanInterfaces()[0] ? '?svli=' . $vi->getVlanInterfaces()[0]->getId() : '' )
                                                ?>">
                                                    <span class="glyphicon glyphicon-random"></span>
                                                </a>
                                            <?php endif; ?>
                                            <a class="btn btn-default btn-sm" href="<?= route( "statistics@member-drilldown" , [ "type" => "pi", "typeid" => $pi->getId()  ] ) ?>/?category=<?= $t->category ?>">
                                                <i class="glyphicon glyphicon-zoom-in"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <small>
                                        <br />
                                        <?php if( !$isLAG ): ?>
                                            <?= $pi->getSwitchPort()->getName() ?>
                                        <?php endif; ?>

                                        <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>
                                            <br />
                                            <?php if( $pi->getSwitchPort()->isTypePeering() ): ?>
                                            Peering Port
                                            <?php elseif( $pi->getSwitchPort()->isTypeFanout() ): ?>
                                                Fanout Port for <a href="<?= route( 'customer@overview', [ 'id' => $pi->getRelatedInterface()->getVirtualInterface()->getCustomer()->getId() ] ) ?>">
                                                <?= $pi->getRelatedInterface()->getVirtualInterface()->getCustomer()->getAbbreviatedName() ?>
                                            </a>
                                            <?php elseif( $pi->getSwitchPort()->isTypeReseller() ): ?>
                                                Reseller Uplink Port
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </small>
                                </h4>

                                <p>
                                    <?php if( $pi->statusIsConnectedOrQuarantine() ): ?>
                                        <br />
                                        <?= $t->grapher->physint( $pi )->setCategory( $t->category )->setPeriod( $t->period )->renderer()->boxLegacy() ?>
                                    <?php else: ?>
                                        <?= $t->insert( 'customer/overview-tabs/ports/pi-status', [ 'pi' => $pi ] ) ?>
                                    <?php endif; ?>
                                </p>

                            </div>
                        </div>

                    <?php endforeach; /* $vi->getPhysicalInterfaces() */ ?>
                </div>

            <?php endforeach; /* $t->c->getVirtualInterfaces() */ ?>
        </div>
        
    </div>
<?php $this->append() ?>