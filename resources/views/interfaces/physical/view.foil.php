<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action( 'Interfaces\PhysicalInterfaceController@list' )?>">Physical Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>View Physical Interface</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= action( 'Interfaces\PhysicalInterfaceController@list' )?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= route('interfaces/physical/edit' , [ 'id' => $t->listPi[0]['id'] ]) ?>" title="edit">
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
                            <?= $t->ee( $t->listPi[0]['customer'] )   ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Location :
                            </b>
                        </td>
                        <td>
                            <a href="<?= url( '/vlan/view/id/' ) ?> ">
                                <?= $t->ee(  $t->listPi[0]['location'] )?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Switch :
                            </b>
                        </td>
                        <td>
                            <?= $t->ee(  $t->listPi[0]['switch'] ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Port :
                            </b>
                        </td>
                        <td>
                            <?= $t->ee(  $t->listPi[0]['port'] ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Status :
                            </b>
                        </td>
                        <td>
                            <?= isset( Entities\PhysicalInterface::$STATES[ $t->listPi[0]['status'] ] ) ? Entities\PhysicalInterface::$STATES[ $t->listPi[0]['status'] ] : 'Unknown'  ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Speed :
                            </b>
                        </td>
                        <td>
                            <?= $t->listPi[0]['speed'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Duplex :
                            </b>
                        </td>
                        <td>
                            <?= $t->listPi[0]['duplex'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Auto-Neg :
                            </b>
                        </td>
                        <td>
                            <?= $t->listPi[0]['autoneg'] ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Monitor Index :
                            </b>
                        </td>
                        <td>
                            <?= $t->listPi[0]['monitorindex'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Notes :
                            </b>
                        </td>
                        <td>
                            <?= $t->ee(  $t->listPi[0]['notes'] ) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>