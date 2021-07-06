<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $vli = $t->vli; /** @var $vli \IXP\Models\VlanInterface */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Vlan Interfaces / View
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'vlan-interface@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <a class="btn btn-white" href="<?= route('vlan-interface@edit' , [ 'vli' => $vli->id ] ) ?>" title="edit">
            <span class="fa fa-pencil"></span>
        </a>
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
                    <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\VlanInterface::class, 'logSubject') ): ?>
                        <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'VlanInterface' , 'model_id' => $vli->id ] ) ?>">
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
                                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>:
                                    </b>
                                </td>
                                <td>
                                    <a href="<?= route( "customer@overview" , [ 'cust' => $vli->virtualInterface->custid ] ) ?>">
                                        <?= $t->ee( $vli->virtualInterface->customer->name )   ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        VLAN Name:
                                    </b>
                                </td>
                                <td>
                                    <a href="<?= route( 'vlan@view', [ "id" => $vli->vlanid ] ) ?>">
                                        <?= $t->ee( $vli->vlan->name ) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        IPv4:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->ipv4enabled ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Disabled</span>' ?>
                                    <?php if( $vli->ipv4enabled ): ?>
                                        <br><br>
                                        <?= $t->ee( $vli->ipv4address->address ) ?>
                                        <br>
                                        <?= $t->ee( $vli->ipv4hostname ) ?>
                                        <br>
                                        <?= $vli->ipv4canping ? '<span class="badge badge-success">Can Ping</span>' : '' ?>
                                        &nbsp;
                                        <?= $vli->ipv4monitorrcbgp ? '<span class="badge badge-success">Monitor RC BGP</span>' : '' ?>
                                        <br>
                                    <?php endif ?>
                                    <br/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        BGP IPv4 MD5:
                                    </b>
                                </td>
                                <td>
                                    <code><?= $t->ee( $vli->ipv4bgpmd5secret ) ?></code>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Route Server Client:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->rsclient ? '<i class="fa fa-check"></i>' : '<i class="fa fa-cross"></i>'   ?>
                                </td>
                            </tr>
                            <?php if( $vli->rsclient ): ?>
                                <tr>
                                    <td>
                                        <b>
                                            &nbsp;&nbsp;&nbsp;&nbsp;IRRDB Filtering:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->irrdbfilter ? '<i class="fa fa-check"></i>' : '<i class="fa fa-cross"></i>' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            &nbsp;&nbsp;&nbsp;&nbsp;Allow More Specifics:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->rsmorespecifics ? '<i class="fa fa-check"></i>' : '<i class="fa fa-cross"></i>' ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td>
                                    <b>
                                        Notes :
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $vli->notes ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Created:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->created_at ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Updated:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->updated_at ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>
                                        Multicast:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->mcastenabled ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Disabled</span>' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Max BGP Prefixes:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->maxbgpprefix ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        IPv6:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->ipv6enabled ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Disabled</span>' ?>
                                    <?php if( $vli->ipv6enabled ): ?>
                                        <br><br>
                                        <?= $t->ee( $vli->ipv6address->address ) ?>
                                        <br>
                                        <?= $t->ee( $vli->ipv6hostname )?>
                                        <br>
                                        <?= $vli->ipv6canping ? '<span class="badge badge-success">Can Ping</span>' : '' ?>
                                        &nbsp;
                                        <?= $vli->ipv6monitorrcbgp ? '<span class="badge badge-success">Monitor RC BGP</span>' : '' ?>
                                        <br>
                                    <?php endif; ?>
                                    <br/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        BGP IPv6 MD5:
                                    </b>
                                </td>
                                <td>
                                    <code><?= $t->ee( $vli->ipv6bgpmd5secret )?></code>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>
                                        Busy Host:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->busyhost ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        AS112 Client:
                                    </b>
                                </td>
                                <td>
                                    <?= $vli->as112client ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>