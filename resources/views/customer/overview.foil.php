<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    
    /** @var Entities\Customer $c */
    $c = $t->c;
?>


<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm ml-auto" role="group" aria-label="...">

        <a class="btn btn-white" href="<?= route('statistics@member', [ 'id' => $c->getId() ] ) ?>">
            Port Graphs
        </a>

        <div class="btn-group btn-group-sm">
            <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-cog"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right">

                <a class="dropdown-item" href="<?= route( 'interfaces/virtual/add-wizard/custid', [ 'id' => $c->getId() ] ) ?>">
                    Provision new port...
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="<?= route( 'customer@welcome-email', [ 'id' => $c->getId() ] ) ?>">
                    Send Welcome Email...
                </a>

            </div>
        </div>

        <div class="btn-group btn-group-sm">

            <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-pencil"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">

                <a class="dropdown-item" href="<?= route( 'customer@edit' , [ 'id' => $c->getId() ] ) ?>">
                    Edit Customer Details
                </a>

                <a class="dropdown-item" href="<?= route( 'customer@billing-registration' , [ 'id' => $c->getId() ] ) ?>" >
                    <?php if( !config('ixp.reseller.no_billing') || !$t->resellerMode() || !$c->isResoldCustomer() ): ?>
                        Edit Billing/Registration Details
                    <?php else: ?>
                        Edit Registration Details
                    <?php endif; ?>
                </a>


                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="<?= route( 'customer@tags', [ 'id' => $c->getId() ] ) ?>">
                    Manage Tags...
                </a>


                <?php if( $t->logoManagementEnabled() ): ?>
                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?= route( 'logo@manage', [ 'id' => $c->getId() ] ) ?>">
                        Manage Logo...
                    </a>

                <?php endif; ?>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="<?= route( 'customer@delete-recap', [ 'id' => $c->getId() ] ) ?>">Delete Customer...</a>

            </div>

        </div>

        <?php $haveprev = $havenext = 0 ?>
        <?php $keyCustomers = array_keys( $t->customers ) ?>
        <?php foreach( $t->customers as $id => $name ): ?>

            <?php if( $id == reset( $keyCustomers ) ): ?>
                <?php $cidprev = $id ?>
            <?php endif; ?>

            <?php if( $id == $c->getId() ): ?>
                <?php $haveprev = 1 ?>
            <?php elseif( $haveprev && !$havenext ): ?>
                <?php $havenext = 1 ?>
                <?php $cidnext = $id ?>
            <?php endif; ?>

            <?php if( !$haveprev ): ?>
                <?php $cidprev = $id ?>
            <?php endif; ?>

            <?php if( !$havenext and end( $keyCustomers ) ): ?>
                <?php $cidnext = $id ?>
            <?php endif; ?>

        <?php endforeach; ?>

        <a class="btn btn-white" href="<?= route( "customer@overview", [ 'id' => $cidprev ] ) ?>">
            <span class="fa fa-chevron-left"></span>
        </a>
        <a class="btn btn-white" href="<?= route( "customer@overview", [ 'id' => $c->getId() ] ) ?>">
            <span class="fa fa-refresh"></span>
        </a>
        <a class="btn btn-white" href="<?= route( "customer@overview", [ 'id' => $cidnext ] ) ?>">
            <span class="fa fa-chevron-right"></span>
        </a>

    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <div class="tw-bg-white shadow-sm tw-p-6">

        <div class="row">
            <div class="<?= $t->logoManagementEnabled() && ( $logo = $c->getLogo( Entities\Logo::TYPE_WWW80 ) ) ? "col-md-9 col-lg-7" : "col-12" ?>">

                <h3>
                    <?= $t->ee( $c->getFormattedName() ) ?>
                    <span class="tw-text-sm"><?= $t->insert( 'customer/cust-type', [ 'cust' => $t->c ] ); ?></span>
                </h3>

                <p class="tw-mt-2">
                    <a href="<?= $t->c->getCorpwww() ?>" target="_blank"><?= $t->nakedUrl( $t->c->getCorpwww() ?? '' ) ?></a>

                    <span class="tw-text-gray-600">
                        - joined <?= $c->getDatejoin()->format('Y') ?>

                        <?php if( $c->isResoldCustomer() ): ?>
                            - resold via <?= $c->getReseller()->getName() ?>
                        <?php endif; ?>

                    </span>
                </p>


                <p class="tw-mt-6">

                    <?php if( !$t->c->isTypeAssociate() ): ?>
                        <?php if( $c->getInManrs() ): ?>
                            <a href="https://www.manrs.org/" target="_blank" class="hover:tw-no-underline">
                                <span class="tw-inline-block tw-border tw-border-green-500 tw-rounded-full tw-text-green-500 tw-font-semibold tw-uppercase tw-text-xs tw-px-3 tw-py-1 tw-mr-3">
                                    MANRS
                                </span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if( $c->getTags()->count() ): ?>

                        <?php foreach( $c->getTags() as $tag ): ?>
                            <span class="badge badge-secondary">
                                <?= $tag->getDisplayAs() ?>
                            </span>
                        <?php endforeach; ?>

                        <a class="btn btn-white btn-sm tw-rounded-full tw-text-xs" href="<?= route( 'customer@tags', [ 'id' => $c->getId() ] ) ?>">
                            Edit tags...
                        </a>

                    <?php elseif( count( D2EM::getRepository( Entities\CustomerTag::class )->findAll() ) ): ?>

                        <a class="btn btn-white btn-sm tw-rounded-full tw-border-gray-500 tw-text-gray-500 tw-text-xs" href="<?= route( 'customer@tags', [ 'id' => $c->getId() ] ) ?>">
                            Add tags...
                        </a>


                    <?php endif; ?>

                </p>
            </div>

            <?php if( $t->logoManagementEnabled() && ( $logo = $c->getLogo( Entities\Logo::TYPE_WWW80 ) ) ): ?>

                <div class="col-md-3 col-lg-5 col-12 tw-mt-6 md:tw-mt-0 tw-text-center">
                    <span class="lg:tw-inline-block xl:tw-h-full lg:tw-align-middle"></span>
                    <img class="img-fluid lg:tw-inline-block tw-align-middle" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>">
                </div>

            <?php endif; ?>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li role="overview" class="nav-item">
                    <a class="nav-link <?php if( $t->tab == null || $t->tab == 'overview' ): ?> active <?php endif; ?>" data-toggle="tab" href="#overview">
                        Overview
                    </a>
                </li>
                <li role="details" class="nav-item" >
                    <a class="nav-link <?php if( $t->tab == 'details' ): ?> active <?php endif; ?>" data-toggle="tab" href="#details">
                        Details
                    </a>
                </li>

                <?php if( $t->resellerMode() && $c->isReseller() ): ?>

                    <li role="resold-customers" class="nav-item <?php if( $t->tab == 'resold-customers' ): ?>active<?php endif; ?>">
                        <a class="nav-link " data-toggle="tab" href="#resold-customers" data-toggle="tab">
                            Resold Customers
                        </a>
                    </li>
                <?php endif; ?>
                <?php if( $c->getType() != \Entities\Customer::TYPE_ASSOCIATE && ( ! $c->hasLeft() ) ):?>
                    <li role="ports" class="nav-item ">
                        <a class="nav-link <?php if( $t->tab == 'ports' ): ?> active <?php endif; ?>" data-toggle="tab" href="#ports" data-toggle="tab">
                            Ports
                        </a>
                    </li>

                    <?php if( $c->hasPrivateVLANs() ): ?>
                        <li role="private-vlans" class="nav-item ">
                            <a class="nav-link <?php if( $t->tab == 'private-vlans' ): ?> active <?php endif; ?>" data-toggle="tab" href="#private-vlans" data-toggle="tab">
                                Private VLANs
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li role="users" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab == 'users' ): ?> active <?php endif; ?>" data-toggle="tab" href="#users" data-toggle="tab">
                        Users
                    </a>
                </li>

                <li role="contacts" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab == 'contacts' ): ?> active <?php endif; ?>" data-toggle="tab" href="#contacts" data-toggle="tab">
                        Contacts
                    </a>
                </li>

                <li role="logins" class="nav-item">
                    <a class="nav-link <?php if( $t->tab == 'logins' ): ?> active <?php endif; ?>" data-toggle="tab" href="#logins" data-toggle="tab">
                        Logins
                    </a>
                </li>

                <li role="notes" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab == 'notes' ): ?> active <?php endif; ?>" data-toggle="tab" href="#notes" id="tab-notes" data-toggle="tab">
                        Notes
                        <?php if( $t->notesInfo[ "unreadNotes"] > 0 ): ?>
                            <span id="notes-unread-indicator" class="badge badge-success"><?= $t->notesInfo[ "unreadNotes"] ?></span>
                        <?php endif ?>
                    </a>
                </li>
                <li role="cross-connects" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab == 'cross-connects' ): ?> active <?php endif; ?>" data-toggle="tab" href="#cross-connects" data-toggle="tab">
                        Cross Connects
                    </a>
                </li>

                <?php if( $t->peers ): ?>
                    <li role="peers" class="nav-item">
                        <a class="nav-link <?php if( $t->tab == 'peers' ): ?> active <?php endif; ?>" data-toggle="tab" href="#peers" data-toggle="tab">
                            Peers
                        </a>
                    </li>
                <?php endif; ?>

                <?php if( count( $c->getConsoleServerConnections() ) ): ?>
                    <li role="console-server-connections" class="nav-item ">
                        <a class="nav-link <?php if( $t->tab == 'console-server-connections' ): ?>active<?php endif; ?>" data-toggle="tab" href="#console-server-connections" data-toggle="tab">
                            OOB Access
                        </a>
                    </li>
                <?php endif ?>

                <?php if( $c->getType() != \Entities\Customer::TYPE_ASSOCIATE && ( ! $c->hasLeft() ) ): ?>

                    <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes' ) && $c->isRouteServerClient() ): ?>
                        <li class="nav-item" onclick="window.location.href = '<?= route( "rs-prefixes@view", [ 'id' =>  $c->getId() ] ) ?>'">
                            <a class="nav-link" data-toggle="tab"  href="">
                                RS Prefixes
                                <?php if( $t->rsRoutes[ 'adv_nacc' ][ 'total' ] > 0 ): ?>
                                    <span class="badge badge-danger"><?= $t->rsRoutes[ 'adv_nacc' ][ 'total' ] ?></span>
                                <?php endif ?>
                                &raquo;
                            </a>
                        </li>
                    <?php endif ?>

                    <?php if( !config( 'ixp_fe.frontend.disabled.filtered-prefixes' ) && $c->isRouteServerClient() ): ?>
                        <li class="nav-item" onclick="window.location.href = '<?= route( "filtered-prefixes@list", [ 'customer' =>  $c->getId() ] ) ?>'">
                            <a class="nav-link" data-toggle="tab"  href="">
                                Filtered Prefixes &raquo;
                            </a>
                        </li>
                    <?php endif ?>

                    <?php if( config('grapher.backends.sflow.enabled') ) : ?>
                        <li class="nav-item" onclick="window.location.href = '<?= route( "statistics@p2p", [ 'cid' => $c->getId() ] )  ?>'">
                            <a class="nav-link" data-toggle="tab" href="">P2P &raquo;</a>
                        </li>
                    <?php endif ?>
                <?php endif ?>
            </ul>
        </div>

        <div class="card-body">

            <div class="tab-content">
                <div id="overview" class="tab-pane fade <?php if( $t->tab == null || $t->tab == 'overview' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/overview' ); ?>
                </div>
                <div id="details" class="tab-pane fade <?php if( $t->tab == 'details' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/details' ); ?>
                </div>
                <?php if( $t->resellerMode() && $c->isReseller() ): ?>
                    <div id="resold-customers" class="tab-pane fade">
                        <?= $t->insert( 'customer/overview-tabs/resold-customers' ); ?>
                    </div>
                <?php endif ?>
                <?php if( $c->getType() != \Entities\Customer::TYPE_ASSOCIATE && ( ! $c->hasLeft() ) ):?>
                    <div id="ports" class="tab-pane fade <?php if( $t->tab == 'ports' ): ?> active show <?php endif; ?> ">
                        <?php if( $t->resellerMode() && $c->isReseller() ): ?>
                            <?= $t->insert( 'customer/overview-tabs/reseller-ports' ); ?>
                        <?php else: ?>
                            <?= $t->insert( 'customer/overview-tabs/ports' ); ?>
                        <?php endif ?>
                    </div>
                    <?php if( $c->hasPrivateVLANs() ): ?>
                        <div id="private-vlans" class="tab-pane fade <?php if( $t->tab == 'private-vlans' ): ?> active show <?php endif; ?> ">
                            <?= $t->insert( 'customer/overview-tabs/private-vlans' ); ?>
                        </div>
                    <?php endif ?>
                <?php endif ?>
                <div id="users" class="tab-pane fade <?php if( $t->tab == 'users' ): ?> active show <?php endif; ?> ">
                    <?= $t->insert( 'customer/overview-tabs/users' ); ?>
                </div>
                <div id="contacts" class="tab-pane fade <?php if( $t->tab == 'contacts' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/contacts' ); ?>
                </div>
                <div id="logins" class="tab-pane fade <?php if( $t->tab == 'logins' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/logins' ); ?>
                </div>
                <div id="notes" class="tab-pane fade <?php if( $t->tab == 'notes' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/notes' ); ?>
                </div>
                <div id="cross-connects" class="tab-pane fade">
                    <?= $t->insert( 'customer/overview-tabs/cross-connects' ); ?>
                </div>

                <?php if( $t->peers ): ?>
                    <div id="peers" class="tab-pane fade <?php if( $t->tab == 'peers' ): ?> active show <?php endif; ?>">
                        <?= $t->insert( 'customer/overview-tabs/peers' ); ?>
                    </div>
                <?php endif; ?>

                <div id="console-server-connections" class="tab-pane fade">
                    <?= $t->insert( 'customer/overview-tabs/console-server-connections' ); ?>
                </div>
            </div>
        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

    <?= $t->insert( 'customer/js/overview/users' ); ?>
    <?= $t->insert( 'customer/js/overview/contacts' ); ?>
    <?= $t->insert( 'customer/js/overview/notes' ); ?>

    <?php if( $t->peers ): ?>
        <?= $t->insert( 'customer/js/overview/peers' ); ?>
    <?php endif; ?>

    <script>
        $(document).ready( function() {

            $('.table-responsive-ixp').show();

            $('.table-responsive-ixp').DataTable( {
                responsive: true,
                ordering: false,
                searching: false,
                paging:   false,
                info:   false,
            } );

            $('.table-responsive-ixp-action').show();
            $('.table-responsive-ixp-action').DataTable( {
                responsive: true,
                ordering: false,
                searching: false,
                paging:   false,
                info:   false,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ],
            } );

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust()
                    .responsive.recalc();
            })



        });
    </script>
<?php $this->append() ?>
