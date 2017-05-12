<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'vlanInterface/list' )?>">Vlan Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>View Vlan Interface</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= url('vlanInterface/list') ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= url('vlanInterface/edit').'/'.$t->listVli[0]['id'] ?>" title="edit">
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
                            <a href="<?= url( '/customer/overview/id' ).'/'.$t->listVli[0]['custid']?>">
                                <?= $t->listVli[0]['customer']   ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                VLAN Name :
                            </b>
                        </td>
                        <td>
                            <a href="<?= url( 'vlan/list/id' ).'/'.$t->listVli[0]['vlanid']?>">
                                <?= $t->listVli[0]['vlan'] ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                IPv4 :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['ipv4enabled'] ? '<span class="label label-success">Enable</span>' : '<span class="label label-danger">Disable</span>' ?>
                            <?= $t->listVli[0]['ipv4'] ?>
                            <br/>
                            <?= $t->listVli[0]['ipv4hostname'] ?>
                            <br/>
                            <?= $t->listVli[0]['ipv4canping'] ? '<span class="label label-success">Canping</span>' : '' ?>
                            <br/>
                            <?= $t->listVli[0]['ipv4monitorrcbgp'] ? '<span class="label label-success">Monitor rcbgp</span>' : '' ?>
                            <br/>
                            <br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Secret key IPv4:
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['ipv4bgpmd5secret'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Monitoring Enabled via IPv4 ICMP :
                            </b>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Route Server Client :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['rsclient'] ? '<i class="glyphicon glyphicon-ok"></i>' : '<i class="glyphicon glyphicon-remove"></i>'   ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Notes :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['notes'] ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-6">
                <table class="table_view_info">
                    <tr>
                        <td>
                            <b>
                                Multicast :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['mcastenabled'] ? '<span class="label label-success">Enable</span>' : '<span class="label label-danger">Disable</span>' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Max BGP Prefixes :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['maxbgpprefix'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                IPv6 :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['ipv6enabled'] ? '<span class="label label-success">Enable</span>' : '<span class="label label-danger">Disable</span>' ?>
                            <?= $t->listVli[0]['ipv6'] ?>
                            <br/>
                            <?= $t->listVli[0]['ipv6hostname'] ?>
                            <br/>
                            <?= $t->listVli[0]['ipv6canping'] ? '<span class="label label-success">Canping</span>' : '' ?>
                            <br/>
                            <?= $t->listVli[0]['ipv6monitorrcbgp'] ? '<span class="label label-success">Monitor rcbgp</span>' : '' ?>
                            <br/>
                            <br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Secret key IPv6 :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['ipv6bgpmd5secret'] ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>
                                Monitoring Enabled via IPv6 ICMP :
                            </b>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Busy Host?
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['busyhost'] ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                AS112 Client :
                            </b>
                        </td>
                        <td>
                            <?= $t->listVli[0]['as112client'] ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function() {


        });
    </script>
<?php $this->append() ?>