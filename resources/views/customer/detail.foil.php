<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    // convenience for IDE autocompletion
    /** @var Entities\Customer $c */
    $c = $t->c;
?>


<?php $this->section( 'title' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( $c->isTypeAssociate() ? 'customer@associates' : 'customer@details' )?>"><?= $c->isTypeAssociate() ? 'Associate Members' : 'Customers' ?></a>
    <?php else: ?>
        Customer Detail
    <?php endif; ?>
<?php $this->append() ?>


<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <li>
            <a href="<?= route( 'customer@overview', [ 'id' => $c->getId() ] ) ?>">
                <?= $t->ee( $c->getName() ) ?>
            </a>
        </li>
    <?php $this->append() ?>
<?php endif; ?>


<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">


        <div class="well">
            <div class="row">
                <h3 class="col-sm-9">
                    <?= $t->ee( $c->getFormattedName() ) ?>
                    <?= $t->insert( 'customer/cust-type', [ 'cust' => $t->c ] ); ?>
                </h3>

                <?php if( $t->logoManagementEnabled() && ( $logo = $c->getLogo( Entities\Logo::TYPE_WWW80 ) ) ): ?>

                    <div class="col-sm-3">
                        <img class="www80-padding img-responsive" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                    </div>

                <?php endif; ?>
            </div>
        </div>




        <div class="col-md-6">
            <table class="table_view_info">
                <tr>
                    <td>
                        <b>
                            Member Type:
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $c->resolveType() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            AS Number:
                        </b>
                    </td>
                    <td>
                        <?=  $t->asNumber( $c->getAutsys() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Peering Policy:
                        </b>
                    </td>
                    <td>
                        <?= ucfirst( $t->ee( $c->getPeeringPolicy() ) ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Join Date:
                        </b>
                    </td>
                    <td>
                        <?= $c->getDatejoin()->format('Y-m-d') ?>
                    </td>
                </tr>

                <?php if( Auth::check() ): ?>
                    <tr>
                        <td>
                            <b>
                                Peering Email:
                            </b>
                        </td>
                        <td>
                            <?php if( filter_var( $c->getPeeringemail(), FILTER_VALIDATE_EMAIL ) ): ?>
                                <a href="mailto:<?= $c->getPeeringemail() ?>"><?= $c->getPeeringemail() ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                NOC Phone:
                            </b>
                        </td>
                        <td>
                            <?= $t->ee( $c->getNocphone() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                NOC Hours:
                            </b>
                        </td>
                        <td>
                            <?= $t->ee( $c->getNochours() ) ?>
                        </td>
                    </tr>
                <?php endif; ?>

            </table>
        </div>

        <div class="col-md-6">
            <table class="table_view_info">
                <tr>
                    <td>
                        <b>
                            Member Status:
                        </b>
                    </td>
                    <td>
                        <?= $t->ee( $c->resolveStatus() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Website:
                        </b>
                    </td>
                    <td>
                        <?php if( filter_var( $c->getCorpwww(), FILTER_VALIDATE_URL ) ): ?>
                            <a href="<?= $c->getCorpwww() ?>"><?= $c->getCorpwww() ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Peering Macro:
                        </b>
                    </td>
                    <td>
                        <?=  $t->ee( $c->getPeeringmacro() ) ?>
                    </td>
                </tr>

                <?php if( Auth::check() ): ?>

                    <tr>
                        <td>
                            <b>
                                NOC Email
                            </b>
                        </td>
                        <td>
                            <?php if( filter_var( $c->getNocemail(), FILTER_VALIDATE_EMAIL ) ): ?>
                                <a href="mailto:<?= $c->getNocemail() ?>"><?= $c->getNocemail() ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                NOC 24 Hour Phone
                            </b>
                        </td>
                        <td>
                            <?= $t->ee( $c->getNoc24hphone() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                NOC Website:
                            </b>
                        </td>
                        <td>
                            <?php if( filter_var( $c->getNocwww(), FILTER_VALIDATE_URL ) ): ?>
                                <a href="<?= $c->getNocwww() ?>"><?= $c->getNocwww() ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>

            </table>
        </div>



        <?php $countVi = 1 ?>
        <?php foreach( $c->getVirtualInterfaces() as $vi ): ?>



            <div class="col-md-12">
                <hr>

                <h3>
                    Connection <?= $countVi ?>
                    <?php
                    $countPi  = 1;
                    $isLAG = count( $vi->getPhysicalInterfaces() ) > 1;
                    ?>

                    <small>
                        <?= $vi->getInfrastructure() ? $vi->getInfrastructure()->getName() : '<em>Unknwon Infrastructure</em>' ?>
                        <?= $isLAG ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LAG Port' : '' ?>
                    </small>
                </h3>
            </div>

            <?php foreach( $vi->getPhysicalInterfaces() as $pi ): ?>

                <div class="col-md-12">

                    <?php if( $isLAG ): ?>
                        <h5>Port <?= $countPi ?> of <?= count( $vi->getPhysicalInterfaces() ) ?> in LAG</h5>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <table>
                            <tr>
                                <td>
                                    <b>
                                        Location:&nbsp;&nbsp;
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
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
                        </table>
                    </div>
                    <div class="col-md-6" >
                        <table>
                            <tr>
                                <td>
                                    <b>
                                        Switch:&nbsp;&nbsp;
                                    </b>
                                </td>

                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Port:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $pi->getSwitchPort()->getName() ) ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
                <?php $countPi++ ?>

            <?php endforeach; /* foreach( $vi->getPhysicalInterfaces() as $pi ) */ ?>


                <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>

                    <?php if( $vli->getVlan()->getPrivate() ): ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <div class="col-md-12" style="margin-bottom: 20px; text-indent: 20px ">

                        <br>
                        <h4><?= $t->ee( $vli->getVlan()->getName() ) ?>:</h4>

                        <div class="col-md-6" style="">

                            <table>
                                <tr>
                                    <td>
                                        <b>
                                            IPv6 Address:
                                        </b>
                                    </td>
                                    <td>
                                        <?php if( $vli->getIpv6enabled() and $vli->getIpv6address() ): ?>
                                            <?= $vli->getIPv6Address()->getAddress() ?>
                                            /<?= isset( $t->netinfo[ $vli->getVlan()->getId() ][ 6 ][ 'masklen' ] ) ? $t->netinfo[ $vli->getVlan()->getId() ][ 6 ][ "masklen" ] : '??' ?>
                                        <?php else: ?>
                                            IPv6 not enabled.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Route Server Client:&nbsp;&nbsp;
                                        </b>
                                    </td>
                                    <td>
                                        <?= $vli->getRsclient() ? "Yes" : "No" ?>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <div class="col-md-6">

                            <table>
                                <tr>
                                    <td>
                                        <b>IPv4 Address:&nbsp;&nbsp;</b>
                                    </td>
                                    <td>
                                        <?php if( $vli->getIpv4enabled() and $vli->getIpv4address() ): ?>
                                            <?= $vli->getIPv4Address()->getAddress() ?>
                                            /<?= isset( $t->netinfo[ $vli->getVlan()->getId() ][ 4 ][ 'masklen' ] ) ? $t->netinfo[ $vli->getVlan()->getId() ][ 4 ][ "masklen" ] : '??' ?>
                                        <?php else: ?>
                                            IPv4 not enabled.
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <?php if( $t->as112UiActive() ): ?>
                                    <tr>
                                        <td>
                                            <b>
                                                AS112 Client:&nbsp;&nbsp;
                                            </b>
                                        </td>
                                        <td>
                                            <?= $vli->getAs112client() ? "Yes" : "No" ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php if( Auth::check() ): ?>
                                    <tr>
                                        <td>
                                            <b>
                                                Max Prefixes:&nbsp;&nbsp;
                                            </b>
                                        </td>
                                        <td>
                                            global: <?= $c->getMaxprefixes() ?>, per-interface: <?= $vli->getMaxbgpprefix() ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            </table>

                        </div>
                    </div>

                <?php endforeach; /* foreach( $vi->getVlanInterfaces() as $vli ) */ ?>



            <?php $countVi++ ?>
        <?php endforeach; ?>

    </div>

</div>



<?php $this->append() ?>



