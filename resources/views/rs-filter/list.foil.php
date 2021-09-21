<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $isSuperUser = Auth::getUser()->isSuperUser();
    $isCustUser = Auth::getUser()->isCustUser();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filtering <?= $isSuperUser ? ' for ' . $t->c->name : '' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( !$isCustUser ): ?>
        <div class="btn-group btn-group-sm" role="group" xmlns="http://www.w3.org/1999/html">
            <a class="btn btn-white" href="<?= route('rs-filter@create', [ "cust" => $t->c->id ] ) ?>">
                <span class="fa fa-plus"></span>
            </a>
        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>

            <?php if( $isCustUser ): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12 d-flex">
                            <b class="mr-auto my-auto">
                                If you want to grant your privileges click on the following link: <a href="<?= route( "rs-filter@grant-cust-user" ) ?>">here</a>
                            </b>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


            <?php if( $t->in_sync ): ?>
                <div class="alert alert-success mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa <?= $t->rsFilters->count() ? 'fa-check' : 'fa-info-circle' ?> fa-2x"></i>
                        </div>
                        <div class="col-sm-12 d-flex">
                            <b class="mr-auto my-auto">
                                <?= $t->rsFilters->count() ? 'Your filters are in sync with our production configuration.' : 'You have no filters in production.' ?>
                            </b>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-exclamation fa-2x"></i>
                        </div>
                        <div class="col-sm-12 d-flex">
                            <b class="mr-auto my-auto">
                                Your filters are not in sync with our production configuration. You can continue editing or:
                            </b>
                            <div class="pull-right d-flex">
                                <form id="form-revert" action="<?= route( 'rs-filter@revert', [ 'cust' => $t->c->id ] ) ?>" method="post">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>" />
                                    <button type="submit" class="btn btn-warning mr-4" id="submit-revert"  href="  title="Revert Changes">
                                        Revert
                                    </button>
                                </form>
                                <form id="form-commit" action="<?= route( 'rs-filter@commit', [ 'cust' => $t->c->id ] ) ?>" method="post">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>" />
                                    <button type="submit" class="btn btn-warning mr-4" id="submit-commit"  title="Commit Changes to Production">
                                        Commit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if( !$t->in_sync ): ?>
                <h3 class="my-4">Staged Rules (Deploy via Commit above)</h3>
            <?php endif; ?>

            <?php if( $t->rsFilters->count() ): ?>
                <table id='table-list' class="table table-striped table-responsive-ixp-with-header" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Peer
                            </th>
                            <th>
                                LAN
                            </th>
                            <th>
                                Protocol
                            </th>
                            <th>
                                Advertised Prefix
                            </th>
                            <th>
                                Advertise Action
                            </th>
                            <th>
                                Received Prefix
                            </th>
                            <th>
                                Receive Action
                            </th>
                            <th>
                                Enabled
                            </th>
                            <th>
                                Order
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    <thead>
                    <tbody>
                        <?php foreach( $t->rsFilters as $index => $rsf ):
                            /** @var $rsf \IXP\Models\RouteServerFilter */?>
                            <tr class="<?= $rsf->enabled ?: 'tw-italic tw-line-through' ?>">
                                <td>
                                    <?php if( $isSuperUser ): ?>
                                        <?php if( $rsf->peer ): ?>
                                            <a href="<?= route( 'customer@overview', [ 'cust' => $rsf->peer->id ] ) ?>">
                                                <?= $t->ee( $rsf->peer->name ) ?>
                                            </a>
                                        <?php else: ?>
                                            All
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if( $rsf->peer ): ?>
                                            <?= $t->ee( $rsf->peer->name ) ?>
                                        <?php else: ?>
                                            All
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if( $rsf->vlan ): ?>
                                        <?= $t->ee( $rsf->vlan->name ) ?>
                                    <?php else: ?>
                                        All
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $rsf->protocol() ?>
                                </td>
                                <td>
                                    <?php if( $t->ee( $rsf->advertised_prefix ) ): ?>
                                        <?= $t->ee( $rsf->advertised_prefix ) ?>
                                    <?php else: ?>
                                        *
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->actionAdvertise() ) ?>
                                </td>
                                <td>
                                    <?php if( $t->ee( $rsf->received_prefix ) ): ?>
                                        <?= $t->ee( $rsf->received_prefix ) ?>
                                    <?php else: ?>
                                        *
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->actionReceive() ) ?>
                                </td>
                                <td>
                                    <?= $rsf->enabled ? "Yes" : "No" ?>
                                </td>
                                <td>
                                    <?= $rsf->order_by ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-white" href="<?= route( 'rs-filter@view' , [ 'rsf' =>  $rsf->id ] ) ?>" title="Preview">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        <?php if( !$isCustUser ): ?>
                                            <a class="btn btn-white" href="<?= route( 'rs-filter@edit' , [ 'rsf' =>  $rsf->id ] ) ?>" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-white" href="<?= route( "rs-filter@toggle-enable", [ "rsf" => $rsf->id, "enable" => $rsf->enabled ? 0 : 1 ] ) ?>" title="<?= $rsf->enabled ? "Disable" : "Enable" ?>">
                                                <i class="fa <?= $rsf->enabled ? "fa-times-circle" : "fa-check-circle" ?>"></i>
                                            </a>
                                            <a class="btn btn-white delete-rsf" id="delete-rsf-<?= $rsf->id ?>" data-object-id="<?=  $rsf->id ?>"  href="<?= route( 'rs-filter@delete' , [ 'rsf' => $rsf->id ]  )  ?>" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <a class="btn btn-white <?= $t->rsFilters->first()->id !== $rsf->id ?: "disabled" ?>" href="<?= route( "rs-filter@change-order", [ "rsf" => $rsf->id, "up" => 1 ] ) ?>">
                                                <i class="fa fa-arrow-up"></i>
                                            </a>
                                            <a class="btn btn-white <?= $t->rsFilters->last()->id !== $rsf->id ?: "disabled"  ?>" href="<?= route( "rs-filter@change-order", [ "rsf" => $rsf->id , "up" => 0 ] ) ?>">
                                                <i class="fa fa-arrow-down"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12 d-flex">
                            <b class="mr-auto my-auto">
                                <?= $t->in_sync ? 'No route server filters have been defined.' : 'Commit now to remove all filters from production.' ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <a class="btn btn-sm btn-white" href="<?= route( "rs-filter@create", [ "cust" => $t->c->id ] ) ?>">
                                    Create Route Filter
                                </a>
                            </b>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


            <?php if( !$t->in_sync ): ?>
                <h3 class="my-4">Rules in Production</h3>

                <?php if( $t->rsFiltersProd->count() ): ?>
                    <table id='table-list' class="table table-striped table-responsive-ixp-with-header" width="100%">
                        <thead class="thead-dark">
                        <tr>
                            <th>
                                Peer
                            </th>
                            <th>
                                LAN
                            </th>
                            <th>
                                Protocol
                            </th>
                            <th>
                                Advertised Prefix
                            </th>
                            <th>
                                Advertise Action
                            </th>
                            <th>
                                Received Prefix
                            </th>
                            <th>
                                Receive Action
                            </th>
                            <th>
                                Enabled
                            </th>
                            <th>
                                Order
                            </th>
                            <th></th>
                        </tr>
                        <thead>
                        <tbody>
                        <?php foreach( $t->rsFiltersProd as $index => $rsf ):
                            /** @var $rsf \IXP\Models\RouteServerFilter */?>
                            <tr class="<?= $rsf->enabled ?: 'tw-italic' ?>">
                                <td>
                                    <?php if( $isSuperUser ): ?>
                                        <?php if( $rsf->peer ): ?>
                                            <a href="<?= route( 'customer@overview', [ 'cust' => $rsf->peer->id ] ) ?>">
                                                <?= $t->ee( $rsf->peer->name ) ?>
                                            </a>
                                        <?php else: ?>
                                            All
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if( $rsf->peer ): ?>
                                            <?= $t->ee( $rsf->peer->name ) ?>
                                        <?php else: ?>
                                            All
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if( $rsf->vlan ): ?>
                                        <?= $t->ee( $rsf->vlan->name ) ?>
                                    <?php else: ?>
                                        All
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $rsf->protocol() ?>
                                </td>
                                <td>
                                    <?php if( $t->ee( $rsf->advertised_prefix ) ): ?>
                                        <?= $t->ee( $rsf->advertised_prefix ) ?>
                                    <?php else: ?>
                                        *
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->actionAdvertise() ) ?>
                                </td>
                                <td>
                                    <?php if( $t->ee( $rsf->received_prefix ) ): ?>
                                        <?= $t->ee( $rsf->received_prefix ) ?>
                                    <?php else: ?>
                                        *
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->actionReceive() ) ?>
                                </td>
                                <td>
                                    <?= $rsf->enabled ? "Yes" : "No" ?>
                                </td>
                                <td>
                                    <?= $rsf->order_by ?>
                                </td>
                                <td>
                                    <em><?= $rsf->enabled ? '' : "(disabled)" ?></em>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        <tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-success mt-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fa fa-check fa-2x"></i>
                            </div>
                            <div class="col-sm-12 d-flex">
                                <b class="mr-auto my-auto">
                                    There are no filters in production.
                                </b>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>




        </div>
    </div>




    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="mr-4 text-center">
                <i class="fa fa-question-circle fa-2x"></i>
            </div>
            <div>
                <h3>
                  Route Server Filtering
                </h3>
                <p>
                  <b>IXP Manager</b> supports the industry standards for community based route server filtering. You can find
                  the <a href="https://docs.ixpmanager.org/features/route-servers/#well-known-filtering-communities">official
                  documentation here</a>. Using the BGP-community mechanism can be difficult to implement where a network
                  engineer is not familiar with BGP communities or where a network may have arduous change control processes
                  for altering a router's configuration.
                </p>
                <p>
                  This purpose of this tool is to allow IXP participants to implement the exact same mechanism but rather than
                  tagging your routes on egress from your router / manipulating routes on ingress to your router, the IXP's
                  route servers perform the equivalent tagging / route manipulation as they accept your routes or send you
                  routes from other networks.
                </p>
                <p>
                  Please note the following important points:
                </p>
                <ol>
                  <li>
                      This tool is intended to help you make relatively simple routing policies.
                  </li>
                  <li>
                      When processing routes, <b>the first matching rule wins</b>. Please consider the ordering of your rules
                      and ensure to put more specific rules first.
                  </li>
                  <li>
                      You are responsible for your own routing policy and ensuring any rules you set here have the desired effect.
                      If in doubt, feel free to contact our operations team.
                  </li>
                </ol>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'rs-filter/js/list' ); ?>
<?php $this->append() ?>