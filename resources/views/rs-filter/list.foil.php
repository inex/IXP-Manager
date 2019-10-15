<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filtering for <?= $t->c->getName() ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( !Auth::getUser()->isCustUser() ): ?>
        <div class="btn-group btn-group-sm" role="group">
            <a class="btn btn-white" href="<?= route('rs-filter@add', [ "custid" => $t->c->getId() ] ) ?>">
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

            <?php if( count( $t->rsFilters ) ): ?>
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
                                Prefix
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
                        <?php foreach( $t->rsFilters as $index => $rsf ):
                            /** @var Entities\RouteServerFilter $rsf */
                            ?>
                            <tr>
                                <td>
                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                        <a href="<?= route( 'customer@overview', [ 'id' => $rsf->getPeer()->getId() ] ) ?>">
                                            <?= $t->ee( $rsf->getPeer()->getName() ) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= $t->ee( $rsf->getPeer()->getName() ) ?>
                                    <?php endif; ?>

                                </td>
                                <td>
                                    <?php if($rsf->getVlan() ): ?>
                                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                                            <a href="<?= route( 'vlan@view', [ 'id' => $rsf->getVlan()->getId() ] ) ?>">
                                                <?= $t->ee( $rsf->getVlan()->getName() ) ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $t->ee( $rsf->getVlan()->getName() ) ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        All LAN's
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $rsf->resolveProtocol() ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->getPrefix() ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->resolveActionAdvertise() ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->resolveActionReceive() ) ?>
                                </td>
                                <td>
                                    <?= $rsf->isEnabled() ? "Yes" : "No" ?>
                                </td>
                                <td>
                                    <?= $rsf->getOrderBy() ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-white" href="<?= route( 'rs-filter@view' , [ 'id' =>  $rsf->getId() ] ) ?>" title="Preview">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        <?php if( !Auth::getUser()->isCustUser() ): ?>
                                            <a class="btn btn-white" href="<?= route( 'rs-filter@edit' , [ 'id' =>  $rsf->getId() ] ) ?>" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-white" href="<?= route( "rs-filter@toggle-enable", [ "id" => $rsf->getId(), "enable" => $rsf->isEnabled() ? 0 : 1 ] ) ?>" title="<?= $rsf->isEnabled() ? "Disable" : "Enable" ?>">
                                                <i class="fa <?= $rsf->isEnabled() ? "fa-times-circle" : "fa-check-circle" ?>"></i>
                                            </a>


                                            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                            <div class="dropdown-menu dropdown-menu-right">

                                                <h6 class="dropdown-header">
                                                    Sorting Actions
                                                </h6>

                                                <a class="dropdown-item <?= array_key_first( $t->rsFilters ) == $index ? "disabled" : "" ?>" href="<?= route( "rs-filter@change-order", [ "id" => $rsf->getId(), "up" => 1 ] ) ?>">
                                                    Move Up
                                                </a>

                                                <a class="dropdown-item <?= array_key_last( $t->rsFilters ) == $index ? "disabled" : "" ?>" href="<?= route( "rs-filter@change-order", [ "id" => $rsf->getId() , "up" => 0 ] ) ?>">
                                                    Move Down
                                                </a>

                                            </div>
                                            <a class="btn btn-white" id="delete-rsf-<?=$rsf->getId() ?>" href="" title="Delete">
                                                <i class="fa fa-trash"></i>
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
                                No route server filter found for this <?= config( "ixp_fe.lang.customer.one" ) ?>.
                                <a class="btn btn-white" href="<?= route( "rs-filter@add", [ "custid" => $t->c->getId() ] ) ?>">Add One</a>
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