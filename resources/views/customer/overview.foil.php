<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    
    /** @var Entities\Customer $c */
    $c = $t->c;
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li><?= $c->getFormattedName() ?></li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
<li class="pull-right">
    <div class="btn-group btn-group-xs">

        <a class="btn btn-default btn-xs" href="<?= route('statistics@member', [ 'id' => $c->getId() ] ) ?>">Port Graphs</a>

        <div class="btn-group">
            <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-cog"></i> &nbsp;<span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="<?= route( 'interfaces/virtual/add-wizard/custid', [ 'id' => $c->getId() ] ) ?>">Provision new port...</a>
                </li>
                <li role="separator" class="divider"></li>
                <li >
                    <a href="<?= route( 'customer@welcome-email', [ 'id' => $c->getId() ] ) ?>"               >Send Welcome Email...</a>
                </li>
            </ul>
        </div>

        <div class="btn-group">
            <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-pencil"></i> &nbsp;<span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">

                <li>
                    <a href="<?= route( 'customer@edit' , [ 'id' => $c->getId() ] ) ?>">Edit Customer Details</a>
                </li>

                <li>
                    <a href="<?= route( 'customer@billing-registration' , [ 'id' => $c->getId() ] ) ?>" >
                        <?php if( !config('ixp.reseller.no_billing') || !$t->resellerMode() || !$c->isResoldCustomer() ): ?>
                            Edit Billing/Registration Details
                        <?php else: ?>
                            Edit Registration Details
                        <?php endif; ?>
                    </a>
                </li>

                <li class="divider"></li>
                <li>
                    <a href="<?= route( 'customer@tags', [ 'id' => $c->getId() ] ) ?>">Manage Tags...</a>
                </li>

                <?php if( $t->logoManagementEnabled() ): ?>
                    <li class="divider"></li>
                    <li>
                        <a href="<?= route( 'logo@manage', [ 'id' => $c->getId() ] ) ?>">Manage Logo...</a>
                    </li>
                <?php endif; ?>

                <li class="divider"></li>
                <li>
                    <a href="<?= route( 'customer@delete-recap', [ 'id' => $c->getId() ] ) ?>">Delete Customer...</a>
                </li>
            </ul>
        </div>

        <?php $haveprev = 0 ?>
        <?php $havenext = 0 ?>
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

        <a type="button" class="btn btn-default" href="<?= route( "customer@overview", [ 'id' => $cidprev ] ) ?>">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a type="button" class="btn btn-default" href="<?= route( "customer@overview", [ 'id' => $c->getId() ] ) ?>">
            <span class="glyphicon glyphicon glyphicon-refresh"></span>
        </a>
        <a type="button" class="btn btn-default" href="<?= route( "customer@overview", [ 'id' => $cidnext ] ) ?>">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>
</li>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <div class="row">

        <div class="col-sm-12">

            <div class="well">
                <div class="row">
                    <h3 class="col-sm-9">
                        <?= $c->getFormattedName() ?>

                        <?php if( $c->isResoldCustomer() ): ?>
                            <small>
                                <br>&nbsp;&nbsp;Reseller: <?= $c->getReseller()->getName() ?>
                            </small>
                        <?php endif; ?>

                    </h3>

                    <?php if( $t->logoManagementEnabled() && ( $logo = $c->getLogo( Entities\Logo::TYPE_WWW80 ) ) ): ?>

                        <div class="col-sm-3">
                            <img class="www80-padding img-responsive" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                        </div>

                    <?php endif; ?>
                </div>

                <br>
                <div>
                    <?= $t->insert( 'customer/cust-type', [ 'cust' => $t->c ] ); ?>
                    <?php if( $c->getTags()->count() ): ?>
                            <?php foreach( $c->getTags() as $tag ): ?>
                                <span class="label label-default"><?= $tag->getDisplayAs() ?></span>
                            <?php endforeach; ?>
                            <a class="btn btn-xs btn-default" href="<?= route( 'customer@tags', [ 'id' => $c->getId() ] ) ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                    <?php elseif( count( D2EM::getRepository( Entities\CustomerTag::class )->findAll() ) ): ?>
                        <a class="btn btn-xs btn-default" href="<?= route( 'customer@tags', [ 'id' => $c->getId() ] ) ?>"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Add tags...</a>
                    <?php endif; ?>
                </div>

            </div>

            <ul class="nav nav-tabs">
                <li role="overview" <?php if( $t->tab == null || $t->tab == 'overview' ): ?> class="active" <?php endif; ?>>
                    <a data-toggle="tab" href="#overview">Overview</a>
                </li>
                <li role="details" <?php if( $t->tab == 'details' ): ?> class="active" <?php endif; ?> >
                    <a data-toggle="tab" href="#details">Details</a>
                </li>

                <?php if( $t->resellerMode() && $c->isReseller() ): ?>

                    <li role="resold-customers" <?php if( $t->tab == 'resold-customers' ): ?> class="active" <?php endif; ?>>
                        <a data-toggle="tab" href="#resold-customers" data-toggle="tab">Resold Customers</a>
                    </li>
                <?php endif; ?>
                <?php if( $c->getType() != \Entities\Customer::TYPE_ASSOCIATE && ( ! $c->hasLeft() ) ):?>
                    <li role="ports" <?php if( $t->tab == 'ports' ): ?> class="active" <?php endif; ?>>
                        <a data-toggle="tab" href="#ports" data-toggle="tab">Ports</a>
                    </li>

                    <?php if( $c->hasPrivateVLANs() ): ?>
                        <li role="private-vlans" <?php if( $t->tab == 'private-vlans' ): ?> class="active" <?php endif; ?>>
                            <a data-toggle="tab" href="#private-vlans" data-toggle="tab">Private VLANs</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li role="users" <?php if( $t->tab == 'users' ): ?> class="active" <?php endif; ?>>
                    <a data-toggle="tab" href="#users" data-toggle="tab">Users</a>
                </li>

                <li role="contacts" <?php if( $t->tab == 'contacts' ): ?> class="active" <?php endif; ?>>
                    <a data-toggle="tab" href="#contacts" data-toggle="tab">Contacts</a>
                </li>

                <li role="logins" <?php if( $t->tab == 'logins' ): ?> class="active" <?php endif; ?>>
                    <a data-toggle="tab" href="#logins" data-toggle="tab">Logins</a>
                </li>

                <li role="notes" <?php if( $t->tab == 'notes' ): ?> class="active" <?php endif; ?>>
                    <a data-toggle="tab" href="#notes" id="tab-notes" data-toggle="tab">
                        Notes
                        <?php if( $t->notesInfo[ "unreadNotes"] > 0 ): ?>
                            <span id="notes-unread-indicator" class="badge badge-success"><?= $t->notesInfo[ "unreadNotes"] ?></span>
                        <?php endif ?>
                    </a>
                </li>
                <li role="cross-connects" <?php if( $t->tab == 'cross-connects' ): ?> class="active" <?php endif; ?>>
                    <a data-toggle="tab" href="#cross-connects" data-toggle="tab">Cross Connects</a>
                </li>
                <li role="peers" <?php if( $t->tab == 'peers' ): ?> class="active" <?php endif; ?>>
                    <a data-toggle="tab" href="#peers" data-toggle="tab">Peers</a>
                </li>
                <?php if( count( $c->getConsoleServerConnections() ) ): ?>
                    <li role="console-server-connections" <?php if( $t->tab == 'console-server-connections' ): ?> class="active" <?php endif; ?>>
                        <a data-toggle="tab" href="#console-server-connections" data-toggle="tab">OOB Access</a>
                    </li>
                <?php endif ?>

                <?php if( $c->getType() != \Entities\Customer::TYPE_ASSOCIATE && ( ! $c->hasLeft() ) ): ?>

                    <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes' ) && $c->isRouteServerClient() ): ?>
                        <li onclick="window.location.href = '<?= route( "rs-prefixes@view", [ 'id' =>  $c->getId() ] ) ?>'">
                            <a data-toggle="tab"  href="">
                                RS Prefixes
                                <?php if( $t->rsRoutes[ 'adv_nacc' ][ 'total' ] > 0 ): ?>
                                    <span class="badge badge-danger"><?= $t->rsRoutes[ 'adv_nacc' ][ 'total' ] ?></span>
                                <?php endif ?>
                                &raquo;
                            </a>
                        </li>
                    <?php endif ?>

                    <?php if( config('grapher.backends.sflow.enabled') ) : ?>
                        <li onclick="window.location.href = '<?= route( "statistics@p2p", [ 'cid' => $c->getId() ] )  ?>'">
                            <a data-toggle="tab" href="">P2P &raquo;</a>
                        </li>
                    <?php endif ?>
                <?php endif ?>
            </ul>



            <div class="tab-content">
                <div id="overview" class="tab-pane fade <?php if( $t->tab == null || $t->tab == 'overview' ): ?> in active <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/overview' ); ?>
                </div>
                <div id="details" class="tab-pane fade <?php if( $t->tab == 'details' ): ?> in active <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/details' ); ?>
                </div>
                <?php if( $t->resellerMode() && $c->isReseller() ): ?>
                    <div id="resold-customers" class="tab-pane fade">
                        <?= $t->insert( 'customer/overview-tabs/resold-customers' ); ?>
                    </div>
                <?php endif ?>
                <?php if( $c->getType() != \Entities\Customer::TYPE_ASSOCIATE && ( ! $c->hasLeft() ) ):?>
                    <div id="ports" class="tab-pane fade <?php if( $t->tab == 'ports' ): ?> in active <?php endif; ?> ">
                        <?php if( $t->resellerMode() && $c->isReseller() ): ?>
                            <?= $t->insert( 'customer/overview-tabs/reseller-ports' ); ?>
                        <?php else: ?>
                            <?= $t->insert( 'customer/overview-tabs/ports' ); ?>
                        <?php endif ?>


                    </div>
                    <?php if( $c->hasPrivateVLANs() ): ?>
                        <div id="private-vlans" class="tab-pane fade <?php if( $t->tab == 'private-vlans' ): ?> in active <?php endif; ?> ">
                            <?= $t->insert( 'customer/overview-tabs/private-vlans' ); ?>
                        </div>
                    <?php endif ?>
                <?php endif ?>
                <div id="users" class="tab-pane fade <?php if( $t->tab == 'users' ): ?> in active <?php endif; ?> ">
                    <?= $t->insert( 'customer/overview-tabs/users' ); ?>
                </div>
                <div id="contacts" class="tab-pane fade <?php if( $t->tab == 'contacts' ): ?> in active <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/contacts' ); ?>
                </div>
                <div id="logins" class="tab-pane fade <?php if( $t->tab == 'logins' ): ?> in active <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/logins' ); ?>
                </div>
                <div id="notes" class="tab-pane fade <?php if( $t->tab == 'notes' ): ?> in active <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/notes' ); ?>
                </div>
                <div id="cross-connects" class="tab-pane fade">
                    <?= $t->insert( 'customer/overview-tabs/cross-connects' ); ?>
                </div>
                <div id="peers" class="tab-pane fade <?php if( $t->tab == 'peers' ): ?> in active <?php endif; ?>">
                    <?= $t->insert( 'customer/overview-tabs/peers' ); ?>
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
    <?= $t->insert( 'customer/js/overview/notes' ); ?>
    <?= $t->insert( 'customer/js/overview/peers' ); ?>

    <script>
        /**
         * Iframe to display peeringDB website
         *
         * @param string asNumber The AS number
         *
         * @return html
         */
        function perringDb( ) {
            var str = "<?= config('ixp_tools.peeringdb_url' ) ?>";
            var mapObj = {
                '%ID%':"<?= $c->getPeeringDb() ?>",
                '%ASN%':"<?= $c->getAutsys() ?>",

            };
            var re = new RegExp(Object.keys(mapObj).join("|"),"gi");
            str = str.replace(re, function(matched){
                return mapObj[matched];
            });

            let html = `<iframe width="100%" height="500px" src="${str}" frameborder="0" allowfullscreen></iframe>`;

            bootbox.dialog({
                message: html,
                size: "large",
                title: "AS Number Lookup",
                buttons: {
                    cancel: {
                        label: 'Close',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    }
                }
            });
        }
    </script>
<?php $this->append() ?>
