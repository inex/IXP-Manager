<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filter / <?= $t->ee( $t->rsf->getId() ) ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route ('rs-filter@list', [ "custid" => $t->rsf->getCustomer()->getId() ] ) ?>" title="list">
            <span class="fa fa-list"></span>
        </a>

        <?php if( !Auth::getUser()->isCustUser() ): ?>
            <a class="btn btn-white" href="<?= route ('rs-filter@add', [ "custid" => $t->rsf->getCustomer()->getId() ] ) ?>" title="add">
                <span class="fa fa-plus"></span>
            </a>
            <a class="btn btn-white" href="<?= route ('rs-filter@edit' , [ 'id' => $t->rsf->getId() ] ) ?>" title="edit">
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
                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                        <a href="<?= route( "customer@overview" , [ "id" => $t->rsf->getPeer()->getId() ] )?> ">
                                            <?= $t->ee( $t->rsf->getPeer()->getName() )?>
                                        </a>
                                    <?php else: ?>
                                        <?= $t->ee( $t->rsf->getPeer()->getName() )?>
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
                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                        <a href="<?= route( "customer@overview" , [ "id" => $t->rsf->getCustomer()->getId() ] )?> ">
                                            <?= $t->ee( $t->rsf->getCustomer()->getName() )?>
                                        </a>
                                    <?php else: ?>
                                        <?= $t->ee( $t->rsf->getCustomer()->getName() )?>
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
                                    <?php if( $t->rsf->getVlan() ): ?>
                                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                                            <a href="<?= route( "vlan@view" , [ "id" => $t->rsf->getCustomer()->getId() ] )?> ">
                                                <?= $t->rsf->getVlan()->getName() ?>
                                            </a>
                                        <?php else: ?>
                                            <?= $t->rsf->getVlan()->getName() ?>
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
                                    <?= $t->ee( $t->rsf->getPrefix() ) ?>
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
                                    <?= $t->rsf->isEnabled() ? "Yes" : "No" ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Order By:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->rsf->getOrderBy() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Live:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->rsf->getLive() ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

<?php $this->append() ?>