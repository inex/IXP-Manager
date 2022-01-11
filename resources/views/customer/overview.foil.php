<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    /** @var \IXP\Models\Customer $c */
    $c = $t->c;
    $user = Auth::getUser();
    $isSuperUser = $user->isSuperUser();
    $logo = $c->logo;
?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group" aria-label="...">
        <a class="btn btn-white" href="<?= route('statistics@member', [ 'cust' => $c->id ] ) ?>">
            Port Graphs
        </a>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-cog"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= route( 'virtual-interface@create-wizard-for-cust', [ 'cust' => $c->id ] ) ?>">
                    Provision new port...
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route( "irrdb@list", [ "cust" => $c->id, "type" => 'prefix', "protocol" => $c->isIPvXEnabled( 4) ? 4 : 6 ] ) ?>">
                    View / Update IRRDB Entries...
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route( 'customer@welcome-email', [ 'cust' => $c->id ] ) ?>">
                    Send Welcome Email...
                </a>
            </div>
        </div>

        <div class="btn-group btn-group-sm">
            <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-pencil"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= route( 'customer@edit' , [ 'cust' => $c->id ] ) ?>">
                    Edit <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Details
                </a>

                <a class="dropdown-item" href="<?= route( 'customer@billing-registration' , [ 'cust' => $c->id ] ) ?>" >
                    <?php if( !config('ixp.reseller.no_billing') || !$t->resellerMode() || !$c->reseller ): ?>
                        Edit Billing/Registration Details
                    <?php else: ?>
                        Edit Registration Details
                    <?php endif; ?>
                </a>

                <?php if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) ): ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= route( 'docstore-c-dir@list', [ 'cust' => $c->id ] ) ?>">
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Documents...
                    </a>
                <?php endif; ?>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route( 'customer-tag@link-customer', [ 'cust' => $c->id ] ) ?>">
                    Manage Tags...
                </a>

                <?php if( $t->logoManagementEnabled() ): ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= route( 'logo@manage', [ 'id' => $c->id ] ) ?>">
                        Manage Logo...
                    </a>
                <?php endif; ?>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route( 'customer@delete-recap', [ 'cust' => $c->id ] ) ?>">
                  Delete <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>...
                </a>
            </div>
        </div>
        <?php
            $keys = array_keys( $t->customers );

            if( array_search( $c->id, $keys, true ) ===  0 || !array_search( $c->id, $keys, true ) ){
                $cidprev = $c->id;
            } else {
                $cidprev = $keys[ array_search( $c->id, $keys, true ) -1 ];
            }

            if( array_search( $c->id, $keys, true ) ===  count( $keys ) ){
                $cidnext = $c->id;
            } else {
                $cidnext = $keys[ array_search( $c->id, $keys, true ) +1 ] ?? $keys[ array_search( $c->id, $keys, true ) ];
            }
        ?>

        <a class="btn btn-white" href="<?= route( "customer@overview", [ 'cust' => $cidprev ] ) ?>">
            <span class="fa fa-chevron-left"></span>
        </a>

        <a class="btn btn-white" href="<?= route( "customer@overview", [ 'cust' => $c->id ] ) ?>">
            <span class="fa fa-refresh"></span>
        </a>

        <a class="btn btn-white" href="<?= route( "customer@overview", [ 'cust' => $cidnext ] ) ?>">
            <span class="fa fa-chevron-right"></span>
        </a>
        <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\Customer::class, 'logSubject') ): ?>
            <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'Customer' , 'model_id' => $c->id ] ) ?>">
                View logs
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>
    <div class="tw-bg-white shadow-sm tw-p-6">
        <div class="row">
            <div class="<?= $t->logoManagementEnabled() && $logo  ? "col-md-9 col-lg-7" : "col-12" ?>">
                <h3>
                    <?= $t->ee( $c->getFormattedName() ) ?>
                    <span class="tw-text-sm">
                        <?= $t->insert( 'customer/cust-type', [ 'cust' => $c ] ); ?>
                    </span>
                </h3>

                <p class="tw-mt-2">
                    <?php if( $c->corpwww ): ?>
                        <a href="<?= $c->corpwww ?>" target="_blank">
                            <?= $t->nakedUrl( $c->corpwww ) ?>
                        </a>
                        <span class="tw-text-gray-600">
                            -
                        </span>
                    <?php endif; ?>

                    <span class="tw-text-gray-600">
                         joined <?= \Carbon\Carbon::instance( $c->datejoin )->format('Y') ?>
                        <?php if( $r = $c->resellerObject ): ?>
                            - resold via <?= $r->name ?>
                        <?php endif; ?>
                    </span>
                </p>

                <p class="tw-mt-6">
                    <?php if( !$c->typeAssociate() ): ?>
                        <?php if( $c->in_manrs ): ?>
                            <a href="https://www.manrs.org/" target="_blank" class="hover:tw-no-underline">
                                <span class="tw-inline-block tw-border-1 tw-border-green-500 tw-rounded-full tw-text-green-500 tw-font-semibold tw-uppercase tw-text-xs tw-px-3 tw-py-1 tw-mr-3">
                                    MANRS
                                </span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if( $c->tags ): ?>
                        <?php foreach( $c->tags as $tag ): ?>
                            <span class="badge badge-secondary">
                                <?= $tag->display_as ?>
                            </span>
                        <?php endforeach; ?>

                        <a class="btn btn-white btn-sm tw-rounded-full tw-text-xs" href="<?= route( 'customer-tag@link-customer', [ 'cust' => $c->id ] ) ?>">
                            Edit tags...
                        </a>
                    <?php elseif( \IXP\Models\CustomerTag::all()->count() ): ?>
                        <a class="btn btn-white btn-sm tw-rounded-full tw-border-gray-500 tw-text-gray-500 tw-text-xs" href="<?= route( 'customer-tag@link-customer', [ 'cust' => $c->id ] ) ?>">
                            Add tags...
                        </a>
                    <?php endif; ?>
                </p>
            </div>

            <?php if( $t->logoManagementEnabled() && $logo ): ?>
                <div class="col-md-3 col-lg-5 col-12 tw-mt-6 md:tw-mt-0 tw-text-center align-self-center">
                    <img class="img-fluid lg:tw-inline-block tw-align-middle" src="<?= url( 'logos/' . $logo->shardedPath() ) ?>">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link <?php if( !$t->tab || $t->tab === 'overview' ): ?> active <?php endif; ?>" data-toggle="tab" href="#overview">
                        Overview
                    </a>
                </li>
                <li role="details" class="nav-item" >
                    <a class="nav-link <?php if( $t->tab === 'details' ): ?> active <?php endif; ?>" data-toggle="tab" href="#details">
                        Details
                    </a>
                </li>

                <?php if( $t->resellerMode() && $c->isReseller ): ?>
                    <li role="resold-customers" class="nav-item <?php if( $t->tab === 'resold-customers' ): ?>active<?php endif; ?>">
                        <a class="nav-link " data-toggle="tab" href="#resold-customers" data-toggle="tab">
                            Resold <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if( !$c->typeAssociate() && !$c->hasLeft() ):?>
                    <li role="ports" class="nav-item ">
                        <a class="nav-link <?php if( $t->tab === 'ports' ): ?> active <?php endif; ?>" data-toggle="tab" href="#ports" data-toggle="tab">
                            Ports
                        </a>
                    </li>

                    <?php if( $c->hasPrivateVLANs() ): ?>
                        <li role="private-vlans" class="nav-item ">
                            <a class="nav-link <?php if( $t->tab === 'private-vlans' ): ?> active <?php endif; ?>" data-toggle="tab" href="#private-vlans" data-toggle="tab">
                                Private VLANs
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <li role="users" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab === 'users' ): ?> active <?php endif; ?>" data-toggle="tab" href="#users" data-toggle="tab">
                        Users
                    </a>
                </li>

                <li role="contacts" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab === 'contacts' ): ?> active <?php endif; ?>" data-toggle="tab" href="#contacts" data-toggle="tab">
                        Contacts
                    </a>
                </li>

                <li role="logins" class="nav-item">
                    <a class="nav-link <?php if( $t->tab === 'logins' ): ?> active <?php endif; ?>" data-toggle="tab" href="#logins" data-toggle="tab">
                        Logins
                    </a>
                </li>

                <li role="notes" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab === 'notes' ): ?> active <?php endif; ?>" data-toggle="tab" href="#notes" id="tab-notes" data-toggle="tab">
                        Notes
                        <?php if( $t->notesInfo[ "unreadNotes"] > 0 ): ?>
                            <span id="notes-unread-indicator" class="badge badge-success"><?= $t->notesInfo[ "unreadNotes"] ?></span>
                        <?php endif ?>
                    </a>
                </li>

                <li role="cross-connects" class="nav-item ">
                    <a class="nav-link <?php if( $t->tab === 'cross-connects' ): ?> active <?php endif; ?>" data-toggle="tab" href="#cross-connects" data-toggle="tab">
                        Cross Connects
                    </a>
                </li>

                <li role="peers" class="nav-item">
                    <a class="nav-link peers-tab <?php if( $t->tab === 'peers' ): ?> active <?php endif; ?>" data-toggle="tab" href="#peers" data-toggle="tab">
                        Peers
                    </a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.console-server-connection' ) && $c->consoleServerConnections->count() ): ?>
                    <li role="console-server-connections" class="nav-item ">
                        <a class="nav-link <?php if( $t->tab === 'console-server-connections' ): ?>active<?php endif; ?>" data-toggle="tab" href="#console-server-connections" data-toggle="tab">
                            OOB Access
                        </a>
                    </li>
                <?php endif ?>

                <?php if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) ): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= route( 'docstore-c-dir@list', [ 'cust' => $c->id ] ) ?>">
                            Documents &raquo;
                        </a>
                    </li>
                <?php endif; ?>

                <?php if( !$c->typeAssociate() && !$c->hasLeft() ): ?>
                    <?php if( $c->routeServerClient() ): ?>
                        <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes' ) ): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= route( "rs-prefixes@view", [ 'cust' =>  $c->id ] ) ?>">
                                    RS Prefixes &raquo;
                                </a>
                            </li>
                        <?php endif ?>

                        <?php if( !config( 'ixp_fe.frontend.disabled.filtered-prefixes' ) ): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= route( "filtered-prefixes@list", [ 'cust' =>  $c->id ] ) ?>">
                                    Filtered Prefixes &raquo;
                                </a>
                            </li>
                        <?php elseif( $c->irrdbFiltered() ): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= route( "irrdb@list", [ "cust" => $c->id, "type" => 'prefix', "protocol" => $c->isIPvXEnabled( 4) ? 4 : 6 ] ) ?>">
                                    IRRDB Entries &raquo;
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif ?>

                    <?php if( config('grapher.backends.sflow.enabled') ) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= route( "statistics@p2p", [ 'cust' => $c->id ] )  ?>">
                              P2P &raquo;
                            </a>
                        </li>
                    <?php endif ?>
                <?php endif ?>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div id="overview" class="tab-pane fade <?php if( !$t->tab || $t->tab === 'overview' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/overview' ); ?>
                </div>

                <div id="details" class="tab-pane fade <?php if( $t->tab === 'details' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/details' ); ?>
                </div>

                <?php if( $t->resellerMode() && $c->isReseller ): ?>
                    <div id="resold-customers" class="tab-pane fade">
                        <?= $t->insert( 'customer/overview-tabs/resold-customers', [ 'isSuperUser' => $isSuperUser ] ); ?>
                    </div>
                <?php endif ?>

                <?php if( !$c->typeAssociate() && !$c->hasLeft() ):?>
                    <div id="ports" class="tab-pane fade <?php if( $t->tab === 'ports' ): ?> active show <?php endif; ?> ">
                        <?php if( $t->resellerMode() && $c->isReseller ): ?>
                            <?= $t->insert( 'customer/overview-tabs/reseller-ports', [ 'isSuperUser' => $isSuperUser ] ); ?>
                        <?php else: ?>
                            <?= $t->insert( 'customer/overview-tabs/ports', [ 'isSuperUser' => $isSuperUser ] ); ?>
                        <?php endif ?>
                    </div>

                    <?php if( $c->hasPrivateVLANs() ): ?>
                        <div id="private-vlans" class="tab-pane fade <?php if( $t->tab === 'private-vlans' ): ?> active show <?php endif; ?> ">
                            <?= $t->insert( 'customer/overview-tabs/private-vlans' ); ?>
                        </div>
                    <?php endif ?>
                <?php endif ?>

                <div id="users" class="tab-pane fade <?php if( $t->tab === 'users' ): ?> active show <?php endif; ?> ">
                    <?= $t->insert( 'customer/overview-tabs/users', [ 'isSuperUser' => $isSuperUser ] ); ?>
                </div>

                <div id="contacts" class="tab-pane fade <?php if( $t->tab === 'contacts' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/contacts' ); ?>
                </div>

                <div id="logins" class="tab-pane fade <?php if( $t->tab === 'logins' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/logins' ); ?>
                </div>

                <div id="notes" class="tab-pane fade <?php if( $t->tab === 'notes' ): ?> active show <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/notes', [ 'isSuperUser' => $isSuperUser, 'user' => $user ] ); ?>
                </div>

                <div id="cross-connects" class="tab-pane fade">
                    <?= $t->insert( 'customer/overview-tabs/cross-connects', [ 'isSuperUser' => $isSuperUser ] ); ?>
                </div>

                <div id="peers" class="tab-pane peers-tab fade <?php if( $t->tab === 'peers' ): ?> active show <?php endif; ?>">
                    <p class="tw-text-center">
                        <br/>
                        <b>Data loading please wait...</b>
                    </p>
                </div>

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
    <?= $t->insert( 'customer/js/overview/notes', [ 'isSuperUser' => $isSuperUser ] ); ?>

    <script>
        $(document).ready( function() {
            $('.table-responsive-ixp').dataTable( {
                responsive: true,
                ordering: false,
                searching: false,
                paging:   false,
                info:   false,
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
            } ).show();
            
            $('.table-responsive-ixp-action').dataTable( {
                responsive: true,
                ordering: false,
                searching: false,
                paging:   false,
                info:   false,
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ],
            } ).show();

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust()
                    .responsive.recalc();
            })

            // Loads peers with ajax
            let url = "<?= route( 'customer@load-peers', [ 'cust' => $c->id ] ) ?>";

            $.ajax( url , {
                type: 'get'
            })
            .done( function( data ) {
                if( data.success ){
                    // load th frag in the view
                    $('#peers').html( data.htmlFrag );
                }
            })
            .fail( function() {
                alert( "Error running ajax query for " + url );
                throw new Error( "Error running ajax query for " + url );
            })
        });
    </script>
<?php $this->append() ?>