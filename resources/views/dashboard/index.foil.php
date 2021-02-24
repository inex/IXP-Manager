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

    <div class="col-lg-12">


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

                        <?php if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) ): ?>
                            <li class="nav-item" onclick="window.location.href = '<?= route( 'docstore-c-dir@list', [ 'cust' => $t->c->getId() ] ) ?>'">
                                <a class="nav-link" data-toggle="tab" href="">
                                    Documents &raquo;
                                </a>
                            </li>
                        <?php endif; ?>


                        <?php if( $t->c->getType() != \Entities\Customer::TYPE_ASSOCIATE && ( ! $t->c->hasLeft() ) ): ?>


                            <?php if( $t->c->isRouteServerClient() ): ?>


                                <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes' ) ): ?>
                                    <li class="nav-item" onclick="window.location.href = '<?= route( "rs-prefixes@view", [ 'cid' =>  $t->c->getId() ] ) ?>'">
                                        <a class="nav-link" data-toggle="tab"  href="">
                                            RS Prefixes
                                            <?php if( $t->rsRoutes[ 'adv_nacc' ][ 'total' ] > 0 ): ?>
                                                <span class="badge badge-danger"><?= $t->rsRoutes[ 'adv_nacc' ][ 'total' ] ?></span>
                                            <?php endif ?>
                                            &raquo;
                                        </a>
                                    </li>
                                <?php endif ?>


                                <?php if( !config( 'ixp_fe.frontend.disabled.filtered-prefixes' ) ): ?>

                                    <li class="nav-item" onclick="window.location.href = '<?= route( "filtered-prefixes@list", [ 'customer' =>  $t->c->getId() ] ) ?>'">
                                        <a class="nav-link" data-toggle="tab"  href="">
                                            Filtered Prefixes &raquo;
                                        </a>
                                    </li>

                                <?php elseif( $t->c->isIrrdbFiltered() ): ?>

                                    <li class="nav-item" onclick="window.location.href = '<?= route( "irrdb@list", [ "customer" => $t->c->getId(), "type" => 'prefix', "protocol" => $t->c->isIPvXEnabled( 4) ? 4 : 6 ] ) ?>'">
                                        <a class="nav-link" data-toggle="tab"  href="">
                                            IRRDB Entries &raquo;
                                        </a>
                                    </li>

                                <?php endif; ?>


                            <?php endif ?>

                            <?php if( config('grapher.backends.sflow.enabled') ) : ?>
                                <li class="nav-item" onclick="window.location.href = '<?= route( "statistics@p2p", [ 'cid' => $t->c->getId() ] )  ?>'">
                                    <a class="nav-link" data-toggle="tab" href="">P2P &raquo;</a>
                                </li>
                            <?php endif ?>

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

                            <?php if( $t->resellerMode() && $t->c->isReseller() ): ?>
                                <?= $t->insert( 'customer/overview-tabs/reseller-ports' ); ?>
                            <?php else: ?>
                                <?= $t->insert( 'customer/overview-tabs/ports' ); ?>
                            <?php endif ?>

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
        $('.table-responsive-ixp').show();

        $('.table-responsive-ixp').DataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
        } );

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust()
                .responsive.recalc();
        });

        <?php if( Auth::getUser()->isCustUser() ): ?>
            $( document ).ready(function() {
                $( "#details input" ).attr( "disabled", "disabled" );
                $( "#details select" ).attr( "disabled", "disabled" )
            });
        <?php endif; ?>
    </script>
<?php $this->append() ?>




