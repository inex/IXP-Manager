<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'page-header-preamble' ) ?>
    Your <?= config('identity.sitename' ) ?> Dashboard
<?php $this->append() ?>



<?php $this->section('content') ?>
<div class="row">

    <div class="col-sm-12">


        <?= $t->alerts() ?>
        <?php if( !$t->c->isTypeAssociate() ): ?>
            <div class="card mt-4">
                <div class="card-header">

                    <ul class="nav nav-tabs card-header-tabs">

                        <li class="nav-item">
                            <a class="nav-link <?php if( $t->tab == null || $t->tab == 'overview' || $t->tab == 'index' ): ?>active<?php endif; ?>" data-toggle="tab" href="#overview">
                                Overview
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php if( $t->tab == 'details' ): ?>active<?php endif; ?>" data-toggle="tab" href="#details">Details</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php if( $t->tab == 'ports' ): ?>active<?php endif; ?>" data-toggle="tab" href="#ports" data-toggle="tab">Ports</a>
                        </li>

                        <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php if( $t->tab == 'resold-customers' ): ?>active<?php endif; ?>" data-toggle="tab" href="#resold-customers" data-toggle="tab">Resold Customers</a>
                            </li>
                        <?php endif; ?>

                        <?php if( $t->notes ): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php if( $t->tab == 'notes' ): ?>active<?php endif; ?>" data-toggle="tab" href="#notes" id="tab-notes" data-toggle="tab">
                                    Notes
                                    <?php if( $t->notesInfo[ "unreadNotes"] > 0 ): ?>
                                        <span id="notes-unread-indicator" class="badge badge-success"><?= $t->notesInfo[ "unreadNotes"] ?></span>
                                    <?php endif ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link <?php if( $t->tab == 'cross-connect' ): ?>active<?php endif; ?>" data-toggle="tab" href="#cross-connects" data-toggle="tab">
                                Cross Connects
                            </a>
                        </li>

                        <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes' ) && $t->c->isRouteServerClient() ): ?>
                            <li class="nav-item" onclick="window.location.href = '<?= route( "rs-prefixes@view", [ 'id' =>  $t->c->getId() ] ) ?>'">
                                <a class="nav-link" data-toggle="tab"  href="">
                                    RS Prefixes
                                    <?php if( $t->rsRoutes[ 'adv_nacc' ][ 'total' ] > 0 ): ?>
                                        <span class="badge badge-danger"><?= $t->rsRoutes[ 'adv_nacc' ][ 'total' ] ?></span>
                                    <?php endif ?>
                                    &raquo;
                                </a>
                            </li>
                        <?php endif ?>

                        <?php if( !config( 'ixp_fe.frontend.disabled.peering-manager' ) ): ?>
                            <li class="nav-item">
                                <a class="nav-link" id="peering-manager-a" href=<?= url('') ?>/peering-manager>
                                    Peering Manager &raquo;
                                </a>
                            </li>
                        <?php endif ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= route( "statistics@member") ?>">
                                Statistics &raquo;
                            </a>
                        </li>

                        <?php if( config( 'grapher.backends.sflow.enabled' )  ): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= route( "statistics@p2p" , [ "cid" => $t->c->getId() ]) ?>">
                                    Peer to Peer Traffic &raquo;
                                </a>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
                <div class="card-body">

                    <div class="tab-content">

                        <div id="overview" class="tab-pane fade <?php if( $t->tab == null || $t->tab == 'overview' || $t->tab == 'index' ): ?> show active <?php endif; ?>">
                            <?= $t->insert( 'dashboard/dashboard-tabs/overview' ); ?>
                        </div>

                        <div id="details" class="tab-pane fade <?php if( $t->tab == 'details' ): ?> show active <?php endif; ?>">
                            <?= $t->insert( 'dashboard/dashboard-tabs/details' ); ?>
                        </div>

                        <div id="ports" class="tab-pane fade <?php if( $t->tab == 'ports' ): ?> show active <?php endif; ?>">
                            <div class="row">
                                <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>
                                    <?= $t->insert( 'customer/overview-tabs/reseller-ports' ); ?>
                                <?php else: ?>
                                    <?= $t->insert( 'customer/overview-tabs/ports' ); ?>
                                <?php endif ?>
                            </div>

                        </div>


                        <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>
                            <div id="resold-customers" class="tab-pane fade <?php if( $t->tab == 'resold-customers' ): ?> show active <?php endif; ?>">
                                <?= $t->insert( 'customer/overview-tabs/resold-customers' ); ?>
                            </div>
                        <?php endif ?>

                        <?php if( $t->notes ): ?>
                            <div id="notes" class="tab-pane fade <?php if( $t->tab == 'notes' ): ?> show active <?php endif; ?> ">
                                <?= $t->insert( 'customer/overview-tabs/notes' ); ?>
                            </div>
                        <?php endif ?>

                        <div id="cross-connects" class="tab-pane fade <?php if( $t->tab == 'cross-connects' ): ?> show active <?php endif; ?>">
                            <?= $t->insert( 'customer/overview-tabs/cross-connects' ); ?>
                        </div>

                    </div>
                </div>
            <?php else: ?>
                <?= $t->insert( 'dashboard/dashboard-tabs/associate' ); ?>
            <?php endif; ?>

    </div>
</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'customer/js/overview/notes' ); ?>
<script>

</script>
<?php $this->append() ?>
