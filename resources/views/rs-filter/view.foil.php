<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filter / <?= $t->ee( $t->rsf->id ) ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route ('rs-filter@list', [ "cust" => $t->rsf->customer->id ] ) ?>" title="list">
            <span class="fa fa-list"></span>
        </a>

        <?php if( !Auth::user()->custUser() ): ?>
            <a class="btn btn-white" href="<?= route ('rs-filter@create', [ "cust" => $t->rsf->customer->id ] ) ?>" title="create">
                <span class="fa fa-plus"></span>
            </a>
            <a class="btn btn-white" href="<?= route ('rs-filter@edit' , [ 'rsf' => $t->rsf->id ] ) ?>" title="edit">
                <span class="fa fa-pencil"></span>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    Details for Route Server Filter
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
                                    <?php if( Auth::user()->superUser() ): ?>
                                        <?php if( $t->rsf->peer ): ?>
                                            <a href="<?= route( "customer@overview" , [ "id" => $t->rsf->peer->id ] )?> ">
                                                <?= $t->ee( $t->rsf->peer->name )?>
                                            </a>
                                        <?php else: ?>
                                            All Peers
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if( $t->rsf->peer ): ?>
                                            <?= $t->ee( $t->rsf->peer->name )?>
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
                                    <?php if( Auth::user()->superUser() ): ?>
                                        <a href="<?= route( "customer@overview" , [ "id" => $t->rsf->customer->id ] )?> ">
                                            <?= $t->ee( $t->rsf->customer->name )?>
                                        </a>
                                    <?php else: ?>
                                        <?= $t->ee( $t->rsf->customer->name )?>
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
                                        <?php if( Auth::user()->superUser() ): ?>
                                            <a href="<?= route( "vlan@view" , [ "id" => $t->rsf->vlan->id ] )?> ">
                                                <?= $t->rsf->vlan->name ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $t->rsf->vlan->name ?>
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
                                    <?= $t->rsf->resolveProtocol()?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Prefix:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->rsf->prefix ) ?>
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
                                    <?= $t->rsf->resolveActionAdvertise() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Action Receive:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->rsf->resolveActionReceive() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Enable:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->rsf->enabled ? "Yes" : "No" ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Order By:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->rsf->order_by ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Live:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->rsf->live ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>