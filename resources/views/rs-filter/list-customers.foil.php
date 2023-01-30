<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filtering :: Customers with Filters
<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>

            <?php if( $t->customers->count() ): ?>
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
                        <?php foreach( $t->customers as $c ):
                            /** @var $c \IXP\Models\Customer */?>
                            <tr>
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
                                No customer has route server filters configured.
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