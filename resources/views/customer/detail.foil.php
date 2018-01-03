<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( 'customer@list' )?>">Customers</a>
    <?php else: ?>
        Customer Detail
    <?php endif; ?>
<?php $this->append() ?>

<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <li> <?= $t->ee( $t->cust->getName() ) ?></li>
    <?php $this->append() ?>
<?php endif; ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-xs-6">
            <table class="table_view_info">
                <tr>
                    <td>
                        <b>
                            Member Type:
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $t->cust->resolveType() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            AS Number:
                        </b>
                    </td>
                    <td>
                        <?=  $t->asNumber( $t->cust->getAutsys() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Peering Policy:
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $t->cust->getPeeringPolicy() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Peering Email:
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $t->cust->getPeeringemail() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            NOC Phone:
                        </b>
                    </td>
                    <td>
                        <?= $t->cust->getNocphone() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Dedicated NOC Web:
                        </b>
                    </td>
                    <td>
                        <a href="<?= $t->ee( $t->cust->getNocwww() ) ?>" target="_blank">
                            <?= $t->ee( $t->cust->getNocwww() ) ?>
                        </a>

                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            NOC Hours:
                        </b>
                    </td>
                    <td>
                        <?= $t->cust->getNochours() ?>
                    </td>
                </tr>

            </table>
        </div>
        <div class="col-xs-6">
            <table class="table_view_info">
                <tr>
                    <td>
                        <b>
                            Member Status:
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $t->cust->resolveStatus() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Peering Macro:
                        </b>
                    </td>
                    <td>
                        <?=  $t->ee( $t->cust->getPeeringmacro() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Corporate Web:
                        </b>
                    </td>
                    <td>
                        <a href="<?= $t->ee( $t->cust->getCorpwww() ) ?>" target="_blank">
                            <?= $t->ee( $t->cust->getCorpwww() ) ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            NOC Email
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $t->cust->getNocemail() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            NOC 24 Hour Phone
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $t->cust->getNoc24hphone() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            NOC Fax
                        </b>
                    </td>
                    <td>
                        <?= $t->cust->getNocfax() ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?php $countVi = 1 ?>
    <?php foreach( $t->cust->getVirtualInterfaces() as $vi ): ?>

        <div class="row col-md-12" style="margin-bottom: 20px">
            <hr>
            <div>
                <h3>
                    Connection <?= $countVi ?>
                    <?php $vlanints =$vi->getVlanInterfaces() ?>
                    <?php $vlanint = $vlanints[ 0 ]  ?>
                    <?php if( $vlanint ): ?>
                        <small>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Infrastructure #<?= $vlanint->getVlan()->getNumber() % 10 == 0 ? 1 : 2 ?>
                            <?php if( count( $vi->getPhysicalInterfaces() ) > 1 ): ?>
                                <?php $isLAG = 1 ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LAG Port
                            <?php else: ?>
                                <?php $isLAG = 0 ?>
                            <?php endif; ?>
                        </small>
                    <?php endif; ?>
                </h3>
            </div>

            <?php $countPi = 1 ?>
            <?php foreach( $vi->getPhysicalInterfaces() as $pi ): ?>
                <div class="col-md-12">
                    <?php if( $isLAG ): ?>
                        <h5>Port <?= $countPi ?> of <?= count( $vi->getPhysicalInterfaces() ) ?> in LAG</h5>
                    <?php endif; ?>
                    <div class="col-xs-6">
                        <table>
                            <tr>
                                <td>
                                    <b>
                                        Switch:
                                    </b>
                                </td>

                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Speed:
                                    </b>
                                </td>
                                <td>
                                    <?= $pi->resolveSpeed() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Location:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-xs-6" >
                        <table class="">
                            <tr>
                                <td>
                                    <b>Switch Port:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getName() ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Duplex:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getDuplex() ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Colo Cabinet ID:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getName() ) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php $countPi++ ?>
            <?php endforeach; ?>


            <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>
                <?php $vlanid =$vli->getVlan()->getId() ?>

                <?php if( !$vli->getVlan()->getPrivate() ): ?>
                    <div class="col-md-12" style="margin-bottom: 20px; text-indent: 20px ">

                        <h4><?= $t->ee( $vli->getVlan()->getName() ) ?>:</h4>

                        <div class="col-xs-6" style="">
                            <table class="">
                                <tr>
                                    <td>
                                        <b>
                                            IPv6 Address:
                                        </b>
                                    </td>
                                    <td>
                                        <?php if( $vli->getIpv6enabled() and $vli->getIpv6address() ): ?>
                                            <?= $vli->getIPv6Address()->getAddress() ?> <?php if( isset( $netinfo[ $vlanid ][ 6 ][ 'masklen' ] ) ) : ?> /<?= $netinfo[ $vlanid ][ 6 ][ "masklen" ] ?> <?php endif;?>
                                        <?php else: ?>
                                            IPv6 not enabled.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Multicast Enabled:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->getMcastenabled() ? "Yes" : "No" ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Route Server Client:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->getRsclient() ? "Yes" : "No" ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-xs-6">
                            <table>
                                <tr>
                                    <td>
                                        <b>IPv4 Address:</b>
                                    </td>
                                    <td>
                                        <?php if( $vli->getIpv4enabled() and $vli->getIpv4address() ): ?>
                                            <?= $vli->getIPv4Address()->getAddress() ?> <?php if( isset( $netinfo[ $vlanid ][ 4 ][ 'masklen' ] ) ) : ?> /<?= $netinfo[ $vlanid ][ 4 ][ "masklen" ] ?> <?php endif;?>
                                        <?php else: ?>
                                            IPv4 not enabled.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Max Prefixes:
                                        </b>
                                    </td>
                                    <td>
                                        global: <?= $t->cust->getMaxprefixes() ?>, per-interface: <?= $vli->getMaxbgpprefix() ?>
                                    </td>
                                </tr>

                                <?php if( $t->as112UiActive ): ?>
                                    <tr>
                                        <td>
                                            <b>
                                                AS112 Client:
                                            </b>
                                        </td>
                                        <td>
                                            <?= $vli->getAs112client() ? "Yes" : "No" ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                         </div>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>

        </div>

        <?php $countVi++ ?>
    <?php endforeach; ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>
