<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/sflow-receiver/list' )?>">Sflow Receiver</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>View Sflow Receiver</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route( 'interfaces/sflow-receiver/list' ) ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= route('interfaces/sflow-receiver/edit' , [ 'id' => $t->sflr->getId() ]) ?>" title="edit">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Informations
        </div>
        <div class="panel-body">
            <div class="col-xs-6">
                <table class="table_view_info">
                    <tr>
                        <td>
                            <b>
                                Customer :
                            </b>
                        </td>
                        <td>
                            <a href="<?= url( '/customer/overview/id' ).'/'.$t->sflr->getCustomer()->getId()?>">
                                <?= $t->ee(  $t->sflr->getCustomer()->getName() ) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Virtual Interface :
                            </b>
                        </td>
                        <td>
                            <a href="<?= url( 'vlan/list/id' ).'/'.$t->sflr->getVirtualInterface()->getId()?>">
                                <?= $t->ee( $t->sflr->getVirtualInterface()->getName() ) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Destination IP :
                            </b>
                        </td>
                        <td>
                            <?= $t->sflr->getDstIp() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Destination Port :
                            </b>
                        </td>
                        <td>
                            <?= $t->sflr->getDstPort() ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>