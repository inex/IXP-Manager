<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    // convenience for IDE autocompletion
    /** @var Entities\Customer $c */
    $c = $t->c;

    // list of route server asns
    $rsasns = d2r( 'Router' )->getAllPeeringASNs( \Entities\Router::TYPE_ROUTE_SERVER );
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

                    <p class="mt-2">
                        <a href="<?= $t->c->getCorpwww() ?>" target="_blank"><?= $t->nakedUrl( $t->c->getCorpwww() ) ?></a>

                        - joined <?= $c->getDatejoin()->format('Y') ?>
                    </p>

                    <?php if( !$t->c->isTypeAssociate() ): ?>
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

                        </p>
                    <?php endif; ?>
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

                    <?php if( !$c->isTypeAssociate() && strlen( $c->getPeeringmacro() ?? "" ) ): ?>
                        <div class="row">
                            <div class="col-12 col-md-3 text-center md:ixpm-im-text-right">
                                <span class="font-bold mr-3">Peering&nbsp;Macro:</span>
                            </div>
                            <div class="col-12 col-md-9 text-center md:ixpm-im-text-left">
                                <?=  $t->ee( $c->getPeeringmacro() ) ?>
                            </div>
                        </div>
                    <?php endif; ?>


                </div>
            </div>
        <?php endif; ?>






        <div class="row mt-4">

            <?php $countVi = 1 ?>
            <?php foreach( $c->getVirtualInterfaces() as $vi ):

                if( !$vi->isConnected() || !$vi->isPeeringPort() ) {
                    continue;
                }
            ?>

            <div class="col-12 col-md-6 col-xl-4 mt-4">

                <div class="max-w-sm rounded overflow-hidden shadow-lg">
                    <div class="px-6 py-4">

                        <div class="font-bold text-xl mb-2">
                            <?= $vi->getInfrastructure() ? $vi->getInfrastructure()->getName() : '<em>Unknwon Infrastructure</em>' ?>

                            <span class="block float-right ml-4 text-lg font-semibold">
                                <?= $t->scaleBits( $vi->speed() * 1000 * 1000, 0 ) ?>
                            </span>

                        </div>

                        <?php if( $vi->getPhysicalInterfaces() ):
                            $pi = $vi->getPhysicalInterfaces()[0]; ?>

                            <p class="text-grey-dark text-sm">
                                Location
                            </p>
                            <p class="text-grey-darker text-base">
                                <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                            </p>
                        <?php endif; ?>

                        <br>

                        <?php if( $vi->getVlanInterfaces() ): ?>

                            <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>

                                <?php if( $vli->getVlan()->getPrivate() ): ?>
                                    <?php continue; ?>
                                <?php endif; ?>

                                <p class="text-grey-dark text-sm">
                                    <?= $vi->numberOfPublicVlans() > 1 ? $t->ee( $vli->getVlan()->getName() ) : 'IP Addresses' ?>
                                </p>

                                <p class="text-grey-darker text-base">

                                    <?php if( $vli->getIpv6enabled() and $vli->getIpv6address() ): ?>
                                        <?= $vli->getIPv6Address()->getAddress() ?>
                                        <br>
                                    <?php endif; ?>
                                    <?php if( $vli->getIpv4enabled() and $vli->getIpv4address() ): ?>
                                        <?= $vli->getIPv4Address()->getAddress() ?>
                                        <br>
                                    <?php else: ?>
                                        &nbsp;<br>
                                    <?php endif; ?>

                                    <?php if( !$vli->getIpv6enabled() ): ?>
                                        &nbsp;<br>
                                    <?php endif; ?>

                                </p>
                                <br>

                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                    <div class="px-6 pb-4">

                        <?php if( $vi->getVlanInterfaces() ): ?>

                            <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>

                                <?php if( $vli->getVlan()->getPrivate() ): ?>
                                    <?php continue; ?>
                                <?php endif; ?>


                                <?php if( !in_array( $t->c->getAutsys(), $rsasns ) ): ?>
                                    <?php if( $vli->getRsclient() ): ?>
                                        <span class="inline-block border border-green p-1 rounded-full text-green-dark  font-semibold text-uppercase text-sm px-3 py-1 mr-2" style="border-color: #1f9d55 !important;">
                                    <?php else: ?>
                                        <span class="inline-block border border-red   p-1 rounded-full text-red-lighter font-semibold text-uppercase text-sm px-3 py-1 mr-2" style="border-color: #f9acaa !important;">
                                    <?php endif; ?>
                                        Route Server
                                    </span>
                                <?php endif; ?>

                                <?php if( $t->c->getAutsys() !== 112 ): ?>
                                    <?php if( $vli->getAs112client() ): ?>
                                        <span class="inline-block border border-green p-1 rounded-full text-green-dark  font-semibold text-uppercase text-sm px-3 py-1 mr-2 my-2" style="border-color: #1f9d55 !important;">
                                    <?php else: ?>
                                        <span class="inline-block border border-red   p-1 rounded-full text-red-lighter font-semibold text-uppercase text-sm px-3 py-1 mr-2 my-2" style="border-color: #f9acaa !important;">
                                    <?php endif; ?>
                                        AS112
                                    </span>
                                <?php endif; ?>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>
                </div>

            </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>



<?php $this->append() ?>



