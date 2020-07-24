<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filtering for <?= $t->c->name ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( !Auth::getUser()->isCustUser() ): ?>
        <div class="btn-group btn-group-sm" role="group">
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

            <?php if( Auth::getUser()->isCustUser() ): ?>
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

            <?php if( $t->rsFilters->count() ): ?>
                <table id='table-list' class="table table-striped table-responsive-ixp-with-header" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Peer
                            </th>
                            <th>
                                Lan
                            </th>
                            <th>
                                Protocol
                            </th>
                            <th>
                                Advertised Prefix
                            </th>
                            <th>
                                Received Prefix
                            </th>
                            <th>
                                Advertise Action
                            </th>
                            <th>
                                Receive Action
                            </th>
                            <th>
                                Enabled
                            </th>
                            <th>
                                Order By
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    <thead>
                    <tbody>
                        <?php foreach( $t->rsFilters as $index => $rsf ):?>
                            <tr>
                                <td>
                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                        <?php if( $rsf->peer ): ?>
                                            <a href="<?= route( 'customer@overview', [ 'id' => $rsf->peer->id ] ) ?>">
                                                <?= $t->ee( $rsf->peer->name ) ?>
                                            </a>
                                        <?php else: ?>
                                            All Peers
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if( $rsf->peer ): ?>
                                            <?= $t->ee( $rsf->peer->name ) ?>
                                        <?php else: ?>
                                            All Peers
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if( $rsf->vlan ): ?>
                                        <?= $t->ee( $rsf->vlan->name ) ?>
                                    <?php else: ?>
                                        All LAN's
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $rsf->resolveProtocol() ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->received_prefix ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->advertised_prefix ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->resolveActionAdvertise() ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->resolveActionReceive() ) ?>
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

                                        <?php if( !Auth::getUser()->isCustUser() ): ?>
                                            <a class="btn btn-white" href="<?= route( 'rs-filter@edit' , [ 'rsf' =>  $rsf->id ] ) ?>" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-white" href="<?= route( "rs-filter@toggle-enable", [ "rsf" => $rsf->id, "enable" => $rsf->enabled ? 0 : 1 ] ) ?>" title="<?= $rsf->enable ? "Disable" : "Enable" ?>">
                                                <i class="fa <?= $rsf->enabled ? "fa-times-circle" : "fa-check-circle" ?>"></i>
                                            </a>
                                            <a class="btn btn-white delete-rsf" id="delete-rsf-<?= $rsf->id ?>" href="#" data-object-id="<?=  $rsf->id ?>" data-url="<?= route( 'rs-filter@delete' , [ 'rsf' => $rsf->id ]  )  ?>" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <h6 class="dropdown-header">
                                                    Sorting Actions
                                                </h6>
                                                <a class="dropdown-item <?= $t->rsFilters->first()->id !== $rsf->id ?: "disabled" ?>" href="<?= route( "rs-filter@change-order", [ "rsf" => $rsf->id, "up" => 1 ] ) ?>">
                                                    Move Up
                                                </a>
                                                <a class="dropdown-item <?= $t->rsFilters->last()->id !== $rsf->id ?: "disabled"  ?>" href="<?= route( "rs-filter@change-order", [ "rsf" => $rsf->id , "up" => 0 ] ) ?>">
                                                    Move Down
                                                </a>
                                            </div>
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
                                No route server filter found for this <?= config( "ixp_fe.lang.customer.one" ) ?>.
                                <a class="btn btn-white" href="<?= route( "rs-filter@create", [ "cust" => $t->c->id ] ) ?>">Create One</a>
                            </b>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'rs-filter/js/list' ); ?>
<?php $this->append() ?>