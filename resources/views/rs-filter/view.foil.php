<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $rsf = $t->rsf; /** @var $rsf \IXP\Models\RouteServerFilter */
    $isSuperUser = Auth::getUser()->isSuperUser();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filter / <?= $t->ee( $rsf->id ) ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route ('rs-filter@list', [ "cust" => $rsf->customer_id ] ) ?>" title="list">
            <span class="fa fa-list"></span>
        </a>

        <?php if( !Auth::getUser()->isCustUser() ): ?>
            <a class="btn btn-white" href="<?= route ('rs-filter@create', [ "cust" => $rsf->customer_id ] ) ?>" title="create">
                <span class="fa fa-plus"></span>
            </a>
            <a class="btn btn-white" href="<?= route ('rs-filter@edit' , [ 'rsf' => $rsf->id ] ) ?>" title="edit">
                <span class="fa fa-pencil"></span>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header tw-flex">
                    <div class="mr-auto">
                        Details
                    </div>
                    <?php if( $isSuperUser && !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\RouteServerFilter::class, 'logSubject') ): ?>
                        <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'RouteServerFilter' , 'model_id' => $rsf->id ] ) ?>">
                            View logs
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body row">
                    <div class="col-lg-6 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>
                                        Peer:
                                    </b>
                                </td>
                                <td>
                                    <?php if( $isSuperUser ): ?>
                                        <?php if( $rsf->peer ): ?>
                                            <a href="<?= route( "customer@overview" , [ 'cust' => $rsf->peer_id ] )?> ">
                                                <?= $t->ee( $rsf->peer->name )?>
                                            </a>
                                        <?php else: ?>
                                            All Peers
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if( $rsf->peer ): ?>
                                            <?= $t->ee( $rsf->peer->name )?>
                                        <?php else: ?>
                                            All Peers
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Customer:
                                    </b>
                                </td>
                                <td>
                                    <?php if( $isSuperUser ): ?>
                                        <a href="<?= route( "customer@overview" , [ 'cust' => $rsf->customer_id ] )?> ">
                                            <?= $t->ee( $rsf->customer->name )?>
                                        </a>
                                    <?php else: ?>
                                        <?= $t->ee( $rsf->customer->name )?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Vlan:
                                    </b>
                                </td>
                                <td>
                                    <?php if( $t->rsf->vlan ): ?>
                                        <?php if( $isSuperUser ): ?>
                                            <a href="<?= route( "vlan@view" , [ "id" => $rsf->vlan_id ] )?> ">
                                                <?= $rsf->vlan->name ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $rsf->vlan->name ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        All LAN's
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Protocol:
                                    </b>
                                </td>
                                <td>
                                    <?= $rsf->protocol()?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Received Prefix:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->received_prefix ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Advertised Prefix:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->advertised_prefix ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Created:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->created_at ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Updated:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $rsf->updated_at ) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>
                                        Action Advertise:
                                    </b>
                                </td>
                                <td>
                                    <?= $rsf->actionAdvertise() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Action Receive:
                                    </b>
                                </td>
                                <td>
                                    <?= $rsf->actionReceive() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Enable:
                                    </b>
                                </td>
                                <td>
                                    <?= $rsf->enabled ? "Yes" : "No" ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Order By:
                                    </b>
                                </td>
                                <td>
                                    <?= $rsf->order_by ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Live:
                                    </b>
                                </td>
                                <td>
                                    <?= $rsf->live ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>