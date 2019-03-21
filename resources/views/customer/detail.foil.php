<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    // convenience for IDE autocompletion
    /** @var Entities\Customer $c */
    $c = $t->c;
?>


<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( $c->isTypeAssociate() ? 'customer@associates' : 'customer@details' )?>"><?= $c->isTypeAssociate() ? 'Associate Members' : 'Customers' ?></a>
    <?php else: ?>
        Member Detail
    <?php endif; ?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        /
        <a href="<?= route( 'customer@overview', [ 'id' => $c->getId() ] ) ?>">
            <?= $t->ee( $c->getName() ) ?>
        </a>
    <?php endif; ?>
<?php $this->append() ?>



<?php $this->section('content') ?>

<div class="row">

    <div class="col-lg-12">


        <div class="bg-light shadow-sm p-4">
            <div class="row">
                <div class="<?= $t->logoManagementEnabled() && ( $logo = $c->getLogo( Entities\Logo::TYPE_WWW80 ) ) ? "col-md-9 col-lg-8" : "col-12" ?>">

                    <h3>
                        <?= $t->ee( $c->getFormattedName() ) ?>
                        <span class="text-sm"><?= $t->insert( 'customer/cust-type', [ 'cust' => $t->c ] ); ?></span>
                    </h3>

                    <p>
                        <a href="<?= $t->c->getCorpwww() ?>" target="_blank"><?= $t->nakedUrl( $t->c->getCorpwww() ) ?></a>

                        <?php if( !$c->isTypeAssociate() && strlen( $c->getPeeringmacro() ?? "" ) ): ?>
                            - <?=  $t->ee( $c->getPeeringmacro() ) ?>
                        <?php endif; ?>

                        - since <?= $c->getDatejoin()->format('Y') ?>
                    </p>

                    <p class="mt-4">

                        <?php if( $c->getInManrs() ): ?>
                            <a href="https://www.manrs.org/" target="_blank" class="hover:no-underline">
                                    <span class="border border-green p-1 rounded-full text-green text-uppercase text-xs mr-3" style="border-color: #38c172 !important;">
                                        MANRS
                                    </span>
                            </a>
                        <?php endif; ?>

                        <?php if( $c->getPeeringpolicy() != \Entities\Customer::PEERING_POLICY_OPEN ): ?>
                            <span class="border border-black p-1 rounded-full text-black text-uppercase text-xs mr-3" style="border-color: #000000 !important;">
                                <?= $c->getPeeringpolicy() ?>
                            </span>
                        <?php endif; ?>

                        <?php if( $c->isRouteServerClient() ): ?>
                            <span class="border border-green p-1 rounded-full text-green-dark text-uppercase text-xs mr-3" style="border-color: #1f9d55 !important;">
                        <?php else: ?>
                            <span class="border border-red   p-1 rounded-full text-red        text-uppercase text-xs mr-3" style="border-color: #e3342f !important;">
                        <?php endif; ?>
                            Route Server
                        </span>

                        <?php if( $c->isAS112Client() ): ?>
                            <span class="border border-green p-1 rounded-full text-green-dark text-uppercase text-xs" style="border-color: #1f9d55 !important;">
                        <?php else: ?>
                            <span class="border border-red   p-1 rounded-full text-red        text-uppercase text-xs" style="border-color: #e3342f !important;">
                        <?php endif; ?>
                            AS112
                        </span>

                    </p>

                </div>

                <?php if( $t->logoManagementEnabled() && ( $logo = $c->getLogo( Entities\Logo::TYPE_WWW80 ) ) ): ?>

                    <div class="col-md-3 col-lg-4 col-12 ixpm-im-mt-4 md:ixpm-im-mt-0 text-right">
                        <img class="img-fluid" style="max-height: 100px;" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>" />
                    </div>

                <?php endif; ?>
            </div>
        </div>


        <?php if( Auth::check() && !$t->c->isTypeAssociate() ): ?>

            <div class="row mt-4 mx-3">
                <div class="col-12 border border-grey p-3 text-black ">


                    <?php if( filter_var( $c->getPeeringemail(), FILTER_VALIDATE_EMAIL ) ): ?>
                        <div class="row">
                            <div class="col-12 col-md-3 text-center md:ixpm-im-text-right">
                                <span class="font-bold mr-3">Peering&nbsp;Email:</span>
                            </div>
                            <div class="col-12 col-md-9 text-center md:ixpm-im-text-left">
                                <a href="mailto:<?= $c->getPeeringemail() ?>"><?= $c->getPeeringemail() ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12 col-md-3 text-center md:ixpm-im-text-right">
                            <span class="font-bold  mr-3">NOC&nbsp;Contact:</span>
                        </div>
                        <div class="col-12 col-md-9 text-center md:ixpm-im-text-left">
                            <?php if( filter_var( $c->getNocemail(), FILTER_VALIDATE_EMAIL ) ): ?>
                                <a href="mailto:<?= $c->getNocemail() ?>"><?= $c->getNocemail() ?></a> /
                            <?php endif; ?>

                            <?= $t->ee( $c->getNocphone() ) ?> (<?= $t->ee( $c->getNochours() ) ?>)

                            <?php if( $c->getNoc24hphone() && $c->getNocphone() != $c->getNoc24hphone() ): ?>
                                / <?= $t->ee( $c->getNoc24hphone() ) ?> (24/7)
                            <?php endif; ?>

                            <?php if( filter_var( $c->getNocwww(), FILTER_VALIDATE_URL ) ): ?>
                                / <a href="<?= $c->getNocwww() ?>"><?= $c->getNocwww() ?></a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        <?php endif; ?>







        <?php $countVi = 1 ?>
        <?php foreach( $c->getVirtualInterfaces() as $vi ): ?>



            <div class="col-lg-12">
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

                <div class="col-lg-12 mt-4">

                    <?php if( $isLAG ): ?>
                        <h5>Port <?= $countPi ?> of <?= count( $vi->getPhysicalInterfaces() ) ?> in LAG</h5>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-6">
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
                        <div class="col-lg-6" >
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

                </div>
                <?php $countPi++ ?>

            <?php endforeach; /* foreach( $vi->getPhysicalInterfaces() as $pi ) */ ?>


                <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>

                    <?php if( $vli->getVlan()->getPrivate() ): ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <div class="col-lg-12 mt-4" style="text-indent: 20px ">

                        <h4><?= $t->ee( $vli->getVlan()->getName() ) ?>:</h4>

                        <div class="row mb-4">
                            <div class="col-lg-6">

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
                            <div class="col-lg-6">

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

                    </div>

                <?php endforeach; /* foreach( $vi->getVlanInterfaces() as $vli ) */ ?>



            <?php $countVi++ ?>
        <?php endforeach; ?>

    </div>

</div>



<?php $this->append() ?>



